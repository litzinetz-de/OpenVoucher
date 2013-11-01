<?php
require('../classes/adminauth.php');
require('../classes/versionmanager.php');

$a = new adminauth();
$vers = new versionmanager();

include('../includes/header.php');

include('menu.php');

echo 'OpenVoucher<br>Version '.$vers->GetCurrentVersion().'<br><br>
Copyright (C) '.$vers->GetReleaseYear().'  Daniel Litzbach (litzi0815) and others<br><br>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.<br><br>

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.<br><br>

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <a href="http://www.gnu.org/licenses/" target="_blank">http://www.gnu.org/licenses/</a>.
	<br><br>
	The OpenVoucher logo is based on the work by <a href="http://commons.wikimedia.org/wiki/User:RRZEicons" target="_blank">RRZEicons</a>.';
?>
</body>
</html>