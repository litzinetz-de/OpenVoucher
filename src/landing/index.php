<?php
include('../includes/header.php');
require('../classes/vouchermanager.php');

$v = new vouchermanager();

if($v->ClientAuthenticated())
{
	echo 'You are online.';
} else {
	echo 'Please add your voucher code in the form below to get internet access.<br><br>
	<form action="auth.php" method="post">
	Voucher code: <input type="text" name="vid" size="20" class="formstyle"><br><br>
	<input type="submit" value="OK" class="formstyle">
	</form>';
}
?>
</body>
</html>
