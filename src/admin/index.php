<?php
require('../classes/adminauth.php');
require('../classes/vouchermanager.php');

$a = new adminauth();
$v = new vouchermanager();

include('../includes/header.php');

echo '<center><b>Admin interface</b></center>
<br>
<ul>';

if($a->CheckPermission('add_voucher')) { echo '<a href="addvoucher.php">Add voucher(s)</a> - '; }
if($a->CheckPermission('view_users')) { echo '<a href="users.php">Manage user accounts</a> - '; }
if($a->CheckPermission('sys_config')) { echo '<a href="config.php">Configure system</a> - '; }

echo '</ul>';

?>
</body>
</html>