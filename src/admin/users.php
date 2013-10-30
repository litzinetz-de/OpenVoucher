<?php
require('../classes/adminauth.php');

$a = new adminauth();
require('../classes/vouchermanager.php');
require('../classes/gui.php');
require('../classes/usermanager.php');
include('../includes/header.php');
include('menu.php');

$agui=new admingui();
$u=new usermanager();

if($_GET['do']=='') // If the user requested no specific action, we just display all users
{
	if(!$a->CheckPermission('view_users'))
	{
		echo '<center><b>You have no permission to edit users.</b></center></body></html>';
		die();
	}

	echo '<center><b>User list</b></center><br><br>';
	$agui->ListUsers($u->GetUserlist());
}

if($_GET['do']=='del') // Requested to delete a user
{
	if($_GET['user']=='') // No user given
	{
		echo '<center><b>No user given</b></center>';
		die();
	}
	if($_GET['confirm']=='y') // Did the user confirm the action?
	{
		if($_GET['user']==$_SESSION['login']) // The user is not allowed to delete himself
		{
			echo '<center><b>You cannot delete yourself. Please use another account to delete yours.</b></center>';
			die();
		}
		$u->DeleteUser($_GET['user']); // Delete the user
		echo 'The user '.$_GET['user'].' has been deleted.';
	} else {
		echo '<center><b>Do you really want to delete the user &quot;'.$_GET['user'].'&quot;?</b><br><br>
		<a href="'.$_SERVER['PHP_SELF'].'?do=del&user='.$_GET['user'].'&confirm=y">Yes, do it!</a></center>';
	}
}
?>
</body>
</html>
