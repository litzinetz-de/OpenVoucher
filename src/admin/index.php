<?php
require('../classes/adminauth.php');
require('../classes/vouchermanager.php');
require('../classes/gui.php');

$a = new adminauth();
$v = new vouchermanager();
$agui = new admingui();

include('../includes/header.php');

include('menu.php');

echo '<center><b>Active vouchers:</b></center><br>';

// List all vouchers
if($a->CheckPermission('view_voucher'))
{
	$agui->ListVouchers($v->GetVoucherList());
} else {
	echo '<center><i>You are now allowed to view vouchers.</i></center>';
}

?>
</body>
</html>