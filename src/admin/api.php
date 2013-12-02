<?xml version="1.0" encoding="UTF-8"?>
<?php
require('../classes/adminauth.php');
require('../classes/systemmanager.php');
require('../classes/usermanager.php');
require('../classes/versionmanager.php');
require('../classes/vouchermanager.php');

$auth = new adminauth('api');
$systemmanager = new systemmanager();
$usermanager = new usermanager();
$versionmanager = new versionmanager();
$vouchermanager = new vouchermanager();
?>