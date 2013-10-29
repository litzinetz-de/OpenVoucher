<?php
require('../classes/adminauth.php');

$a = new adminauth();
if(!$a->CheckPermission('drop_voucher'))
{
	require('../classes/gui.php');
	$agui = new admingui();
	include('../includes/header.php');
	include('menu.php');
	echo '<center><b>You have no permission to drop vouchers.</b></center></body></html>';
	die();
}

require('../classes/vouchermanager.php');
$v = new vouchermanager();
$v->DropVoucher($_GET['vid'],true);
header('Location: index.php');

?>