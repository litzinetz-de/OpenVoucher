<?php
class vouchermanager {
	private $settings;
	private $mysqlconn;
	
	function __construct()
	{
		$this->settings=parse_ini_file('.settings.ini',TRUE);
		$this->mysqlconn=mysql_connect($this->settings['mysql']['host'],$this->settings['mysql']['user'],$this->settings['mysql']['pwd']);
		mysql_select_db($this->settings['mysql']['db'],$this->mysqlconn);
	}
	
	private function VoucherIDExists($vid)
	{
		$res=mysql_query('SELECT COUNT(*) AS cnt FROM vouchers WHERE voucher_id="'.$vid.'"',$this->mysqlconn);
		$row=mysql_fetch_array($res);
		if($row['cnt']>0)
		{
			return true;
		} else {
			return false;
		}
	}
	
	private function GetNewVoucherID()
	{
		do
		{
			$vid=date('Y-m-d',time()).'-'.rand(111111,999999);
		} while($this->VoucherIDExists($vid));
		return $vid;
	}
	
	public function BuildIPTables()
	{
		$ipt='#!/bin/sh'."\n\n".

		'IPTABLES=\'sudo /sbin/iptables\''."\n".
		'PORTAL_INT='.$this->settings['interfaces']['internal'].''."\n".
		'OUTPUT_INT='.$this->settings['interfaces']['external'].''."\n\n".


		'# Flush (delete all rules in chain)'."\n".
		'$IPTABLES -F'."\n".
		'$IPTABLES -X'."\n".
		'$IPTABLES -t nat -F'."\n".
		'$IPTABLES -t nat -X'."\n".
		'$IPTABLES -t mangle -F'."\n".
		'$IPTABLES -t mangle -X'."\n".
		'$IPTABLES -P INPUT ACCEPT'."\n".
		'$IPTABLES -P FORWARD ACCEPT'."\n".
		'$IPTABLES -P OUTPUT ACCEPT'."\n\n".

		'# Chain for captive portal'."\n".
		'$IPTABLES -N captivePortal -t mangle'."\n".
		'$IPTABLES -t mangle -A PREROUTING -j captivePortal'."\n\n".

		'# List of allowed MAC addresses'."\n";
		
		$res=mysql_query('SELECT devices.addr FROM devices INNER JOIN vouchers ON devices.voucher_id=vouchers.voucher_id WHERE type="mac" AND valid_until>'.time(),$this->mysqlconn);
		while($row=mysql_fetch_array($res))
		{
			$ipt=$ipt.'$IPTABLES -t mangle -A captivePortal -m mac --mac-source '.$row['addr'].' -j RETURN'."\n";
		}
		
		$ipt=$ipt."\n".
		'# List of allowed IP addresses'."\n";
		
		$res=mysql_query('SELECT devices.addr FROM devices INNER JOIN vouchers ON devices.voucher_id=vouchers.voucher_id WHERE type="ipv4" AND valid_until>'.time(),$this->mysqlconn);
		while($row=mysql_fetch_array($res))
		{
			$ipt=$ipt.'$IPTABLES -t mangle -A captivePortal -s '.$row['addr'].' -j RETURN'."\n";
		}
		
		$ipt=$ipt."\n".
		'# DNS is allowed for all'."\n".
		'$IPTABLES -t mangle -A captivePortal -i $PORTAL_INT -p udp --dport 53 -j RETURN'."\n\n".
		'# Mark packets that are not allowed till here'."\n".
		'$IPTABLES -t mangle -A captivePortal -i $PORTAL_INT -j MARK --set-mark 99'."\n\n".
		'# Redirect unauthenticated clients to captive portal'."\n".
		'$IPTABLES -t nat -A PREROUTING -m mark --mark 99 -i $PORTAL_INT -p tcp --dport 80 -j DNAT --to-destination '.$this->settings['interfaces']['internal_ip']."\n\n".
		
		'# drop all marked with 99'."\n".
		'$IPTABLES -t filter -A FORWARD -m mark --mark 99 -j DROP'."\n\n".
		'# masquerading NAT'."\n".
		'$IPTABLES -t nat -A POSTROUTING -o $OUTPUT_INT -j MASQUERADE';
		
		file_put_contents($this->settings['system']['tmpdir'].'iptables-autogen.sh',$ipt);
		shell_exec('chmod ugo+x '.$this->settings['system']['tmpdir'].'iptables-autogen.sh');
		$runcmd=$this->settings['system']['tmpdir'].'iptables-autogen.sh';
		shell_exec($runcmd);
	}
	
	public function MakeVoucher($devicecount,$valid_until,$comment)
	{
		$vid=$this->GetNewVoucherID();
		if(mysql_query('INSERT INTO vouchers VALUES ("'.$vid.'",'.$devicecount.','.$valid_until.',"'.$comment.'")',$this->mysqlconn))
		{
			return $vid;
		} else {
			return false;
		}
	}
	
	public function DropVoucher($vid,$rebuild=true)
	{
		// Delete voucher from db
		mysql_query('DELETE FROM vouchers WHERE voucher_id="'.$vid.'"',$this->mysqlconn);
		mysql_query('DELETE FROM devices WHERE voucher_id="'.$vid.'"',$this->mysqlconn);
		if(mysql_affected_rows($this->mysqlconn)>0) // if a voucher has been deleted, rebuild iptables
		{
			if($rebuild)
			{
				$this->BuildIPTables(); // only rebuild tables if it is necessary
			}
		}
	}
	
	public function DropOldVouchers()
	{
		// Look for expired vouchers in db
		$res=mysql_query('SELECT voucher_id FROM vouchers WHERE valid_until<'.time(),$this->mysqlconn);
		while($row=mysql_fetch_array($res))
		{
			// Drop found vouchers but to not rebuild iptables for each one. this would waste resources
			$this->DropVoucher($row['voucher_id'],false);
		}
		// After deletion, rebuild iptables once
		$this->BuildIPTables();
	}
	
	public function AuthDevice($vid,$type,$addr)
	{
		// Voucher valid?
		$res=mysql_query('SELECT dev_count,valid_until FROM vouchers WHERE voucher_id="'.$vid.'"',$this->mysqlconn);
		$row=mysql_fetch_array($res);
		if(trim($row['valid_until'])=='' || $row['valid_until']<=time()) // Voucher not found or exceeded
		{
			return 'not-found-exceeded ';
		} else {
			$res=mysql_query('SELECT COUNT(*) AS cnt FROM devices WHERE voucher_id="'.$vid.'"',$this->mysqlconn);
			$row_dev=mysql_fetch_array($res);
			if(trim($row_dev['cnt'])=='' || $row_dev['cnt']>=$row['dev_count']) // Maximum number of devices reached or exceeded, or not able to get number of devices (database failure?)
			{
				return 'maxnumber-reached';
			} else {
				if(mysql_query('INSERT INTO devices VALUES ("'.$type.'","'.$addr.'","'.$vid.'")',$this->mysqlconn))
				{
					$this->BuildIPTables();
					return 'ok'; // Device has been authenticated
				} else {
					return 'db-error'; // Database error
				}
			}
		}
	}
	
	public function DropDevice($type,$addr)
	{
		if($type=='mac')
		{
			mysql_query('DELETE FROM devices WHERE type="mac" AND addr="'.$addr.'"',$this->mysqlconn);
			shell_exec('sudo '.$this->settings['system']['iptables'].' -t mangle -D captivePortal -m mac --mac-source '.$addr.' -j RETURN');
		}
		if($type=='ipv4')
		{
			mysql_query('DELETE FROM devices WHERE type="ipv4" AND addr="'.$addr.'"',$this->mysqlconn);
			shell_exec('sudo '.$this->settings['system']['iptables'].' -t mangle -D captivePortal -s '.$addr.' -j RETURN');
		}
	}
	
	// Return a list of all devices that are registered to a specific voucher
	public function GetDeviceList($vid)
	{
		$res=mysql_query('SELECT type,addr FROM devices WHERE voucher_id="'.$vid.'"',$this->mysqlconn);
		$i=0;
		$devices=array();
		while($row=mysql_fetch_array($res))
		{
			$devices[$i]=$row;
		}
		return $devices;
	}

	// Check if a specific device is registered to a specific voucher
	public function DeviceInVoucher($vid,$type,$addr)
	{
		$res=mysql_query('SELECT voucher_id FROM devices WHERE type="'.$type.'" AND addr="'.$addr.'"',$this->mysqlconn);
		$row=mysql_fetch_array($res);
		if($row['voucher_id']==$vid)
		{
			return true;
		} else {
			return false;
		}
	}
}
?>
