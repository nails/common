<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_URI extends CI_URI
{
	function _set_uri_string($str)
	{
		return parent::_set_uri_string( strtolower( $str ) );
	}
}