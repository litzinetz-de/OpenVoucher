<?php

$temp=explode('/',$_SERVER['SCRIPT_NAME']);
unset($temp[count($temp)-1]);
$path=join('/',$temp);

header('Location: '.$path.'/landing/');
die();
?>