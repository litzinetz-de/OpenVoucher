<?php
include('/var/www/localscripts/classes/vouchermanager.php');
$v = new vouchermanager();
$v->DropOldVouchers();
?>