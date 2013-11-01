<?php
include('/var/www/classes/vouchermanager.php');
$v = new vouchermanager();
$v->DropOldVouchers();
?>