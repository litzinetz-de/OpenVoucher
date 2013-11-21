<?php
define('CURRENTVER','0.3.3');
define('RELYEAR',2013);

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
	
	public function NewestVersion()
	{
		if(!isset($this->newestversion))
		{
			$this->newestversion=file_get_contents('http://www.openvoucher.org/ov-data/newestver.txt');
		}
		return $this->newestversion;
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
