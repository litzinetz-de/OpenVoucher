<?php
require('../classes/adminauth.php');
require('../classes/systemmanager.php');
require('../classes/usermanager.php');
require('../classes/versionmanager.php');
require('../classes/vouchermanager.php');

$auth = new adminauth('api');
$systemmanager = new systemmanager();
$usermanager = new usermanager();
$versionmanager = new versionmanager();
$vouchermanager = new vouchermanager();

if($_GET['do']=='logout')
{
	$auth->Logout();
}

if($_GET['do']=='lstvouchers')
{
	echo '<voucherlist>'."\n";
	if($auth->CheckPermission('view_voucher'))
	{
		echo "\t".'<state>success</state>'."\n";
		$vouchers=$vouchermanager->GetVoucherList();
		for($i=0;$i<count($vouchers);$i++)
		{
			echo "\t\t".'<voucher>
			<vid>'.$vouchers[$i]['voucher_id'].'</vid>
			<verification>'.$vouchers[$i]['verification_key'].'</verification>
			<devcount>'.$vouchers[$i]['dev_count'].'</devcount>
			<validuntil>'.$vouchers[$i]['valid_until'].'</validuntil>
			<comment>'.$vouchers[$i]['comment'].'</comment>
		</voucher>'."\n";
		}
	} else {
		echo '<state>failed</state>';
	}
	echo '</voucherlist>';
}

if($_GET['do']=='dropvoucher')
{
	echo '<action>'."\n\t".'<job>dropvoucher</job>'."\n";
	if($auth->CheckPermission('drop_voucher'))
	{
		if(trim($_GET['vid'])=='')
		{
			echo "\t".'<state>failed</state>'."\n";
		} else {
			echo "\t".'<state>success</state>'."\n";
			$vouchermanager->DropVoucher($_GET['vid'],true);
		}
	} else {
		echo "\t".'<state>failed</state>'."\n";
	}
	echo '</action>';
}

if($_GET['do']=='dropdevice')
{
	echo '<action>'."\n\t".'<job>dropdevice</job>'."\n";
	
	if($auth->CheckPermission('drop_device'))
	{
		if(trim($_GET['type'])=='' || trim($_GET['addr'])=='')
		{
			echo "\t".'<state>failed</state>'."\n";
		} else {
			echo "\t".'<state>success</state>'."\n";
			$vouchermanager->DropDevice($_GET['type'],$_GET['addr']);
		}
	} else {
		
	}
	echo '</action>';
}
if($_GET['do']=='addvoucher')
{
	echo '<action>'."\n\t".'<job>addvoucher</job>'."\n";
	
	if($auth->CheckPermission('add_voucher'))
	{
		if(!isset($_GET['devicecount']) || !is_numeric($_GET['devicecount']) || !isset($_GET['valid_until']) || !is_numeric($_GET['valid_until']))
		{
			echo "\t".'<state>failed</state>'."\n";
		} else {
			$vid=$vouchermanager->MakeVoucher($_GET['devicecount'],$_GET['valid_until'],$_GET['comment']);
			if($vid==0)
			{
				echo "\t".'<state>failed</state>'."\n";
			} else {
				echo "\t".'<state>success</state>'."\n\t".'<vid>'.$vid.'</vid>'."\n";
			}
		}
	} else {
		echo "\t".'<state>failed</state>'."\n";
	}
	echo '</action>';
}

if($_GET['do']=='adduser')
{
	echo '<action>'."\n\t".'<job>adduser</job>'."\n";
	if($auth->CheckPermission('add_users'))
	{
		if(!isset($_GET['user']) || !isset($_GET['pwd']))
		{
			echo "\t".'<state>failed</state>'."\n";
		} else {
			 $usermanager->AddUser($_POST['add_user'],$_POST['add_pwd']);
			 echo "\t".'<state>success</state>'."\n";
		}
	} else {
		echo "\t".'<state>failed</state>'."\n";
	}
	echo '</action>';
}

if($_GET['do']=='addpermission')
{
	echo '<action>'."\n\t".'<job>addpermission</job>'."\n";
	if($auth->CheckPermission('edit_permissions'))
	{
		if(!isset($_GET['user']) || !isset($_GET['permission']))
		{
			echo "\t".'<state>failed</state>'."\n";
		} else {
			$usermanager->AddPermission($_GET['user'],$_GET['permission']);
			echo "\t".'<state>success</state>'."\n";
		}
	} else {
		echo "\t".'<state>failed</state>'."\n";
	}
	echo '</action>';
}

if($_GET['do']=='lstpermissions')
{
	echo '<permissionlist>'."\n";
	if($auth->CheckPermission('edit_permissions'))
	{
		if(isset($_GET['user']))
		{
			echo "\t".'<state>success</state>'."\n";
			$permissions=$usermanager->GetPermissionList($_GET['user']);
			foreach($permissions as $permission)
			{
				echo "\t".'<permission>'.$permission.'</permission>'."\n";
			}
		} else {
			echo "\t".'<state>failed</state>'."\n";
		}
	} else {
		echo "\t".'<state>failed</state>'."\n";
	}
	echo '</permissionlist>';
}

if($_GET['do']=='droppermission')
{
	echo '<action>'."\n\t".'<job>droppermission</job>'."\n";
	if($auth->CheckPermission('edit_permissions'))
	{
		if(!isset($_GET['user']) || !isset($_GET['permission']))
		{
			echo "\t".'<state>failed</state>'."\n";
		} else {
			$usermanager->DropPermission($_GET['user'],$_GET['permission']);
			echo "\t".'<state>success</state>'."\n";
		}
	}
	echo '</action>';
}

if($_GET['do']=='dropuser')
{
	echo '<action>'."\n\t".'<job>dropuser</job>'."\n";
	if($auth->CheckPermission('delete_users'))
	{
		if(!isset($_GET['user']))
		{
			echo "\t".'<state>failed</state>'."\n";
		} else {
			$usermanager->DropPermissionDeleteUser($_GET['user']);
			echo "\t".'<state>success</state>'."\n";
		}
	}
	echo '</action>';
}

if($_GET['do']=='lstusers')
{
	echo '<userlist>'."\n";
	if($auth->CheckPermission('view_users'))
	{
		if(isset($_GET['user']))
		{
			echo "\t".'<state>success</state>'."\n"
			$users=$usermanager->GetUserlist($_GET['user']);
		}
	}
}

?>
