<?php
chdir ('/var/www/localscripts/'); // Edit this path if needed

// Don't edit below!
include('../classes/vouchermanager.php');
$v = new vouchermanager();
$v->DropOldVouchers();
?>
