<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Mustache Wrapper
*
* Description:	A Wrapper for Mustache.php
*
*/

class Mustache
{
	private $_mustachio;


	// --------------------------------------------------------------------------


	public function __construct()
	{
		require NAILS_PATH . 'libraries/_resources/Mustache/Autoloader.php';
		Mustache_Autoloader::register();

		$this->_mustachio = new Mustache_Engine;
	}


	// --------------------------------------------------------------------------


	public function __call( $name, $arguments )
	{
		return call_user_func_array( array( $this->_mustachio, $name ), $arguments );
	}
}

/* End of file Mustache.php */
/* Location: ./libraries/Mustache.php */