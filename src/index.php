<?php

$temp=explode('/',$_SERVER['SCRIPT_NAME']);
unset($temp[count($temp)-1]);
$path=join('/',$temp);

header("Content-Type: application/json");
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

header('Location: '.$path.'/landing/');
die();
?>