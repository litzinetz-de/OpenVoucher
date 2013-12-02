<?php
include('../includes/config.php');
class adminauth
{
	private $auth_ok;
	private $auth_user;
	private $auth_pwd;
	private $settings;
	private $mysqlconn;
	private $mode;
	
	function __construct($mode='gui') // TODO: Get session id from parameter if it's an api connection
	{
		$this->mode=$mode;
		if($mode=='api')
		{
			ini_set("session.use_cookies",0);
			ini_set("session.use_trans_sid",1);
			session_id($_GET['session_id']);
		}
		$this->auth_ok=true;
		session_start();
		
		$this->mysqlconn=mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PWD);
		mysql_select_db(MYSQL_DB,$this->mysqlconn);
		
		if(trim($_POST['user'])!='' && trim($_POST['pwd'])!='') // Got credentials from form - new user
		{
			// Get and check password
			$res=mysql_query('SELECT username,pwd FROM users WHERE username="'.$_POST['user'].'"',$this->mysqlconn);
			$row=mysql_fetch_array($res);
			if($row['pwd']!=sha1($_POST['pwd'])) // Password correct?
			{
				$this->auth_ok=false; // Not correct
			} else {
				$_SESSION['login']=$row['username']; // Password correct, start session
				setcookie('LastUsername', $row['username'], time()+31536000); // Remeber username for next login
			}
		} else {
			if(trim($_SESSION['login'])=='') // Is there an active session?
			{
				$this->auth_ok=false; // No active session, user has to login first
			} else {
				// There is an active session, we will check if the username is correct
				$res=mysql_query('SELECT COUNT(*) AS cnt FROM users WHERE username="'.$_SESSION['login'].'"',$this->mysqlconn);
				$row=mysql_fetch_array($res);
				if($row['cnt']==0 || trim($row['cnt'])=='')
				{
					$this->auth_ok=false; // Username in session not found in database or database error
					echo 'user not found';
				}
			}
		}
		
		if($this->auth_ok) // so far so good...
		{
			// ...but is the user allowed to log in via admin panel / api?
			if($mode=='api')
			{
				$this->auth_ok=$this->CheckPermission('api_login');
			} else {
			$this->auth_ok=$this->CheckPermission('admin_login');
			}
		}
		
		// If auth_ok has not been set to false until this point, the user is authenticated
		
		if(!$this->auth_ok)
		{			
			if($mode=='api')
			{
				echo '<authentication>
<state>failed</state>
</authentication>';
			} else {
				include('../includes/header.php');
				echo '<center><b>Please login</b></center>
				<tr>
				<form action="'.$_SERVER['PHP_SELF'].'" method="post">
				<table border="0">
				<td>Username:</td>
				<td><input type="text" name="user" value="'.$_COOKIE['LastUsername'].'" class="formstyle"></td>
				</tr><tr>
				<td>Password:</td>
				<td><input type="password" name="pwd" class="formstyle">
				</td></tr></table>
				<br><input type="submit" value="Login" class="formstyle"></form>
				</body>
				</html>';
			}
			die();
		}
		
		if($mode=='api')
		{
			echo '<authentication>
			<state>success</state>
			<session>'.session_id().'</session>
			</authentication>';
		}
		
	}
	
	public function Logout()
	{
		session_start();
		$_SESSION = array();
		session_destroy();
		if($this->mode=='gui')
		{
			header('Location: index.php');
		}
		if($this->mode=='api')
		{
			echo '<action>
<job>logout</job>
<state>success</state>
</action>';
		}
	}
	
	public function CheckPermission($permission) // Check if the logged in user has a specific permission
	{
		$res=mysql_query('SELECT COUNT(*) AS cnt FROM permissions WHERE username="'.$_SESSION['login'].'" AND permission="all"',$this->mysqlconn);
		$row=mysql_fetch_array($res);
		if($row['cnt']>0)
		{
			return true;
		} else {
			$res=mysql_query('SELECT COUNT(*) AS cnt FROM permissions WHERE username="'.$_SESSION['login'].'" AND permission="'.$permission.'"',$this->mysqlconn);
			$row=mysql_fetch_array($res);
			if($row['cnt']>0)
			{
				return true;
			} else {
				return false;
			}
		}
	}
	
	public function GetUsername()
	{
		return $_SESSION['login'];
	}
}
?>