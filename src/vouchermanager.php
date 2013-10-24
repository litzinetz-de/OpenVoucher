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

		'IPT=\''.$this->settings['system']['iptables'].'\''."\n".
		'LAN='.$this->settings['interfaces']['internal'].''."\n".
		'INET='.$this->settings['interfaces']['external'].''."\n\n".


		'# Flush (delete all rules in chain)'."\n".
		'$IPT -F'."\n".
		'$IPT -t nat -F'."\n".
		'$IPT -t mangle -F'."\n\n".

		'# Delete chains'."\n".
		'$IPT -X'."\n".
		'$IPT -t nat -X'."\n".
		'$IPT -t mangle -X'."\n\n".

		'# Default policies - Allow traffic to and from server, drop forwarding by default (unless a client has been authenticated)'."\n".
		'$IPT -P INPUT ACCEPT'."\n".
		'$IPT -P FORWARD DROP'."\n".
		'$IPT -P OUTPUT ACCEPT'."\n\n".

		'# Allow established'."\n".
		'$IPT -A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT'."\n".
		'$IPT -A FORWARD -m state --state ESTABLISHED,RELATED -j ACCEPT'."\n\n".

		'# Drop invalid packets'."\n".
		'$IPT -N INVALID'."\n\n".

		'$IPT -A INVALID -m limit --limit 3/s -j LOG --log-prefix "INVALID "'."\n".
		'$IPT -A INVALID -j DROP'."\n\n".

		'$IPT -A INPUT -m state --state INVALID -j INVALID'."\n".
		'$IPT -A FORWARD -m state --state INVALID -j INVALID'."\n".
		'$IPT -A OUTPUT -m state --state INVALID -j INVALID'."\n\n".


		'# Activate masquerading'."\n".
		'$IPT -t nat -A POSTROUTING -o $INET -j MASQUERADE'."\n\n".

		'# List of allowed MAC-addresses';
		
		$res=mysql_query('SELECT devices.addr FROM devices INNER JOIN vouchers ON devices.voucher_id=vouchers.voucher_id WHERE type="mac" AND valid_until>'.time(),$this->mysqlconn);
		while($row=mysql_fetch_array($res))
		{
			$ipt=$ipt.'$IPT -A FORWARD -i $LAN -o $INET -m mac --mac-source '.$row['addr'].' -j ACCEPT'."\n";
		}
		file_put_contents($this->settings['system']['tmpdir'].'iptables-autogen.sh',$ipt);
		shell_exec('chmod u+x '.$this->settings['system']['tmpdir'].'iptables-autogen.sh');
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
}
?>