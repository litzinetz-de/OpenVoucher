<?php
require('../classes/vouchermanager.php');

$v = new vouchermanager();

$authtype=$v->GetAuthMethod();

if($authtype=='mac-only')
{
	// MAC
	$mac=$v->GetClientMAC();
	if($mac!='')
	{
		$res=$v->AuthDevice($_POST['vid'],'mac',$mac);
		if($res!='ok')
		{
			$auth_error=$res;
		}
	} else {
		$auth_error='no-mac';
	}
} elseif($authtype=='mac-ipv4')
{
	// MAC and IP4v fallback
} elseif($authtype=='ipv4')
{
	// IPv4 only
} else {
	$auth_error='no-auth-method';
}

if($auth_error!='')
{
	include('../includes/header.php');
	
	echo 'There has been a Problem authenticating your device.<br><br>';
	
	if($auth_error=='no-mac') { echo 'I wasn\'t able to read your device\'n network address. Please contact an administrator.'; }
	if($auth_error=='no-auth-method') { echo 'I wasn\'t able to read the authentication method from the config. Please contact an administrator.'; }
} else {
	header('Location: index.php');
}
?>