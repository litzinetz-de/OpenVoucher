<?php
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo "\n";

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

if($_GET['do']=='lst-vouchers')
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
			<verification>'.$vouchers[$i]['verification'].'</verification>
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
	echo '<action>'."\n\t".'<job>dropvoucher</job>'."\n\t<state>";
	if(trim($_GET['vid'])=='')
	{
		echo 'failed';
	} else {
		echo 'success';
		$vouchermanager->DropVoucher($_GET['vid'],true);
	}
	echo '</state>'."\n".'</action>';
}

if($_GET['do']=='dropdevice')
{
	echo '<action>'."\n\t".'<job>dropdevice</job>'."\n\t<state>";
	if(trim($_GET['type'])=='' || trim($_GET['addr'])=='')
	{
		echo 'failed';
	} else {
		echo 'success';
		$vouchermanager->DropDevice($_GET['type'],$_GET['addr']);
	}
	echo '</state>'."\n".'</action>';
}
if($_GET['do']=='addvoucher')
{
	echo '<action>'."\n\t".'<job>addvoucher</job>'."\n\t<state>";
	
	if(!isset($_GET['devicecount']) || !is_numeric($_GET['devicecount') || !isset($_GET['valid_until']) || !is_numeric($_GET['valid_until']))
	{
		echo 'failed</state>';
	} else {
		$vid=$vouchermanager->MakeVoucher($_GET['devicecount'],$_GET['valid_until'],$_GET['comment']);
		if($vid==0)
		{
			echo 'failed</state>';
		} else {
			echo 'success</state>'."\n\t".'<vid>'.$vid.'</vid>';
		}
	}
	echo '</action>';
}
?>
