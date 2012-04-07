<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_User_agent extends CI_User_agent {

	/**
	 * from_string
	 *
	 * Compiles the user agent from a supplied string
	 *
	 * @access	public
	 * @param	string	$str	The string to compile
	 * @return	void
	 */
	public function from_string( $str = FALSE )
	{
		//	Get platform info
		$_platform = $this->_get_platform( $str );
		
		//	Get browser info
		$_browser = $this->_get_browser( $str );
		
		//	Set output
		$_out->platform	= $_platform;
		$_out->browser	= $_browser['browser'];
		$_out->version	= $_browser['version'];
		
		return $_out;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * _get_browser
	 *
	 * Gets the browser (modified copy of parent method: _set_browser)
	 *
	 * @access	public
	 * @param	string	$agent	The string to compile
	 * @return	array
	 */
	private function _get_browser( $agent )
	{
		if (is_array($this->browsers) AND count($this->browsers) > 0)
		{
			foreach ($this->browsers as $key => $val)
			{
				if (preg_match("|".preg_quote($key).".*?([0-9\.]+)|i", $agent, $match))
				{
					$_out['version'] = $match[1];
					$_out['browser'] = $val;
					return $_out;
				}
			}
		}
		return FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * _get_platform
	 *
	 * Gets the platform (modified copy of parent method: _set_platform)
	 *
	 * @access	public
	 * @param	string	$agent	The string to compile
	 * @return	string
	 */
	private function _get_platform( $agent )
	{
		if (is_array($this->platforms) AND count($this->platforms) > 0)
		{
			foreach ($this->platforms as $key => $val)
			{
				if (preg_match("|".preg_quote($key)."|i", $agent))
				{
					return $val;
				}
			}
		}
		return 'Unknown Platform';
	}
}

/* End of file NAILS_User_agent.php */
/* Location: ./application/core/NAILS_User_agent.php */