<?php
require('../classes/adminauth.php');

$a = new adminauth();

if(!$a->CheckPermission('drop_voucher'))
{
	require('../classes/gui.php');
	include('../includes/header.php');
	include('menu.php');
	echo '<center><b>You have no permission to drop devices.</b></center></body></html>';
	die();
}

include('../includes/header.php');

include('menu.php');

if(is_numeric($_POST['cnt']) && trim($_POST['cnt'])!='') // Has a number been entered or do we have to display the form?
{
	// TODO
} else {
	echo '<form action="addvoucher.php" method="post" name="voucherform">
	How many vouchers do you want to create?<br>
	<input type="text" class="formstyle" name="cnt" size="2"> pieces (enter amount)<br><br>

	<input type="checkbox" name="print" value="y" class="formstyle" checked> Print vouchers in PDF
	<br><br><input type="submit" value="Create" class="formstyle"></form>';
}
?>