<?php
include('../includes/config.php');

class systemmanager
{
	private $mysqlconn;
	
	function __construct()
	{
		$this->mysqlconn=mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PWD);
		mysql_select_db(MYSQL_DB,$this->mysqlconn);
	}
	
	public function GetSetting($setting)
	{
		$res=mysql_query('SELECT s_value FROM settings WHERE setting="'.$setting.'"',$this->mysqlconn);
		$row=mysql_fetch_array($res);
		return $row['s_value'];
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