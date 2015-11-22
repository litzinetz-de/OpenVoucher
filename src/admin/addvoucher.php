<?php
require('../classes/adminauth.php');

$a = new adminauth();

if(!$a->CheckPermission('add_voucher'))
{
	require('../classes/gui.php');
	include('../includes/header.php');
	include('menu.php');
	echo '<center><b>You have no permission to add vouchers.</b></center></body></html>';
	die();
}

include('../includes/header.php');

include('menu.php');

// Has a number been entered or do we have to display the form? Has the user set how long the voucher should be valid?
if((is_numeric($_POST['cnt']) && trim($_POST['cnt'])!='') && ($_POST['d']!=0 || $_POST['h']!=0 || $_POST['m']!=0) && $_POST['start_expire']!='')
{
	// Include and load the vouchermanager
	require('../classes/vouchermanager.php');
	$v = new vouchermanager();

	if(!is_numeric($_POST['dev-cnt'])) $_POST['dev-cnt']=1; // Has the user enterered a numeric value for the device count?
	
	$voucher_ids=array();
	
	if($_POST['start_expire']=='now') // Shall the voucher(s) start expiring instantly?
	{
		$valid_until=time()+($_POST['d']*86400)+($_POST['h']*3600)+($_POST['m']*60); // Calculate expiration time from now on
		$valid_for=0;
	} else { // Voucher shall start expiring at entering the code
		$valid_until=0;
		$valid_for=($_POST['e_d']*86400)+($_POST['e_h']*3600)+($_POST['e_m']*60);
	}
	for($i=1;$i<=$_POST['cnt'];$i++)
	{
		array_push($voucher_ids,$v->MakeVoucher($_POST['dev-cnt'],$valid_until,$valid_for,$_POST['comment']));
	}
	echo '<center><b>The voucher(s) have been issued.</b></center><br><br>';
	if($_POST['print']=='y')
	{
		$_SESSION['print_voucher_list']=$voucher_ids;
		echo '<ul><a href="printvouchers.php" target="_blank">Print voucher(s)</a></ul>';
	}
	echo 'The following voucher IDs have been issued:<br><ul>';
	foreach($voucher_ids as $vid)
	{
		echo '<li>'.$vid.'</li>';
	}
	echo '</ul>';
} else {
	echo '<form action="addvoucher.php" method="post" name="voucherform">
	<table border="0" cellspacing="1">
	<tr class="darkbg">
	<td>Number of vouchers to create</td><td>';
	
	if($s->GetSetting('force_voucher-qty')=='y')
	{
			echo '<input type="text" class="roinput" name="cnt" size="2" value="'.$s->GetSetting('default_voucher-qty').'" readonly>';
	} else {
		echo '<input type="text" class="formstyle" name="cnt" size="2" value="'.$s->GetSetting('default_voucher-qty').'">';
	}
	
	echo '</td></tr><tr class="lightbg"><td>
	Duration/Expiration of validity</td><td>
	<table border="0" cellspacing="0" width="100%"><tr class="darkbg">';
	
	if($s->GetSetting('default_start_exp')=='creation')
	{
		$exp_now_checked=' checked';
		$exp_given_checked='';
	} else {
		$exp_now_checked='';
		$exp_given_checked=' checked';
	}
	
	if($s->GetSetting('force_start_exp')=='y')
	{
		if($s->GetSetting('force_exp')=='y')
		{
			echo '<td><input type="radio" name="start_expire" value="now" id="exp_now" '.$exp_now_checked.' disabled> Fixed expiration time <input type="text" class="roinput" name="d" size="2" value="'.$s->GetSetting('default_exp_d').'" readonly> days, <input type="text" class="roinput" name="h" size="2" value="'.$s->GetSetting('default_exp_h').'" readonly> hours, 
			<input type="text" class="roinput" name="m" size="2" value="'.$s->GetSetting('default_exp_m').'" readonly> minutes</td>
			</td></tr>
			<tr class="lightbg">
			<td>
			<input type="radio" name="start_expire" value="given" id="exp_given" '.$exp_given_checked.' disabled> <input type="text" class="roinput" name="e_d" size="2" value="'.$s->GetSetting('default_exp_d').'" readonly> days, <input type="text" class="roinput" name="e_h" size="2" value="'.$s->GetSetting('default_exp_h').'" readonly> hours, 
			<input type="text" class="roinput" name="e_m" size="2" value="'.$s->GetSetting('default_exp_m').'" readonly> minutes after activating the voucher</td>
			</td>';
		} else {
			echo '<td><input type="radio" name="start_expire" value="now" id="exp_now" '.$exp_now_checked.' disabled> Fixed expiration time <input type="text" class="formstyle" name="d" size="2" value="'.$s->GetSetting('default_exp_d').'"> days, <input type="text" class="formstyle" name="h" size="2" value="'.$s->GetSetting('default_exp_h').'"> hours, 
			<input type="text" class="formstyle" name="m" size="2" value="'.$s->GetSetting('default_exp_m').'"> minutes</td>
			</td></tr>
			<tr class="lightbg">
			<td>
			<input type="radio" name="start_expire" value="given" id="exp_given" '.$exp_given_checked.' disabled> <input type="text" class="formstyle" name="e_d" size="2" value="'.$s->GetSetting('default_exp_d').'"> days, <input type="text" class="formstyle" name="e_h" size="2" value="'.$s->GetSetting('default_exp_h').'"> hours, 
			<input type="text" class="formstyle" name="e_m" size="2" value="'.$s->GetSetting('default_exp_m').'"> minutes after activating the voucher</td>
			</td>';
		}
	} else {
		if($s->GetSetting('force_exp')=='y')
		{
			echo '<td><input type="radio" name="start_expire" value="now" id="exp_now" '.$exp_now_checked.'> Fixed expiration time <input type="text" class="roinput" name="d" size="2" value="'.$s->GetSetting('default_exp_d').'" readonly> days, <input type="text" class="roinput" name="h" size="2" value="'.$s->GetSetting('default_exp_h').'" readonly> hours, 
			<input type="text" class="roinput" name="m" size="2" value="'.$s->GetSetting('default_exp_m').'" readonly> minutes</td>
			</td></tr>
			<tr class="lightbg">
			<td>
			<input type="radio" name="start_expire" value="given" id="exp_given" '.$exp_given_checked.'> <input type="text" class="roinput" name="e_d" size="2" value="'.$s->GetSetting('default_exp_d').'" readonly> days, <input type="text" class="roinput" name="e_h" size="2" value="'.$s->GetSetting('default_exp_h').'" readonly> hours, 
			<input type="text" class="roinput" name="e_m" size="2" value="'.$s->GetSetting('default_exp_m').'" readonly> minutes after activating the voucher</td>
			</td>';
		} else {
			echo '<td><input type="radio" name="start_expire" value="now" id="exp_now" '.$exp_now_checked.'> Fixed expiration time <input type="text" class="formstyle" name="d" size="2" value="'.$s->GetSetting('default_exp_d').'"> days, <input type="text" class="formstyle" name="h" size="2" value="'.$s->GetSetting('default_exp_h').'"> hours, 
			<input type="text" class="formstyle" name="m" size="2" value="'.$s->GetSetting('default_exp_m').'"> minutes</td>
			</td></tr>
			<tr class="lightbg">
			<td>
			<input type="radio" name="start_expire" value="given" id="exp_given" '.$exp_given_checked.'> <input type="text" class="formstyle" name="e_d" size="2" value="'.$s->GetSetting('default_exp_d').'"> days, <input type="text" class="formstyle" name="e_h" size="2" value="'.$s->GetSetting('default_exp_h').'"> hours, 
			<input type="text" class="formstyle" name="e_m" size="2" value="'.$s->GetSetting('default_exp_m').'"> minutes after activating the voucher</td>
			</td>';
		}
	}
	
	
	
	echo '</tr>
	</table>
	<script language="javascript">';
	if($s->GetSetting('force_exp')!='y')
	{
		echo 'AddVoucherToggleExp();';
	}
	echo '</script>
	
	</td></tr>
	<tr class="lightbg">
	<td>How many devices may the user register with this voucher?</td><td>';
	
	if($s->GetSetting('force_voucher-qty')=='y')
	{
		echo '<input type="text" class="roinput" name="dev-cnt" size="2" value="'.$s->GetSetting('default_voucher-qty').'" readonly>';
	} else {
		echo '<input type="text" class="formstyle" name="dev-cnt" size="2" value="'.$s->GetSetting('default_voucher-qty').'">';
	}
	
	echo ' devices</td></tr><tr class="darkbg"><td>
	You may enter a comment if you with (e.g. the user\'s name):</td><td>
	<input type="text" class="formstyle" name="comment" size="20"></td></tr>
	</table>
	<br><br>

	<input type="checkbox" name="print" value="y" class="formstyle" checked> Print vouchers in PDF
	<br><br><input type="submit" value="Create" class="formstyle"></form>';
}
?>
