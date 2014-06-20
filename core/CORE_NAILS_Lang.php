<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Lang extends MX_Lang {

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
		if ( $params === NULL ) :

			return parent::line( $line );

		endif;

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


	// --------------------------------------------------------------------------


	public function load($langfile, $lang = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '', $_module = '')
	{
		//	Are we loading an array of languages? If so, handle each one on its own.
		if ( is_array( $langfile ) ) :

			foreach ( $langfile as $_lang ) :

				$this->load( $_lang );

			endforeach;

			return $this->language;

		endif;

		// --------------------------------------------------------------------------

		//	Determine which language we're using, if not specified, use the app's default
		$_default = CI::$APP->config->item('language');
		$idiom = ($lang == '') ? $_default : $lang;

		// --------------------------------------------------------------------------

		//	Check to see if the language file has already been loaded
		if ( in_array ($langfile.'_lang'.EXT, $this->is_loaded, TRUE ) ) :

			return $this->language;

		endif;

		// --------------------------------------------------------------------------

		//	Look for the language
		$_module OR $_module = CI::$APP->router->fetch_module();
		list($path, $_langfile) = Modules::find( $langfile.'_lang', $_module, 'language/'.$idiom.'/' );

		/**
		 *
		 * Confession. I'm not entirely sure how/why this works. Dumping out debug statements confuses
		 * me as they don't make sense, but the right lang files seem to be laoded. Sorry, future Pablo.
		 *
		 **/

		if ( $path === FALSE ) :

			//	File not found, fallback to the default language if not already using it
			if ( $idiom != $_default ) :

				//	Using MXs version seems to work as expected.
				if ( $lang = parent::load( $langfile, $_default, $return, $add_suffix, $alt_path ) ) :

					return $lang;

				endif;

			else :

				//	Not found within modules, try normal load()
				if ( $lang = CI_Lang::load($langfile, $idiom, $return, $add_suffix, $alt_path ) ) :

					return $lang;

				endif;

			endif;

		else :

			//	Lang file was found. Load it.
			if ( $lang = Modules::load_file($_langfile, $path, 'lang')) :

				if ($return) return $lang;
				$this->language = array_merge($this->language, $lang);
				$this->is_loaded[] = $langfile.'_lang'.EXT;
				unset($lang);

			endif;

		endif;

		// --------------------------------------------------------------------------

		return $this->language;
	}
}

/* End of file NAILS_Lang.php */
/* Location: ./application/core/NAILS_Lang.php */