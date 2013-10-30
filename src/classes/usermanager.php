<?php
class usermanager
{
	private $settings;
	private $mysqlconn;
	
	function __construct()
	{
		$this->settings=parse_ini_file('../.settings.ini',TRUE);
		$this->mysqlconn=mysql_connect($this->settings['mysql']['host'],$this->settings['mysql']['user'],$this->settings['mysql']['pwd']);
		mysql_select_db($this->settings['mysql']['db'],$this->mysqlconn);
	}

	public function GetUserlist()
	{
		$res=mysql_query('SELECT username FROM users ORDER BY username ASC',$this->mysqlconn);
		$dataset=array();
		while($row=mysql_fetch_array($res))
		{
			array_push($dataset,$row);
		}
		return $dataset;
	}

	public function AddUser($username,$pwd)
	{
		@mysql_query('INSERT INTO users (username,pwd) VALUES ("'.$username.'","'.sha1($pwd).'")',$this->mysqlconn);
	}

	public function GetPermissionList($user)
	{
		$res=mysql_query('SELECT permission FROM permissions WHERE username="'.$user.'"',$this->mysqlconn);
		return mysql_fetch_array($res);
	}

	public function AddPermission($user,$permission)
	{
		@mysql_query('INSERT INTO permissions (username,permission) VALUES ("'.$user.'","'.$permission.'")',$this->mysqlconn);
	}
	
	public function DropPermission($user,$permisssion)
	{
		@mysql_query('DELETE FROM permissions WHERE permission="'.$permission.'",user="'.$user.'"',$this->mysqlconn);
	}
}
?>
