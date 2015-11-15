<?php
require('../classes/adminauth.php');
require('../classes/systemmanager.php');

$a = new adminauth();
$s = new systemmanager();

if(!$a->CheckPermission('sys_config'))
{
	require('../classes/gui.php');
	include('../includes/header.php');
	include('menu.php');
	echo '<center><b>You have no permission to manage the system config.</b></center></body></html>';
	die();
}

include('../includes/header.php');

include('menu.php');

echo '<center><b>Manage system config</b></center><br><br>';

if($_GET['do']=='update_general')
{
	$s->SetSetting('vouchertext1',$_POST['vouchertext1']);
	$s->SetSetting('vouchertext2',$_POST['vouchertext2']);
	$s->SetSetting('pre-form-text',$_POST['pre-form-text']);
	$s->SetSetting('post-form-text',$_POST['post-form-text']);
	
	if($_POST['use_verification']=='y')
	{
		$s->SetSetting('use_verification','y');
	} else {
		$s->SetSetting('use_verification','n');
	}
	
	if($_POST['use_exp_date']=='y')
	{
		$s->SetSetting('use_exp_date','y');
	} else {
		$s->SetSetting('use_exp_date','n');
	}
}

if($_GET['do']=='update_default')
{
	$s->SetSetting('default_voucher-qty',$_POST['qty']);
	$s->SetSetting('force_voucher-qty',$_POST['force_voucher-qty']);
	$s->SetSetting('default_device-qty',$_POST['qty_devices']);
	$s->SetSetting('force_device-qty',$_POST['force_device-qty']);
}

if($_GET['do']=='logo')
{
	move_uploaded_file($_FILES['logo']['tmp_name'],'../graphics/'.trim($_FILES['logo']['name']));
	if(!preg_match("/image/i",$_FILES['logo']['type']))
	{
		unlink('../graphics/'.trim($_FILES['logo']['name']));
		die('The file doesn\'t seem to be a picture.');
	}
	$s->SetSetting('logo',trim($_FILES['logo']['name']));
}

if($_GET['do']=='del_logo')
{
	$logo=$s->GetSetting('logo');
	if(file_exists('../graphics/'.$logo) && !is_dir('../graphics/'.$logo))
	{
		@unlink('../graphics/'.$logo);
		$s->SetSetting('logo','');
	}
}

echo '<table border="0" cellspacing="1">
<tr class="tableheader"><td colspan="2">General Settings</td></tr>
<tr class="darkbg">
<form action="'.$_SERVER['PHP_SELF'].'?do=update_general" method="post">
<td valign="top">Voucher information text 1:<br>
<small>First line of info text shown in the voucher.</small>
</td><td><input type="text" class="formstyle" name="vouchertext1" size="50" value="'.$s->GetSetting('vouchertext1').'"></td></tr>
<tr class="lightbg">
<td valign="top">Voucher information text 2:<br>
<small>Second line of info text shown in the voucher.</small>
</td><td><input type="text" class="formstyle" name="vouchertext2" size="50" value="'.$s->GetSetting('vouchertext2').'"></td></tr>
<tr class="darkbg">
<td valign="top">Pre-form text:<br>
<small>This text is shown on the landing page above the form.</small>
</td><td><input type="text" class="formstyle" name="pre-form-text" size="50" value="'.$s->GetSetting('pre-form-text').'"></td></tr>
<tr class="lightbg">
<td valign="top">Post-form text:<br>
<small>This text is shown on the landing page below the form.</small>
</td><td><input type="text" class="formstyle" name="post-form-text" size="50" value="'.$s->GetSetting('post-form-text').'"></td></tr>
<tr class="darkbg">
<td>Use verification keys:</td>';

if($s->GetSetting('use_verification')=='y')
{
	$veri_checked=' checked';
} else {
	$veri_checked='';
}

echo '<td><input type="checkbox" name="use_verification" value="y"'.$veri_checked.'></td></tr>
<tr class="lightbg">
<td>Use expiration date for voucher codes:</td>';

if($s->GetSetting('use_exp_date')=='y')
{
	$exp_checked=' checked';
} else {
	$exp_checked='';
}

if($s->GetSetting('force_voucher-qty')=='y')
{
	$force_voucher_qty_checked=' checked';
} else {
	$force_voucher_qty_checked='';
}

if($s->GetSetting('force_device-qty')=='y')
{
	$force_device_qty_checked=' checked';
} else {
	$force_device_qty_checked='';
}

echo '<td><input type="checkbox" name="use_exp_date" value="y"'.$exp_checked.'></td></tr>
</table>
<br>
<input type="submit" value="Save" class="formstyle">
</form>
<br><br>
<form action="'.$_SERVER['PHP_SELF'].'?do=update_default" method="post">
<table border="0" cellspacing="1">
<tr class="tableheader"><td colspan="3">Change and enforce default values</td></tr>
<tr class="darkbg"><td>Number of vouchers to create</td><td><input type="text" name="qty" size="5" class="formstyle" value="'.$s->GetSetting('default_voucher-qty').'"> 
</td><td><input type="checkbox" class="formstyle" name="force_voucher-qty" value="y" '.$force_voucher_qty_checked.'> Enforce</td></tr>
<tr class="lightbg"><td>Number of devices per voucher</td><td><input type="text" name="qty_devices" class="formstyle" value="'.$s->GetSetting('default_device-qty').'">
</td><td><input type="checkbox" class="formstyle" name="force_device-qty" value="y" '.$force_device_qty_checked.'> Enforce</td></tr>

</table>
<br>
<input type="submit" value="Save" class="formstyle">
</form>

<br><br>
<table border="0" cellspacing="1">
<tr class="tableheader"><td colspan="2">Change logo</td></tr>
<tr class="darkbg">
<td>
Logo:</td><td>';

$logo=$s->GetSetting('logo');
if(file_exists('../graphics/'.$logo) && !is_dir('../graphics/'.$logo))
{
	echo '<img src="../graphics/'.$logo.'"><br><a href="'.$_SERVER['PHP_SELF'].'?do=del_logo">[Delete]</a>';
} else {
	echo '<i>No image defined or not found</i>';
}

echo '</td></tr>
<tr class="lightbg"><td>
You can upload a new logo here. This will overwrite your existing logo.</td><td>
<form action="'.$_SERVER['PHP_SELF'].'?do=logo" method="post" enctype="multipart/form-data">
<input type="file" name="logo" class="formstyle">
</td></tr>
</table>
<br>
<input type="submit" value="Upload" class="formstyle">
</form>
</body></html>';