<?php
include('../includes/header.php');
require('../classes/vouchermanager.php');
require('../classes/systemmanager.php');

$v = new vouchermanager();
$clientdata=$v->ClientAuthenticated();

$s = new systemmanager();

if($clientdata!='noauth')
{
	echo 'You are online and can now browse the internet.<br><br>
	Your voucher ID is: '.$clientdata[2].'<br>';
	
	$vinfo=$v->GetVoucherInfo($clientdata[2]);
	
	echo 'Your voucher is valid until '.date('Y-m-d H:i',$vinfo['valid_until']).' and you can register '.$vinfo['remain'].' more device(s) with this voucher.';
	
} else {
	echo $s->GetSetting('pre-form-text').'<br><br>
	<form action="auth.php" method="post">
	Voucher code: <input type="text" name="vid" size="20" class="formstyle"><br><br>
	<input type="submit" value="OK" class="formstyle">
	</form><br><br>'.$s->GetSetting('post-form-text');
}
?>
</body>
</html>
