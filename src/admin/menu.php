<?php
echo '<center><div class="small">Admin interface</div></center>
<br>
<ul><a href="index.php">Home / View Vouchers</a> - ';

if($a->CheckPermission('add_voucher')) { echo '<a href="addvoucher.php">Issue voucher(s)</a> - '; }
if($a->CheckPermission('view_users')) { echo '<a href="users.php">Manage user accounts</a> - '; }
//if($a->CheckPermission('sys_config')) { echo '<a href="config.php">Configure system</a> - '; } // This will be available in the next version

echo '<a href="about.php">About / Version</a> - <a href="logout.php">Logout</a>
</ul><br>';
?>