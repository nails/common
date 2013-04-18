<?php

/**
 * Name:		Module Installer
 *
 * Description:	This file contains generic methods for the module installers
 * 
 */

class Module_installer
{
	protected function has_module( $needle, $haystack )
	{
		foreach ( $haystack AS $module ) :
		
			if ( preg_match( '/^' . $needle . '(\[.*\])?$/', $module ) ) :
			
				return TRUE;
			
			else :
			
				return FALSE;
			
			endif;
		
		endforeach;
	}
}