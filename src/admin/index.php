<?php
require('../classes/adminauth.php');
require('../classes/vouchermanager.php');

$a = new adminauth();
$v = new vouchermanager();

include('../includes/header.php');

include('menu.php');

echo '<table width="80%" border="0">
<tr cass="tableheader">
<td width="20%">Voucher ID</td>
<td width="20%">Valid until</td>
<td width="20%">Connected devices</td>
<td width="20%">Drop devices</td>
<td width="20%">Drop voucher</td>
</tr>';

// List all vouchers
if($a->CheckPermission('view_voucher'))
{
	
}

?>
</body>
</html>