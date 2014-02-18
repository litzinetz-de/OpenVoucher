<?php
require_once('../includes/config.php');
require_once('../classes/systemmanager.php');

class vouchermanager {
	private $settings;
	private $mysqlconn;
	private $sysconfig;
	
	function __construct()
	{
		// Configs are read from config.php - No need to change anything here !!
		$this->settings['mysql']['host']=MYSQL_HOST;
		$this->settings['mysql']['user']=MYSQL_USER;
		$this->settings['mysql']['pwd']=MYSQL_PWD;
		$this->settings['mysql']['db']=MYSQL_DB;
		
		$this->settings['system']['iptables']=SYSTEM_IPTABLES;
		$this->settings['system']['arp']=SYSTEM_ARP;
		$this->settings['system']['tmpdir']=SYSTEM_TMPDIR;

		$this->settings['system']['authentication']=SYSTEM_AUTHENTICATION;
		
		$this->settings['interfaces']['internal']=INTERFACES_INTERNAL;
		$this->settings['interfaces']['internal_ip']=INTERFACES_INTERNAL_IP;
		$this->settings['interfaces']['external']=INTERFACES_EXTERNAL;
		
		$this->settings['system']['demo']=OV_DEMO;
		
		$this->mysqlconn=mysql_connect($this->settings['mysql']['host'],$this->settings['mysql']['user'],$this->settings['mysql']['pwd']);
		mysql_select_db($this->settings['mysql']['db'],$this->mysqlconn);
		
		$this->sysconfig = new systemmanager();
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
	
	private function GetNewVerificationKey()
	{
		return rand(111111,999999);
	}
	
	public function GetClientMAC($ipAddress='')
	{
		if($ipAddress=='')
		{
			$ipAddress=$_SERVER['REMOTE_ADDR'];
		}
		$macAddr='';

		$arp=shell_exec($this->settings['system']['arp'].' -a '.$ipAddress);
		
		$x=strpos($arp,':');
		$begin=$x-2;
		$macAddr=substr($arp,$begin,17);
		
		return $macAddr;
	}

	public function GetClientIP()
	{
		return $_SERVER['REMOTE_ADDR'];
	}
	
	public function BuildIPTables()
	{
		if(!$this->settings['system']['demo'])
		{
			$ipt='#!/bin/sh'."\n\n".

			'IPTABLES=\'sudo '.$this->settings['system']['iptables'].'\''."\n".
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
	}
	
	public function MakeVoucher($devicecount,$valid_until,$comment)
	{
		$vid=$this->GetNewVoucherID();
		
		if($this->sysconfig->GetSetting('use_verification')=='y')
		{
			$verification_key=$this->GetNewVerificationKey();
		} else {
			$verification_key='';
		}
		
		if(mysql_query('INSERT INTO vouchers (voucher_id,dev_count,valid_until,verification_key,comment) VALUES ("'.$vid.'",'.$devicecount.','.$valid_until.',"'.$verification_key.'","'.$comment.'")',$this->mysqlconn))
		{
			return $vid;
		} else {
			return 0;
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
			// Drop found vouchers but do not rebuild iptables for each one. this would waste resources
			$this->DropVoucher($row['voucher_id'],false);
		}
		// After deletion, rebuild iptables once
		$this->BuildIPTables();
	}
	
	// Find out the authentication method that should be used for the client
	public function GetAuthMethod()
	{
		return $this->settings['system']['authentication'];
	}
	
	// Check if a verification key is correct
	public function VerifyVoucherKey($vid,$verification_key)
	{
		$res=mysql_query('SELECT verification_key FROM vouchers WHERE voucher_id="'.$vid.'"',$this->mysqlconn);
		$row=mysql_fetch_array($res);
		
		if($this->sysconfig->GetSetting('use_verification')=='y')
		{
			if($verification_key != $row['verification_key'])
			{
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}
	
	public function AuthDevice($vid,$verification_key,$type,$addr)
	{
		// Voucher valid?
		$res=mysql_query('SELECT dev_count,valid_until FROM vouchers WHERE voucher_id="'.$vid.'"',$this->mysqlconn);
		$row=mysql_fetch_array($res);
		
		if($this->sysconfig->GetSetting('use_verification')=='y')
		{
			if(!$this->VerifyVoucherKey($vid,$verification_key))
			{
				return 'verification-failed';
			}
		}
		
		if(trim($row['valid_until'])=='' || $row['valid_until']<=time()) // Voucher not found or exceeded
		{
			return 'not-found-exceeded';
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
			$i++;
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
	
	// Is the requesting client authenticated?
	public function ClientAuthenticated()
	{
		if($this->settings['system']['authentication']=='mac-only')
		{
			$addr=$this->GetClientMAC();
			$type='mac';
		}
		elseif($this->settings['system']['authentication']=='ipv4-only')
		{
			$addr=$this->GetClientIP();
			$type='ipv4';
		}
		
		$res=mysql_query('SELECT voucher_id FROM devices WHERE type="'.$type.'" AND addr="'.$addr.'"',$this->mysqlconn);
		$row=mysql_fetch_array($res);
		if(trim($row['voucher_id'])!='')
		{
			return array($type,$addr,$row['voucher_id']);
		} else {
			return 'noauth';
		}
	}
	
	// Get some voucher information from db
	public function GetVoucherInfo($vid)
	{
		$res=mysql_query('SELECT dev_count,valid_until FROM vouchers WHERE voucher_id="'.$vid.'"',$this->mysqlconn);
		$row=mysql_fetch_array($res);
		
		$res=mysql_query('SELECT COUNT(*) AS cnt FROM devices WHERE voucher_id="'.$vid.'"',$this->mysqlconn);
		$cnt=mysql_fetch_array($res);
		
		$row['remain']=$row['dev_count']-$cnt['cnt']; // Calculate how many devices are left to register
		return $row;
	}
	
	// Generate a voucher list
	public function GetVoucherList($searchstring='')
	{
		$dataset=array();
		$res=mysql_query('SELECT voucher_id,verification_key,dev_count,valid_until,comment FROM vouchers '.$searchstring);
		while($row=mysql_fetch_array($res))
		{
			array_push($dataset,$row);
		}
		return $dataset;
	}
}
?>
