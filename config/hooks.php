<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files. Please see the user guide for info:
|
| http://codeigniter.com/user_guide/general/hooks.html
|
*/

	if ( is_file( FCPATH . APPPATH . 'modules/system/hooks/' ) ) :

		$_path = FCPATH . APPPATH . 'modules/system/hooks/';

	else :

		$_path = FCPATH . 'vendor/nailsapp/module-system/system/hooks/';

	endif;

	$hook['pre_system'] =	array(
								'class'		=> 'System_startup',
								'function'	=> 'init',
								'filename'	=> 'System_startup.php',
								'filepath'	=> $_path,
							);

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */