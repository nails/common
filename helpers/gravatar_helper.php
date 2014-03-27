<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* CodeIgniter
*
* An open source application development framework for PHP 4.3.2 or newer
*
* @package      CodeIgniter
* @author       Rick Ellis
* @copyright    Copyright (c) 2006, EllisLab, Inc.
* @license      http://www.codeigniter.com/user_guide/license.html
* @link         http://www.codeigniter.com
* @since        Version 1.0
* @filesource
*/

/**
* CodeIgniter Gravatar Helper
*
* @package      CodeIgniter
* @subpackage   Helpers
* @category     Helpers
* @author       Karl Ballard
* @url          http://codeigniter.com/forums/viewthread/56017/
*/

/**
* Gravatar
*
* Fetches a gravatar from the Gravatar website using the specified params
*
* @access  public
* @param   string
* @param   string
* @param   integer
* @param   string
* @return  string
*/
if ( ! function_exists( 'gravatar' ))
{
	function gravatar( $email, $data = FALSE )
	{
		if ( $data !== FALSE )
			$data = (object) $data;

		if ( empty( $email ) )
			return FALSE;

		//	Set defaults
		if ( ! isset( $data->return_img ) || empty( $data->return_img ) )
			$data->return_img = TRUE;

		if ( ! isset( $data->default ) || empty( $data->default ) )
			$data->default = 'mm';

		if ( ! isset( $data->size ) || empty( $data->size ) )
			$data->size = 80;

		if ( ! isset( $data->rating ) || empty( $data->rating ) )
			$data->rating = 'PG';

		if ( ! isset( $data->force_default ) || empty( $data->force_default ) )
			$data->force_default = FALSE;

		if ( ! isset( $data->img_title ) || empty( $data->img_title ) )
			$data->img_title = $email.'\'s Gravatar';

		if ( ! isset( $data->img_alt ) || empty( $data->img_alt ) )
			$data->img_alt = $data->img_title;

		$email_hash = md5( strtolower( trim( $email ) ) );

		$fd = ( $data->force_default === TRUE ) ? '&amp;forcedefault=y' : '';

		$image['src'] = ( isset( $_SERVER['HTTPS'] ) ? 'https://secure.' : 'http://' ) .
						 'gravatar.com/avatar/'.
						 $email_hash .
						 '?size='. $data->size .
						  '&amp;default='. $data->default .
						  '&amp;rating='. $data->rating . $fd;

		$image['class']		= 'gravatar_image';
		$image['title']		= $data->img_title;
		$image['alt']		= $data->img_alt;
		$image['width']		= $data->size;
		$image['height']	= $data->size;

		return ( $data->return_img === TRUE ) ? img( $image ) : $image['src'];
	}

}

/* End of file gravatar_helper.php */
/* Location: ./helpers/gravatar_helper.php */