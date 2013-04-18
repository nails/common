<?php

/**
 * Name:		System Installer
 *
 * Description:	This file contains the installer for the Nails System module
 * 
 */

require_once( dirname(__FILE__) . '/../_installer.php' );

class System_installer extends Module_installer
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
/* Location: ./modules/system/_installer.php */