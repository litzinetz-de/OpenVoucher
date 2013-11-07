<?php
define('CURRENTVER',0.212);
define('RELYEAR',2013);

class versionmanager
{
	public function GetCurrentVersion()
	{
		return CURRENTVER;
	}
	
	public function GetReleaseYear()
	{
		return RELYEAR;
	}
	
	public function UpdateAvailable()
	{
		$newestver=file_get_contents('http://www.openvoucher.org/ov-data/newestver.txt');
		if($newestver>CURRENTVER)
		{
			return true;
		} else {
			return false;
		}
	}
}
?>
