<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Lang extends MX_Lang {
	
	
	/**
	 *	Overloading this method so that if a lang file is supplied with the prefix of FCPATH then we
	 *	load that file directly rather than try and do anythign clever with the path
	 *
	 **/
	
	public function load($langfile, $lang = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '', $_module = '') {
	
		if ( strpos( $langfile, FCPATH ) === 0 ) :
		
			//	The supplied langfile is an absolute path, so use it.
			
			if (is_array($langfile)) {
				foreach($langfile as $_lang) $this->load($_lang);
				return $this->language;
			}
				
			$deft_lang = CI::$APP->config->item('language');
			$idiom = ($lang == '') ? $deft_lang : $lang;
		
			if (in_array($langfile.'_lang'.EXT, $this->is_loaded, TRUE))
				return $this->language;
			
			//$_module OR $_module = CI::$APP->router->fetch_module();
			//list($path, $_langfile) = Modules::find($langfile.'_lang', $_module, 'language/'.$idiom.'/');
			
			//	Add on .php if it's not there (so pathinfo() works as expected)
			if ( substr( $langfile, -4 ) != '.php' )
				$langfile .= '.php';
			
			//	Get path information about the langfile
			$_pathinfo	 = pathinfo( $langfile );
			$_path		= $_pathinfo['dirname'] . '/';
			$_langfile	=  $_pathinfo['filename'] . '_lang';
			
			if ($_path === FALSE) {
				
				if ($lang = parent::load($langfile, $lang, $return, $add_suffix, $alt_path)) return $lang;
			
			} else {
	
				if($lang = Modules::load_file($_langfile, $_path, 'lang')) {
					if ($return) return $lang;
					$this->language = array_merge($this->language, $lang);
					$this->is_loaded[] = $langfile.'_lang'.EXT;
					unset($lang);
				}
			}
			
			return $this->language;
		
		else :
		
			//	Fall back to the old method
			return parent::load( $langfile, $lang, $return, $add_suffix, $alt_path, $_module );
			
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Overriding the default line() method so that parameters can be specified
	 *
	 * @access	public
	 * @param	string	$line	the language line
	 * @param	array	$params	any parameters to sub in
	 * @return	string
	 */
	public function line( $line = '', $params = NULL )
	{
		if ( is_null( $params ) )
			return parent::line( $line );
		
		//	We have some parameters, sub 'em in or the unicorns will die.
		$line = parent::line( $line );
		
		if ( $line !== FALSE ) :
		
			if ( is_array( $params ) ) :
			
				$line = vsprintf( $line, $params );
				
			else :
			
				$line = sprintf( $line, $params );
				
			endif;
			
		endif;
		
		return $line;
	}
}

/* End of file NAILS_Lang.php */
/* Location: ./application/core/NAILS_Lang.php */