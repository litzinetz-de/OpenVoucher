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
	
	if($a->CheckPermission('add_users')) // Show form to add users if the client has permission to do so
	{
		echo '<br><br><ul><u>Add User:</u><br>
		<form action="'.$_SERVER['PHP_SELF'].'?do=add" method="post">
		Username: <input type="text" name="add_user" size="20" class="formstyle"><br>
		Password: <input type="password" name="add_pwd" size="20" class="formstyle"><br>
		Repeat: <input type="password" name="add_repeat" size="20" class="formstyle"><br><br>
		<input type="submit" value="Add user" class="formstyle">
		</form></ul>';
	}
}

if($_GET['do']=='add') // Requested to delete a user
{
	if(!$a->CheckPermission('add_users'))
	{
		echo '<center><b>You have no permission to add users.</b></center></body></html>';
		die();
	}
	// Check the entered data
	if(trim($_POST['add_user'])=='') { die('No user given.'); }
	if($_POST['add_pwd']!=$_POST['add_repeat']) { die('The passwords do not match.'); }
	if(trim($_POST['add_pwd'])=='') { die('No password given.'); }
	
	$u->AddUser($_POST['add_user'],$_POST['add_pwd']);
	echo 'The user '.$_GET['add_user'].' has been added.';
}

if($_GET['do']=='del') // Requested to delete a user
{
	if(!$a->CheckPermission('delete_users'))
	{
		echo '<center><b>You have no permission to delete users.</b></center></body></html>';
		die();
	}
	
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

if($_GET['do']=='edit_perm')
{
	if(!$a->CheckPermission('edit_permissions'))
	{
		echo '<center><b>You have no permission to edit permissions.</b></center></body></html>';
		die();
	}
	if($_GET['user']==$_SESSION['login']) // The user is not allowed to delete himself
	{
		echo '<center><b>You cannot edit yourself. Please use another account to edit yours.</b></center>';
		die();
	}
	
	if($_GET['user']=='') die('No user given');
	echo '<center><b>Permissions for user '.$_GET['user'].'</b></center><br><br>
	<ul>';
	
	$permissions=$u->GetPermissionList($_GET['user']);
	
	foreach($permissions as $permission)
	{
		echo '<li>'.$permission.' <a href="'.$_SERVER['PHP_SELF'].'?do=drop_permission&user='.$_GET['user'].'&permission='.$permission.'">[Drop]</a></li>';
	}
	echo '</ul><br><br><form action="'.$_SERVER['PHP_SELF'].'" methdo="get">
	<input type="hidden" name="do" value="add_permission">
	<input type="hidden" name="user" value="'.$_GET['user'].'">
	Add permission: <select name="permission" size="1" class="formstyle">';
	foreach($u->GetExistingPermissions() as $permission)
	{
		echo '<option value="'.$permission.'">'.$permission.'</option>';
	}
	echo '</select> <input type="submit" value="OK" class="formstyle"></form>';
}

if($_GET['do']=='drop_permission')
{
	if($_GET['user']==$_SESSION['login']) // The user is not allowed to delete himself
	{
		echo '<center><b>You cannot edit yourself. Please use another account to edit yours.</b></center>';
		die();
	}
	
	if(!$a->CheckPermission('edit_permissions'))
	{
		echo '<center><b>You are not allowed to edit permissions.</b></center></body></html>';
		die();
	}
	
	$u->DropPermission($_GET['user'],$_GET['permission']);
	echo 'Permission &quot;'.$_GET['permission'].'&quot; has been dropped for user &quot;'.$_GET['user'].'&quot;<br><br>
	<a href="'.$_SERVER['PHP_SELF'].'?do=edit_perm&user='.$_GET['user'].'">Back to user\'s permissions</a>';
}
if($_GET['do']=='add_permission')
{
	if($_GET['user']==$_SESSION['login']) // The user is not allowed to delete himself
	{
		echo '<center><b>You cannot edit yourself. Please use another account to edit yours.</b></center>';
		die();
	}
	
	if(!$a->CheckPermission('edit_permissions'))
	{
		echo '<center><b>You are not allowed to edit permissions.</b></center></body></html>';
		die();
	}
	
	if($_GET['user']=='') die('No user given');
	if($_GET['permission']=='') die('No permission given');
	
	$u->AddPermission($_GET['user'],$_GET['permission']);
	echo 'Permission &quot;'.$_GET['permission'].'&quot; has been added for user &quot;'.$_GET['user'].'&quot;<br><br>
	<a href="'.$_SERVER['PHP_SELF'].'?do=edit_perm&user='.$_GET['user'].'">Back to user\'s permissions</a>';
}
?>
</body>
</html>
