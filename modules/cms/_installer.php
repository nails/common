<?php

/**
 * Name:		CMS Installer
 *
 * Description:	This file contains the installer for the Nails CMS module
 * 
 */

require_once( dirname(__FILE__) . '/../_installer.php' );

class Cms_installer extends Module_installer
{
	public function run( $config )
	{
		//	Nothing to install
		return TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function dependencies( &$modules )
	{
		//	No dependencies
		return 'OK';
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function fail_reason()
	{
		return FALSE;
	}
}

/* End of file _installer.php */
/* Location: ./modules/cms/_installer.php */