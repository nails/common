<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * str_lreplace
 *
 * Replace the last occurance of a string within a string with a string
 *
 * @access	public
 * @param	string	The item to replace
 * @param	string	The string to replace with
 * @param	string	The string to search
 * @return	string
 */
if ( ! function_exists('str_lreplace'))
{
	function str_lreplace( $search, $replace, $subject )
	{
		$pos = strrpos( $subject, $search );
		
		if( $pos !== FALSE ) :
		
			$subject = substr_replace( $subject, $replace, $pos, strlen( $search ) );
			
		endif;
		
		return $subject;
	}
}



/* End of file NAILS_string_helper.php */
/* Location: ./helpers/NAILS_string_helper.php */