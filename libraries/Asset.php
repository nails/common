<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Asset
*
* Description:	A Library to make the loading (and unloading) of CSS and JS assets a breeze
*
*/

class Asset
{

	private $CI;
	private $unload_assets;

	private $css				= array();
	private $css_nails			= array();
	private $css_nails_bower	= array();
	private $css_inline			= array();

	private $js					= array();
	private $js_nails			= array();
	private $js_nails_bower		= array();
	private $js_inline			= array();


	// --------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function __construct()
	{
		$this->CI =& get_instance();
	}


	// --------------------------------------------------------------------------


	/**
	 * Load an asset
	 *
	 * @access	public
	 * @param	string
 	 * @param	boolean
 	 * @param	boolean
	 * @return	void
	 **/
	public function load( $assets, $nails_asset = FALSE, $force_type = FALSE )
	{
		$assets = ! is_array( $assets ) && ! is_object( $assets ) ? array( $assets ) : $assets ;

		// --------------------------------------------------------------------------

		if ( $nails_asset === TRUE ) :

			$this->_load_nails( $assets );

		elseif ( strtoupper( $nails_asset ) === 'BOWER' ) :

			$this->_load_nails_bower( $assets );

		else :

			$this->_load_app( $assets );

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Load an app asset
	 *
 	 * @access	private
	 * @param array $assets An array of assets to load
	 * @return void
	 **/
	private function _load_app( $assets )
	{
		foreach ( $assets AS $asset ) :

			$_type = $this->_determine_type( $asset );

			switch ( $_type ) :

				case 'CSS' :	$this->css[$asset]	= $asset;	break;
				case 'JS' :		$this->js[$asset]	= $asset;	break;

			endswitch;

		endforeach;
	}


	// --------------------------------------------------------------------------


	/**
	 * Load a Nails asset
	 *
 	 * @access	private
	 * @param array $assets An array of assets to load
	 * @return void
	 **/
	private function _load_nails( $assets )
	{
		foreach ( $assets AS $asset ) :

			$_type = $this->_determine_type( $asset );

			switch ( $_type ) :

				case 'CSS' :	$this->css_nails[$asset]	= $asset;	break;
				case 'JS' :		$this->js_nails[$asset]		= $asset;	break;

			endswitch;

		endforeach;
	}


	// --------------------------------------------------------------------------


	/**
	 * Load a Nails bower asset
	 *
	 * @access	private
	 * @param array $assets An array of assets to load
	 * @return void
	 **/
	private function _load_nails_bower( $assets )
	{
		foreach ( $assets AS $asset ) :

			$_type = $this->_determine_type( $asset );

			switch ( $_type ) :

				case 'CSS' :	$this->css_nails_bower[$asset]	= $asset;	break;
				case 'JS' :		$this->js_nails_bower[$asset]	= $asset;	break;

			endswitch;

		endforeach;
	}


	// --------------------------------------------------------------------------


	/**
	 * Load a library (collection of assets)
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 **/
	public function library( $library )
	{
		switch( $library ) :

			case 'ckeditor' :

				//	Load assets for CKEditor
				$this->load( 'libraries/ckeditor/ckeditor.js', TRUE );
				$this->load( 'libraries/ckeditor/adapters/jquery.js', TRUE );

			break;

			// --------------------------------------------------------------------------

			case 'jqueryui' :

				$this->load( 'jquery.ui.min.js', TRUE );
				$this->load( 'jquery.ui.datetimepicker.min.js', TRUE );

			break;

			// --------------------------------------------------------------------------

			case 'ibutton' :

				$this->load( 'libraries/jquery.ibutton/lib/jquery.ibutton.min.js', TRUE );

			break;

			// --------------------------------------------------------------------------

			case 'uploadify' :

				$this->load( 'jquery.uploadify.min.js', TRUE );

			break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	/**
	 * Mark an asset for unloading
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 **/
	public function unload( $assets )
	{
		$assets = ! is_array( $assets ) && ! is_object( $assets ) ? array( $assets ) : $assets ;

		foreach ( $assets AS $asset ) :

			$this->unload_assets[$asset] = $asset;

		endforeach;
	}


	// --------------------------------------------------------------------------


	/**
	 * Load an inline asset
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 **/
	public function inline( $script = NULL )
	{
		if ( NULL === $script ) :

			return;

		endif;

		// --------------------------------------------------------------------------

		$_type = $this->_determine_type( $script );

		switch ( $_type ) :

			case 'CSS_INLINE' :	$this->css_inline[]	= $script;	break;
			case 'JS_INLINE' :	$this->js_inline[]	= $script;	break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	/**
	 * Clear loaded assets
	 *
	 * @access	public
	 * @param	boolean
	 * @param	boolean
	 * @param	boolean
	 * @param	boolean
	 * @param	boolean
	 * @param	boolean
	 * @param	boolean
	 * @param	boolean
	 * @return	void
	 **/
	public function clear( $css = FALSE, $css_nails = FALSE, $css_nails_bower = FALSE, $css_inline = TRUE, $js = FALSE, $js_nails = FALSE, $js_nails_bower = FALSE, $js_inline = TRUE )
	{
		//	CSS
		if ( $css === TRUE ) :

			$this->css = array();

		endif;

		if ( $css_nails === TRUE ) :

			$this->css_nails = array();

		endif;

		if ( $css_nails_bower === TRUE ) :

			$this->css_nails_bower = array();

		endif;

		if ( $css_inline === TRUE ) :

			$this->css_inline = array();

		endif;

		//	JS
		if ( $js === TRUE ) :

			$this->js = array();

		endif;

		if ( $js_nails === TRUE ) :

			$this->js_nails = array();

		endif;

		if ( $js_nails_bower === TRUE ) :

			$this->js_nails_bower = array();

		endif;

		if ( $js_inline === TRUE ) :

			$this->js_inline = array();

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Clears all loaded assets
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function clear_all()
	{
		$this->clear( TRUE, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE );
	}


	// --------------------------------------------------------------------------


	/**
	 * Return an object with the currently loaded objects, useful for debugging
	 *
	 * @access	public
	 * @return	object
	 **/
	public function get_loaded()
	{
		$_loaded					= new stdClass();
		$_loaded->css				= $this->css;
		$_loaded->css_nails			= $this->css_nails;
		$_loaded->css_nails_bower	= $this->css_nails_bower;
		$_loaded->css_inline		= $this->css_inline;

		$_loaded->js				= $this->js;
		$_loaded->js_nails			= $this->js_nails;
		$_loaded->js_nails_bower	= $this->js_nails_bower;
		$_loaded->js_inline			= $this->js_inline;

		return $_loaded;
	}


	// --------------------------------------------------------------------------


	/**
	 * Output the assets for HTML
	 *
	 * @access	public
	 * @param	string
	 * @param	boolean
	 * @return	object
	 **/
	public function output( $type = 'ALL', $return = FALSE )
	{
		//	Unload anything first
		if ( count ( $this->unload_assets ) ) :

			foreach ( $this->unload_assets AS $asset) :

				//	CSS
				unset( $this->css[$asset] );
				unset( $this->css_nails[$asset] );
				unset( $this->css_nails_bower[$asset] );

				//	JS
				unset( $this->js[$asset] );
				unset( $this->js_nails[$asset] );
				unset( $this->js_nails_bower[$asset] );

			endforeach;

		endif;

		// --------------------------------------------------------------------------

		//	Now output
		$_out = '';
		switch ( strtoupper( $type ) ) :

			case 'CSS' :

				$_out .= $this->_print_css_nails_bower();
				$_out .= $this->_print_css_nails();
				$_out .= $this->_print_css();

			break;

			case 'CSS-INLINE' :

				$_out  = $this->_print_css_inline();

			break;

			case 'JS' :

				$_out .= $this->_print_js_nails_bower();
				$_out .= $this->_print_js_nails();
				$_out .= $this->_print_js();

			break;

			case 'JS-INLINE' :

				$_out .= $this->_print_js_inline();

			break;

			case 'ALL' :

				$_out .= $this->_print_css_nails_bower();
				$_out .= $this->_print_css_nails();
				$_out .= $this->_print_css();
				$_out .= $this->_print_js_nails_bower();
				$_out .= $this->_print_js_nails();
				$_out .= $this->_print_js();

			break;

		endswitch;

		// --------------------------------------------------------------------------

		//	Force SSL for assets if running on non-standard port
		if ( page_is_secure() ) :

			$_out = str_replace( BASE_URL, SECURE_BASE_URL, $_out );

		endif;

		// --------------------------------------------------------------------------

		if ( $return ) :

			return $_out;

		else :

			echo $_out;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Determine the type of asset being loaded
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	string
	 **/
	private function _determine_type( $input, $force_type = FALSE )
	{
		//	Override if nessecary
		if ( $force_type ) :

			return $force_type;

		endif;

		//	Look for <style></style>
		if ( preg_match( '/\<style.*\<\/style\>/si', $input ) ) :

			return 'CSS_INLINE';

		endif;

		//	Look for <script></script>
		if ( preg_match( '/\<script.*\<\/script\>/si', $input ) ) :

			return 'JS_INLINE';

		endif;

		//	Look for .css
		if ( substr( $input, strrpos( $input, '.' ) ) == '.css' ) :

			return 'CSS';

		endif;

		//	Look for .js
		if ( substr( $input, strrpos( $input, '.' ) ) == '.js' ) :

			return 'JS';

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Output the referenced CSS files
	 *
	 * @access	private
	 * @param	none
	 * @return	string
	 **/
	private function _print_css()
	{
		$_out = '';

		$this->css = array_filter( $this->css );
		$this->css = array_unique( $this->css );

		foreach ( $this->css AS $asset ) :

			$_url  = preg_match( '/[http|https|ftp]:\/\/.*/si', $asset ) ? $asset : 'assets/css/' . $asset ;
			$_out .= link_tag( $_url ) . "\n";

		endforeach;

		return $_out;
	}


	// --------------------------------------------------------------------------


	/**
	 * Output the referenced Nails CSS files
	 *
	 * @access	private
	 * @param	none
	 * @return	string
	 **/
	private function _print_css_nails()
	{
		$_out = '';

		$this->css_nails = array_filter( $this->css_nails );
		$this->css_nails = array_unique( $this->css_nails );

		foreach ( $this->css_nails AS $asset ) :

			$_out .= link_tag( NAILS_ASSETS_URL . 'css/' . $asset ) . "\n";

		endforeach;

		return $_out;
	}


	// --------------------------------------------------------------------------


		/**
	 * Output the referenced Nails bower CSS files
	 *
	 * @access	private
	 * @param	none
	 * @return	string
	 **/
	private function _print_css_nails_bower()
	{
		$_out = '';

		$this->css_nails_bower = array_filter( $this->css_nails_bower );
		$this->css_nails_bower = array_unique( $this->css_nails_bower );

		foreach ( $this->css_nails_bower AS $asset ) :

			$_out .= link_tag( NAILS_ASSETS_URL . 'bower_components/' . $asset ) . "\n";

		endforeach;

		return $_out;
	}


	// --------------------------------------------------------------------------


	/**
	 * Output the inline CSS
	 *
	 * @access	private
	 * @param	none
	 * @return	string
	 **/
	private function _print_css_inline()
	{
		$_out = '';

		$this->css_inline = array_filter( $this->css_inline );
		$this->css_inline = array_unique( $this->css_inline );

		foreach ( $this->css_inline AS $asset ) :

			$_out .= $asset . "\n";

		endforeach;

		$_out = preg_replace( '/<\/?style.*?>/si', '', $_out );

		return '<style type="text/css">' . $_out . '</style>';
	}


	// --------------------------------------------------------------------------


	/**
	 * Output the referenced JS files
	 *
	 * @access	private
	 * @param	none
	 * @return	string
	 **/
	private function _print_js()
	{
		$_out = '';

		$this->js = array_filter( $this->js );
		$this->js = array_unique( $this->js );

		foreach ( $this->js AS $asset ) :

			$_url  = preg_match( '/[http|https|ftp]:\/\/.*/si', $asset ) ? $asset : site_url( 'assets/js/' . $asset ) ;
			$_out .= '<script type="text/javascript" src="' . $_url . '"></script>' . "\n";

		endforeach;

		return $_out;
	}


	// --------------------------------------------------------------------------


	/**
	 * Output the referenced Nails JS files
	 *
	 * @access	private
	 * @param	none
	 * @return	string
	 **/
	private function _print_js_nails()
	{
		$_out = '';

		$this->js_nails = array_filter( $this->js_nails );
		$this->js_nails = array_unique( $this->js_nails );

		foreach ( $this->js_nails AS $asset ) :

			$_out .= '<script type="text/javascript" src="' . NAILS_ASSETS_URL . 'js/' . $asset . '"></script>' . "\n";

		endforeach;

		return $_out;
	}


	// --------------------------------------------------------------------------


	/**
	 * Output the referenced Nails Bower JS files
	 *
	 * @access	private
	 * @param	none
	 * @return	string
	 **/
	private function _print_js_nails_bower()
	{
		$_out = '';

		$this->js_nails_bower = array_filter( $this->js_nails_bower );
		$this->js_nails_bower = array_unique( $this->js_nails_bower );

		foreach ( $this->js_nails_bower AS $asset ) :

			$_out .= '<script type="text/javascript" src="' . NAILS_ASSETS_URL . 'bower_components/' . $asset . '"></script>' . "\n";

		endforeach;

		return $_out;
	}


	// --------------------------------------------------------------------------


	/**
	 * Output the inline JS files
	 *
	 * @access	private
	 * @param	none
	 * @return	string
	 **/
	private function _print_js_inline()
	{
		$_out = '';

		$this->js_inline = array_filter( $this->js_inline );
		$this->js_inline = array_unique( $this->js_inline );

		foreach ( $this->js_inline AS $asset ) :

			$_out .= $asset . "\n";

		endforeach;

		$_out = preg_replace( '/<\/?script.*?>/si', '', $_out );

		return '<script type="text/javascript">' . $_out . '</script>';
	}
}

/* End of file asset.php */
/* Location: ./application/libraries/asset.php */