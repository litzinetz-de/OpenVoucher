<?php
class usermanager
{
	private $settings;
	private $mysqlconn;
	private $existing_permissions;
	
	function __construct()
	{
		$this->settings=parse_ini_file('../.settings.ini',TRUE);
		$this->mysqlconn=mysql_connect($this->settings['mysql']['host'],$this->settings['mysql']['user'],$this->settings['mysql']['pwd']);
		mysql_select_db($this->settings['mysql']['db'],$this->mysqlconn);
		
		$this->existing_permissions=array('add_voucher','admin_login','drop_device','drop_voucher','sys_config','view_users','view_voucher','delete_users','edit_permissions','add_users');
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
	
	public function DeleteUser($username)
	{
		@mysql_query('DELETE FROM users WHERE username="'.$username.'"');
		@mysql_query('DELETE FROM permissions WHERE username="'.$username.'"');
	}

	public function GetPermissionList($user)
	{
		$res=mysql_query('SELECT permission FROM permissions WHERE username="'.$user.'"',$this->mysqlconn);
		$dataset=array();
		while($row=mysql_fetch_array($res))
		{
			array_push($dataset,$row['permission']);
		}
		return $dataset;
	}

	public function AddPermission($user,$permission)
	{
		@mysql_query('INSERT INTO permissions (username,permission) VALUES ("'.$user.'","'.$permission.'")',$this->mysqlconn);
	}
	
	public function DropPermission($user,$permission)
	{
		@mysql_query('DELETE FROM permissions WHERE permission="'.$permission.'" AND username="'.$user.'"',$this->mysqlconn);
	}
	
	public function GetExistingPermissions() // Get all permission levels that exist in OpenVoucher
	{
		return $this->existing_permissions;
	}
}
?>
