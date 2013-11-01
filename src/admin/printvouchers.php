<?php
require('../classes/adminauth.php');

$a = new adminauth();

if(!$a->CheckPermission('view_voucher'))
{
	include('../includes/header.php');
	include('menu.php');
	echo '<center><b>You have no permission to view (and therefore print) vouchers.</b></center></body></html>';
	die();
}

require('../classes/printvoucher.php');
if(!isset($_SESSION['print_voucher_list'])) { die('No voucher list given.'); }
$pr = new printvoucher('small',$_SESSION['print_voucher_list']);
?>