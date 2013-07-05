<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_URI extends CI_URI
{
	/**
	 * Overridding _set_uri_string so that the URI is always interpreted as lowercase.
	 * Prevents a bug where fatal errors occur when the URI is in capitals.
	 *
	 * @access	public
	 * @param	string	$str The URI string
	 * @return	void
	 */
	function _set_uri_string($str)
	{
		return parent::_set_uri_string( strtolower( $str ) );
	}
}