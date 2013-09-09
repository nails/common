<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Asset
*
* Description:	A Library to make the loading (and unloading) of CSS and JS assets a breeze
*
*/

class Asset {

	private $CI;
	private $unload_assets;

	private $css			= array();
	private $css_nails		= array();
	private $css_inline		= array();

	private $js				= array();
	private $js_nails		= array();
	private $js_inline		= array();


	// --------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
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
	 * @author	Pablo
	 **/
	public function load( $assets, $nails_asset = FALSE, $force_type = FALSE )
	{
		$assets = ( ! is_array($assets) && ! is_object($assets) ) ? array( $assets ) : $assets ;

		//	If it's nails assets put them elewhere and finish execution
		if ( $nails_asset === TRUE ) :

			$this->_load_nails( $assets );
			return;

		endif;

		foreach ( $assets AS $asset ) :

			//	Allow autoload items to unload an already loaded item
			//	This functionality implemented to allow the developer to define an always loaded item
			//	but then unload it within a certain module if needed - usually only useful if system
			//	modules have conflicts with app assets.

			if ( preg_match( '/unload:(.+)/', $asset, $match ) ) :

				$this->unload( $match[1] );

			else :

				$type = $this->_determine_type( $asset );

				switch ($type ) :

					case 'css':	$this->css[$asset]	= $asset;	break;
					case 'js':	$this->js[$asset]	= $asset;	break;

				endswitch;

			endif;

		endforeach;
	}


	// --------------------------------------------------------------------------


	/**
	 * Load a library (collection of assets)
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 * @author	Pablo
	 **/
	public function library( $library )
	{
		switch( $library ) :

			case 'ckeditor' :

				//	Load assets for CKEditor
				$this->load( 'libraries/ckeditor/ckeditor.js', TRUE );

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

			case 'nails_api' :

				//	Generate the api credentials

				//	Load the assets
				$this->load( 'nails.api.min.js', TRUE );

				//	Inline assets
				$_js  = '$(function(){';

				$_js .= 'if ( typeof( NAILS_API ) === \'function\' ){';
				$_js .= 'window.NAILS.API = new NAILS_API();';
				$_js .= 'window.NAILS.API.init( \'\', \'\' );}';

				$_js .= '});';

				$this->inline( '<script>' . $_js . '</script>' );

			break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	/**
	 * Load a nails asset
	 *
	 * @access	private
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	private function _load_nails( $assets )
	{
		foreach ( $assets AS $asset ) :

			//	Allow autoload items to unload an already loaded item
			//	This functionality implemented to allow the developer to define an always loaded item
			//	but then unload it within a certain module if needed - usually only useful if system
			//	modules have conflicts with app assets.

			if ( preg_match( '/unload:(.+)/', $asset, $match ) ) :

				$this->unload( $match[1] );

			else :

				$type = $this->_determine_type( $asset );

				switch ( $type ) :

					case 'css':	$this->css_nails[$asset]	= $asset;	break;
					case 'js':	$this->js_nails[$asset]	= $asset;	break;

				endswitch;

			endif;

		endforeach;
	}


	// --------------------------------------------------------------------------


	/**
	 * Mark an asset for unloading
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 * @author	Pablo
	 **/
	public function unload( $assets )
	{
		$assets = ( ! is_array($assets) && ! is_object($assets) ) ? array( $assets ) : $assets ;

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
	 * @author	Pablo
	 **/
	public function inline( $script = NULL )
	{
		if ( is_null( $script ) ) return;

		$type = $this->_determine_type( $script );
		switch ( $type ) :
			case 'css_inline':	$this->css_inline[]	= $script;	break;
			case 'js_inline':	$this->js_inline[]	= $script;	break;
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
	 * @author	Pablo
	 **/
	public function clear(	$css = FALSE, $css_nails = FALSE,
							$css_inline = TRUE, $js = FALSE, $js_nails = FALSE,
							$js_inline = TRUE )
	{
		//	CSS
		if ( $css === TRUE )
			$this->css = array();

		if ( $css_nails === TRUE )
			$this->css_nails = array();

		if ( $css_inline === TRUE )
			$this->css_inline = array();

		//	JS
		if ( $js === TRUE )
			$this->js = array();

		if ( $js_nails === TRUE )
			$this->js_nails = array();

		if ( $js_inline === TRUE )
			$this->js_inline = array();
	}


	// --------------------------------------------------------------------------


	/**
	 * Clears all loaded assets
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function clear_all()
	{
		$this->clear( TRUE, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE );
	}


	// --------------------------------------------------------------------------


	/**
	 * Return an object with the currently loaded objects, useful for debugging
	 *
	 * @access	public
	 * @return	object
	 * @author	Pablo
	 **/
	public function get_loaded()
	{
		$loaded				= new stdClass();
		$loaded->css		= $this->css;
		$loaded->js			= $this->js;
		$loaded->css_inline	= $this->css_inline;
		$loaded->js_inline	= $this->js_inline;
		return $loaded;
	}


	// --------------------------------------------------------------------------


	/**
	 * Output the assets for HTML
	 *
	 * @access	public
	 * @param	string
	 * @param	boolean
	 * @return	object
	 * @author	Pablo
	 **/
	public function output( $type = 'all', $return = FALSE )
	{
		//	Unload anything first
		if ( count ( $this->unload_assets ) ) :

			foreach ( $this->unload_assets AS $asset) :

				//	CSS
				unset($this->css[$asset]);
				unset($this->css_nails[$asset]);

				//	JS
				unset($this->js[$asset]);
				unset($this->js_nails[$asset]);

			endforeach;

		endif;

		// --------------------------------------------------------------------------

		//	Now output.
		$out = '';
		switch ( $type ) :

			case 'css'			: $out .= $this->_print_css_nails();
								  $out .= $this->_print_css();			break;
			case 'css-inline'	: $out  = $this->_print_css_inline();	break;

			case 'js'			: $out .= $this->_print_js_nails();
								  $out .= $this->_print_js();			break;
			case 'js-inline'	: $out .= $this->_print_js_inline();	break;

			case 'all'			: $out .= $this->_print_css_nails();
								  $out .= $this->_print_css();
								  $out .= $this->_print_js_nails();
								  $out .= $this->_print_js();			break;
		endswitch;

		// --------------------------------------------------------------------------

		//	Force SSL for assets if running on non-standard port
		if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] && $_SERVER['HTTPS'] != 'off' ) :

			$site_url_ssl = str_replace( 'http://', 'https://', site_url() );
			$out = str_replace( site_url(), $site_url_ssl, $out );

		endif;

		// --------------------------------------------------------------------------

		if ( $return ) :

			return $out;

		else :

			echo $out;

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
	 * @author	Pablo
	 **/
	private function _determine_type( $input, $force_type = FALSE )
	{
		//	Override if nessecary
		if ( $force_type )
			return $force_type;

		//	Look for <style></style>
		if ( preg_match( '/\<style.*\<\/style\>/si', $input ) )
			return 'css_inline';

		//	Look for <script></script>
		if ( preg_match( '/\<script.*\<\/script\>/si', $input ) )
			return 'js_inline';

		//	Look for .css
		if ( substr( $input, strrpos( $input, '.' ) ) == '.css' )
			return 'css';

		//	Look for .js
		if ( substr( $input, strrpos( $input, '.' ) ) == '.js' )
			return 'js';
	}


	// --------------------------------------------------------------------------


	/**
	 * Output the referenced CSS files
	 *
	 * @access	private
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	private function _print_css()
	{
		$out = '';
		foreach ( $this->css AS $asset ) :

			$url = ( preg_match( '/[http|https|ftp]:\/\/.*/si', $asset ) ) ? $asset : 'assets/css/' . $asset ;
			$out .= link_tag( $url ) . "\n";

		endforeach;
		return $out;
	}


	// --------------------------------------------------------------------------


	/**
	 * Output the referenced nails CSS files
	 *
	 * @access	private
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	private function _print_css_nails()
	{
		$out = '';

		foreach ( $this->css_nails AS $asset ) :

			$url = ( preg_match( '/[http|https|ftp]:\/\/.*/si', $asset ) ) ? $asset : NAILS_URL . 'css/' . $asset ;
			$out .= link_tag( $url . '?release=' . NAILS_ASSETS_RELEASE ) . "\n";

		endforeach;

		return $out;
	}


	// --------------------------------------------------------------------------


	/**
	 * Output the inline CSS
	 *
	 * @access	private
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	private function _print_css_inline()
	{
		$out = '';
		foreach ( $this->css_inline AS $asset ) :
			$out .= $asset."\n";
		endforeach;
		$out = preg_replace( '/<\/?style.*?>/si', '', $out );
		return '<style type="text/css">'.$out.'</style>';
	}


	// --------------------------------------------------------------------------


	/**
	 * Output the referenced JS files
	 *
	 * @access	private
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	private function _print_js()
	{
		$out = '';

		foreach ( $this->js AS $asset ) :

			$url = ( preg_match( '/[http|https|ftp]:\/\/.*/si', $asset ) ) ? $asset : site_url( 'assets/js/'.$asset ) ;
			$out .= '<script type="text/javascript" src="' . $url . '"></script>' . "\n";

		endforeach;

		return $out;
	}


	// --------------------------------------------------------------------------


	/**
	 * Output the referenced nails JS files
	 *
	 * @access	private
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	private function _print_js_nails()
	{
		$out = '';

		foreach ( $this->js_nails AS $asset ) :

			$url = ( preg_match( '/[http|https|ftp]:\/\/.*/si', $asset ) ) ? $asset : NAILS_URL . 'js/' . $asset ;
			$out .= '<script type="text/javascript" src="' . $url . '?release=' . NAILS_ASSETS_RELEASE . '"></script>' . "\n";

		endforeach;
		return $out;
	}


	// --------------------------------------------------------------------------


	/**
	 * Output the inline JS files
	 *
	 * @access	private
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	private function _print_js_inline()
	{
		$out = '';
		foreach ( $this->js_inline AS $asset ) :

			$out .= $asset . "\n";

		endforeach;

		$out = preg_replace( '/<\/?script.*?>/si', '', $out );

		return '<script type="text/javascript">'.$out.'</script>';
	}
}

/* End of file asset.php */
/* Location: ./application/libraries/asset.php */