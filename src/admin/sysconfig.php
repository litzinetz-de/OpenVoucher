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

if($_GET['do']=='update')
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

echo '<table border="0" cellspacing="0">
<tr>
<form action="'.$_SERVER['PHP_SELF'].'?do=update" method="post">
<td valign="top" width="20%">Voucher information text 1:<br>
<small>First line of info text shown in the voucher. Type a space for empty text.</small>
</td><td><input type="text" class="formstyle" name="vouchertext1" size="20" value="'.$s->GetSetting('vouchertext1').'"></td></tr>
<td valign="top">Voucher information text 2:<br>
<small>Second line of info text shown in the voucher. Type a space for empty text.</small>
</td><td><input type="text" class="formstyle" name="vouchertext2" size="20" value="'.$s->GetSetting('vouchertext2').'"></td></tr>
<td valign="top">Pre-form text:<br>
<small>This text is shown on the landing page above the form. Type a space for empty text.</small>
</td><td><input type="text" class="formstyle" name="pre-form-text" size="20" value="'.$s->GetSetting('pre-form-text').'"></td></tr>
<td valign="top">Post-form text:<br>
<small>This text is shown on the landing page below the form. Type a space for empty text.</small>
</td><td><input type="text" class="formstyle" name="post-form-text" size="20" value="'.$s->GetSetting('post-form-text').'"></td></tr>
<tr><td>Use verification keys:</td>';

if($s->GetSetting('use_verification')=='y')
{
	$veri_checked=' checked';
} else {
	$veri_checked='';
}

echo '<td><input type="checkbox" name="use_verification" value="y"'.$veri_checked.'></td></tr>
<tr><td><b>Use expiration date for voucher codes:</b></td>';

if($s->GetSetting('use_exp_date')=='y')
{
	$exp_checked=' checked';
} else {
	$exp_checked='';
}

echo '<td><input type="checkbox" name="use_exp_date" value="y"'.$exp_checked.'></td></tr>
</table>
<br>
<input type="submit" value="Save" class="formstyle">
</form>

<br><br>
Logo:<br>';

$logo=$s->GetSetting('logo');
if(file_exists('../graphics/'.$logo) && !is_dir('../graphics/'.$logo))
{
	echo '<img src="../graphics/'.$logo.'"><br><a href="'.$_SERVER['PHP_SELF'].'?do=del_logo">[Delete]</a>';
} else {
	echo '<i>No image defined or not found</i>';
}

echo '<br><br>
You can upload a new logo here. This will overwrite your existing logo.<br>
<form action="'.$_SERVER['PHP_SELF'].'?do=logo" method="post" enctype="multipart/form-data">
<input type="file" name="logo" class="formstyle"><br><br>
<input type="submit" value="Upload" class="formstyle">
</form>
</body></html>';