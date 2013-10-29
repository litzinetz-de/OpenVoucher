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

// Has a number been entered or do we have to display the form? Has the user set how long the voucher should be valid?
if((is_numeric($_POST['cnt']) && trim($_POST['cnt'])!='') && ($_POST['d']!=0 || $_POST['h']!=0 || $_POST['m']!=0))
{
	// Include and load the vouchermanager
	require('../classes/vouchermanager.php');
	$v = new vouchermanager();
	
	$voucher_ids=array();
	for($i=1;$i<=$_POST['cnt'];$i++)
	{
		$valid_until=time()+($_POST['d']*86400)+($_POST['h']*3600)+($_POST['m']*60); // Calculate expiration time
		array_push($voucher_ids,$v->MakeVoucher(1,$valid_until,''));
	}
	print_r($voucher_ids);
} else {
	echo '<form action="addvoucher.php" method="post" name="voucherform">
	How many vouchers do you want to create?<br>
	<input type="text" class="formstyle" name="cnt" size="2" value="10"> pieces (enter amount)<br><br>
	How long shall the voucher be valid?<br>
	<input type="text" class="formstyle" name="d" size="2" value="0"> days, <input type="text" class="formstyle" name="h" size="2" value="4"> hours, 
	<input type="text" class="formstyle" name="m" size="2" value="0"> minutes
	<br><br>

	<input type="checkbox" name="print" value="y" class="formstyle" checked> Print vouchers in PDF
	<br><br><input type="submit" value="Create" class="formstyle"></form>';
}
?>