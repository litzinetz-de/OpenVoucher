<?php
define('CURRENTVER',0.210);
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
}
?>
