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
}

echo '<table border="0" cellspacing="0">
<tr>
<form action="'.$_SERVER['PHP_SELF'].'?do=update" method="post">
<td valign="top" width="20%">Voucher information text 1:<br>
<small>First line of info text shown in the voucher</small>
</td><td><input type="text" class="formstyle" name="vouchertext1" size="20" value="'.$s->GetSetting('vouchertext1').'"></td></tr>
<td valign="top">Voucher information text 2:<br>
<small>Second line of info text shown in the voucher</small>
</td><td><input type="text" class="formstyle" name="vouchertext2" size="20" value="'.$s->GetSetting('vouchertext2').'"></td></tr>

</table>
<br>
<input type="submit" value="Save" class="formstyle">
</form></body></html>';