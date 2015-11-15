<?php
define('CURRENTVER','1.0.0');
define('RELYEAR',2015);

class versionmanager
{
	private $newestversion;
	
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
		$newest=explode('.',trim($this->NewestVersion()));
		$current=explode('.',trim(CURRENTVER));
		
		if($newest[0]>$current[0]) // Major
		{
			return true;
		} elseif($newest[1]>$current[1]) // Minor
		{
			return true;
		} elseif($newest[2]>$current[2]) // Revision
		{
			return true;
		} else {
			return false;
		}
	}
}
?>
