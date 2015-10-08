<?php

/**
 * This file provides langauge related helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

//  Include the CodeIgniter original
include 'vendor/rogeriopradoj/codeigniter/system/helpers/language_helper.php';

/**
 * Lang
 *
 * Overriding the helper to incorporate language parameters
 *
 * @access	public
 * @param	string	the language line
 * @param	array	the parameters to sub in
 * @param	string	the id of the form element
 * @return	string
 */
if ( ! function_exists('lang'))
{
	function lang($line, $params = NULL, $id = '')
	{
		$CI =& get_instance();
		$line = $CI->lang->line($line, $params);

		if ($id != '')
		{
			$line = '<label for="'.$id.'">'.$line."</label>";
		}

		return $line;
	}
}
