<?php
class adminauth
{
	private $auth_ok;
	private $auth_user;
	private $auth_pwd;
	private $settings;
	private $mysqlconn;
	
	function __construct()
	{
		$this->auth_ok=true;
		session_start();
		
		$this->settings=parse_ini_file('.settings.ini',TRUE);
		$this->mysqlconn=mysql_connect($this->settings['mysql']['host'],$this->settings['mysql']['user'],$this->settings['mysql']['pwd']);
		mysql_select_db($this->settings['mysql']['db'],$this->mysqlconn);
		
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
				}
			}
		} // If auth_ok has not been set to false until this point, the user is authenticated
		
		if(!$this->auth_ok)
		{
			echo '<html>
			<head>
			<title>Login</title>
			</head>
			<body>
			<center><b>Please login</b></center>
			<tr>
			<form action="'.$_SERVER['PHP_SELF'].'" method="post">
			<table border="0">
			<td>Username:</td>
			<td><input type="text" name="user" value="'.$_COOKIE['LastUsername'].'"></td>
			</tr><tr>
			<td>Password:</td>
			<td><input type="password" name="pwd">
			</td></tr></table>
			<br><input type="submit" value="Login"></form>
			</body>
			</html>';
			die();
		}
		
	}
	
	public function Logout()
	{
		session_start();
		$_SESSION = array();
		session_destroy();
		header('Location: index.php');
	}
	
	public function CheckPermission($permission) // Check if the logged in user has a specific permission
	{
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
?>