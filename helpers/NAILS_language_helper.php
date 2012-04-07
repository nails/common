<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


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



/* End of file NAILS_language_helper.php */
/* Location: ./application/helpers/NAILS_language_helper.php */