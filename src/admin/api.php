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
?>