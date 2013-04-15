<?php

/**
 * Name:		Auth Installer
 *
 * Description:	This file contains the installer for the Nails Auth module
 * 
 */

class Auth_installer
{
	public function run( $config )
	{
		//	Nothing to install
		return TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function dependencies( $modules )
	{
		//	No dependencies
		return TRUE;
	}
}

/* End of file _installer.php */
/* Location: ./modules/auth/_installer.php */