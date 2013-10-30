<?php
require('../classes/adminauth.php');

$a = new adminauth();
require('../classes/vouchermanager.php');
require('../classes/gui.php');
require('../classes/usermanager.php');
include('../includes/header.php');
include('menu.php');

$agui=new admingui();
$u=new usermanager();

if(!$a->CheckPermission('view_users'))
{
	echo '<center><b>You have no permission to edit users.</b></center></body></html>';
	die();
}

echo '<center><b>User list</b></center><br><br>';
$agui->ListUsers($u->GetUserlist());
?>
</body>
</html>
