<?php
require('../classes/adminauth.php');

$a = new adminauth();
if(!$a->CheckPermission('drop_device'))
{
	require('../classes/gui.php');
	$agui = new admingui();
	include('../includes/header.php');
	include('menu.php');
	echo '<center><b>You have no permission to drop devices.</b></center></body></html>';
	die();
}

require('../classes/vouchermanager.php');
$v = new vouchermanager();
$v->DropDevice($_GET['type'],$_GET['addr']);
header('Location: index.php');

?>