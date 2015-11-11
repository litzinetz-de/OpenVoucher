<?php
include('../includes/config.php');
class usermanager
{
	private $settings;
	private $mysqlconn;
	private $existing_permissions;
	
	function __construct()
	{
		//$this->mysqlconn=mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PWD);
		//mysql_select_db(MYSQL_DB,$this->mysqlconn);
		$this->mysqlconn=new mysqli(MYSQL_HOST,MYSQL_USER,MYSQL_PWD,MYSQL_DB);
		
		$this->existing_permissions=array('all','add_voucher','admin_login','drop_device','drop_voucher','sys_config','view_users','view_voucher',
		'delete_users','edit_permissions','add_users','api_login');
	}

	public function GetUserlist()
	{
		//$res=mysql_query('SELECT username FROM users ORDER BY username ASC',$this->mysqlconn);
		$qry='SELECT username FROM users ORDER BY username ASC';
		$res=$this->mysqlconn->query($qry);
		$dataset=array();
		while($row=$res->fetch_assoc())
		{
			// Get permissions for this user
			//$perm_res=mysql_query('SELECT permission FROM permissions WHERE username="'.$row['username'].'" ORDER BY permission ASC');
			$perm_qry='SELECT permission FROM permissions WHERE username="'.$this->mysqlconn->real_escape_string($row['username']).'" ORDER BY permission ASC';
			$perm_res=$this->mysqlconn->query($perm_qry);
			$perm='';
			while($perm_row=$perm_res->fetch_assoc()) // Build permission list
			{
				if($perm!='')
				{
					$perm=$perm.', ';
				}
				$perm=$perm.$perm_row['permission'];
			}
			$row['permission_list']=$perm; // Save permissions to array
			array_push($dataset,$row);
		}
		return $dataset;
	}

	public function AddUser($username,$pwd)
	{
		//@mysql_query('INSERT INTO users (username,pwd) VALUES ("'.$username.'","'.sha1($pwd).'")',$this->mysqlconn);
		$qry='INSERT INTO users (username,pwd) VALUES ("'.$this->mysqlconn->real_escape_string($username).'","'.sha1($this->mysqlconn->real_escape_string($pwd)).'")';
		$this->mysqlconn->query($qry);
	}
	
	public function DeleteUser($username)
	{
		$this->mysqlconn->query('DELETE FROM users WHERE username="'.$this->mysqlconn->real_escape_string($username).'"');
		$this->mysqlconn->query('DELETE FROM permissions WHERE username="'.$this->mysqlconn->real_escape_string($username).'"');
	}

	public function GetPermissionList($user)
	{
		//$res=mysql_query('SELECT permission FROM permissions WHERE username="'.$user.'"',$this->mysqlconn);
		$qry='SELECT permission FROM permissions WHERE username="'.$this->mysqlconn->real_escape_string($user).'"';
		$res=$this->mysqlconn->query($qry);
		$dataset=array();
		while($row=$res->fetch_assoc())
		{
			array_push($dataset,$row['permission']);
		}
		return $dataset;
	}

	public function AddPermission($user,$permission)
	{
		//@mysql_query('INSERT INTO permissions (username,permission) VALUES ("'.$user.'","'.$permission.'")',$this->mysqlconn);
		$this->mysqlconn->query('INSERT INTO permissions (username,permission) VALUES ("'.$this->mysqlconn->real_escape_string($user).'","'.$this->mysqlconn->real_escape_string($permission).'")');
	}
	
	public function DropPermission($user,$permission)
	{
		//@mysql_query('DELETE FROM permissions WHERE permission="'.$permission.'" AND username="'.$user.'"',$this->mysqlconn);
		$this->mysqlconn->query('DELETE FROM permissions WHERE permission="'.$this->mysqlconn->real_escape_string($permission).'" AND username="'.$this->mysqlconn->real_escape_string($user).'"');
	}
	
	public function GetExistingPermissions() // Get all permission levels that exist in OpenVoucher
	{
		return $this->existing_permissions;
	}
}
?>
