<?php
define('CURRENTVER',0.110);
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