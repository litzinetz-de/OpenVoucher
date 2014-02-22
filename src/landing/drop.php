<?php

require('../classes/vouchermanager.php');
require('../classes/systemmanager.php');

$v = new vouchermanager();
$s = new systemmanager();

$authtype=$v->GetAuthMethod();

require('../includes/header.php');

if($_GET['do']=='')
{
	echo 'To drop a device, please enter your voucher code again:<br><br>
	<form action="drop.php?do=lst-devices" method="post">
	Voucher code: <input type="text" name="vid" size="20" class="formstyle">';
	
	if($s->GetSetting('use_verification')=='y')
	{
		echo '<br>Verification key: <input type="text" name="verification_key" size="20" class="formstyle">';
	}
	
	echo '<br><br>
	<input type="submit" value="Next" class="formstyle">';
}

if($_GET['do']=='lst-devices')
{
	if($s->GetSetting('use_verification')=='y')
	{
		if(!$v->VerifyVoucherKey($_POST['vid'],$_POST['verification_key']))
		{
			echo 'The verification key is invalid. Please go <a href="javascript:history.back();">back</a> and try again.</body></html>';
			die();
		}
	}
	
	$voucher_info=$v->GetVoucherInfo($_POST['vid']);
	$devices=$v->GetDeviceList($_POST['vid']);
	
	echo 'Device count: '.$voucher_info['dev_count'].' ('.$voucher_info['remain'].' left)<br>
	Valid until: '.date('Y-m-d H:i',$voucher_info['valid_until']).'<br><br>Choose one of these devices to drop:<br>';
	
	foreach($devices as $device)
	{
		echo '<ul><a href="'.$_SERVER['PHP_SELF'].'?do=drop&vid='.$_POST['vid'].'&verification_key='.$_POST['verification_key'].'&device='.$device.'">'.$addr.'</a></ul>';
	}
}

if($_GET['do']=='drop')
{
	if($s->GetSetting('use_verification')=='y')
	{
		if(!$v->VerifyVoucherKey($_GET['vid'],$_GET['verification_key']))
		{
			echo 'The verification key is invalid. Please go <a href="'.$_SERVER['PHP_SELF'].'">back</a> and try again.</body></html>';
			die();
		}
	}
	
	if(!isset($_GET['device']))
	{
		echo 'Couldn\'t get device.</body></html>';
		die();
	}
	if(filter_var($_GET['addr'], FILTER_VALIDATE_IP))
	{
		$type='ipv4';
	} else {
		$type='mac';
	}
	$v->DropDevice($type,$_GET['addr']);
}

?>
</body>
</html>
