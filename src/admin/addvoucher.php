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

	if(!is_numeric($_POST['dev-cnt'])) $_POST['dev-cnt']=1; // Has the user enterered a numeric value for the device count?
	
	$voucher_ids=array();
	$valid_until=time()+($_POST['d']*86400)+($_POST['h']*3600)+($_POST['m']*60); // Calculate expiration time
	for($i=1;$i<=$_POST['cnt'];$i++)
	{
		array_push($voucher_ids,$v->MakeVoucher($_POST['dev-cnt'],$valid_until,$_POST['comment']));
	}
	echo '<center><b>The voucher(s) have been issued.</b></center><br><br>';
	if($_POST['print']=='y')
	{
		$_SESSION['print_voucher_list']=$voucher_ids;
		echo '<ul><a href="printvouchers.php">Print voucher(s)</a></ul>';
	}
	echo 'The following voucher IDs have been issued:<br><ul>';
	foreach($voucher_ids as $vid)
	{
		echo '<li>'.$vid.'</li>';
	}
	echo '</ul>';
} else {
	echo '<form action="addvoucher.php" method="post" name="voucherform">
	How many vouchers do you want to create?<br>
	<input type="text" class="formstyle" name="cnt" size="2" value="10"> pieces (enter amount)<br><br>
	How long shall the voucher be valid?<br>
	<input type="text" class="formstyle" name="d" size="2" value="0"> days, <input type="text" class="formstyle" name="h" size="2" value="4"> hours, 
	<input type="text" class="formstyle" name="m" size="2" value="0"> minutes
	<br><br>
	How many devices may the user register with this voucher?<br>
	<input type="text" class="formstyle" name="dev-cnt" size="2" value="3"> devices<br><br>
	You may enter a comment if you with (e.g. the user\'s name):<br>
	<input type="text" class="formstyle" name="comment" size="20"><br><br>

	<input type="checkbox" name="print" value="y" class="formstyle" checked> Print vouchers in PDF
	<br><br><input type="submit" value="Create" class="formstyle"></form>';
}
?>
