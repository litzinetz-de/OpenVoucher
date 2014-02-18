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
}

?>
</body>
</html>