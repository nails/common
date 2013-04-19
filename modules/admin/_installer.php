<?php

/**
 * Name:		Admin Installer
 *
 * Description:	This file contains the installer for the Nails Admin module
 * 
 */

require_once( dirname(__FILE__) . '/../_installer.php' );

class Admin_installer extends Module_installer
{
	public function run( $config )
	{
		//	Nothing to install
		return TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function dependencies( &$modules )
	{
		//	Admin is dependent on auth and CDN being available.
		if ( $this->has_module( 'auth', $modules ) && $this->has_module( 'cdn', $modules ) ) :
		
			return 'OK';
		
		else :
		
			$modules	= array();
			$modules[]	= 'auth';
			$modules[]	= 'cdn';
			
			return $modules;
			
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function fail_reason()
	{
		return FALSE;
	}
}

/* End of file _installer.php */
/* Location: ./modules/admin/_installer.php */