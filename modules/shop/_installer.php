<?php

/**
 * Name:		Shop Installer
 *
 * Description:	This file contains the installer for the Nails Shop module
 * 
 */

class Shop_installer
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
/* Location: ./modules/shop/_installer.php */