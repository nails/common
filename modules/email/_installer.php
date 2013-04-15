<?php

/**
 * Name:		Email Installer
 *
 * Description:	This file contains the installer for the Nails Email module
 * 
 */

class Email_installer
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
/* Location: ./modules/email/_installer.php */