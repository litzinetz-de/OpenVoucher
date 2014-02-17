<?php
chdir ('/var/www/localscripts/');
include('../classes/vouchermanager.php');
$v = new vouchermanager();
$v->DropOldVouchers();
?>
