<?php
require_once('../includes/config.php');

class systemmanager
{
	private $mysqlconn;
	private $defaults; // This array contains the default values of nothing is configured in mysql database
	
	function __construct()
	{
		$this->defaults['vouchertext1']='Please enter the code';
		$this->defaults['vouchertext2']='to get internet access';
		$this->defaults['pre-form-text']='Please add your voucher code in the form below to get internet access.';
		$this->defaults['post-form-text']='Feel free to contact an administrator if you have any problems.';
		$this->defaults['use_verification']='n';
		
		$this->mysqlconn=mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PWD);
		mysql_select_db(MYSQL_DB,$this->mysqlconn);
	}
	
	public function GetSetting($setting)
	{
		$res=mysql_query('SELECT s_value FROM settings WHERE setting="'.$setting.'"',$this->mysqlconn);
		$row=mysql_fetch_array($res);
		$return_value=$row['s_value'];
		
		if($return_value=='')
		{
			return $this->defaults[$setting];
		} else {
			return $return_value;
		}
	}
	
	public function SetSetting($setting,$value)
	{
		$res=mysql_query('SELECT COUNT(*) AS cnt FROM settings WHERE setting="'.$setting.'"',$this->mysqlconn);
		$row=mysql_fetch_array($res);
		if($row['cnt']>0)
		{
			$query='UPDATE settings SET s_value="'.$value.'" WHERE setting="'.$setting.'"';
		} else {
			$query='INSERT INTO settings (setting,s_value) VALUES ("'.$setting.'","'.$value.'")';
		}
		mysql_query($query,$this->mysqlconn);
	}
}
?>