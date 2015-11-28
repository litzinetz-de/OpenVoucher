<?php
require_once('../includes/config.php');

if(!class_exists('systemmanager'))
{
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
			
			//$this->mysqlconn=mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PWD);
			//mysql_select_db(MYSQL_DB,$this->mysqlconn);
			$this->mysqlconn=new mysqli(MYSQL_HOST,MYSQL_USER,MYSQL_PWD,MYSQL_DB);
		}
		
		public function GetSetting($setting)
		{
			//$res=mysql_query('SELECT s_value FROM settings WHERE setting="'.$setting.'"',$this->mysqlconn);
			$qry='SELECT s_value FROM settings WHERE setting="'.$this->mysqlconn->real_escape_string($setting).'"';
			$res=$this->mysqlconn->query($qry);
			//$row=mysql_fetch_array($res);
			$row=$res->fetch_assoc();
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
			//$res=mysql_query('SELECT COUNT(*) AS cnt FROM settings WHERE setting="'.$setting.'"',$this->mysqlconn);
			$qry='SELECT COUNT(*) AS cnt FROM settings WHERE setting="'.$this->mysqlconn->real_escape_string($setting).'"';
			$res=$this->mysqlconn->query($qry);
			//$row=mysql_fetch_array($res);
			$row=$res->fetch_assoc();
			if($row['cnt']>0)
			{
				$qry='UPDATE settings SET s_value="'.$this->mysqlconn->real_escape_string($value).'" WHERE setting="'.$this->mysqlconn->real_escape_string($setting).'"';
			} else {
				$qry='INSERT INTO settings (setting,s_value) VALUES ("'.$this->mysqlconn->real_escape_string($setting).'","'.$this->mysqlconn->real_escape_string($value).'")';
			}
			//mysql_query($query,$this->mysqlconn);
			$this->mysqlconn->query($qry);
		}
	}
}
?>