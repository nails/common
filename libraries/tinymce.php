<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			TinyMCE
**
*
* Docs:			-
*
* Created:		17/02/2011
* Modified:		10/01/2012
*
* Description:	A Library to take the pain out of using TinyMCE in your apps.
* 
*/

class TinyMCE {
	
	private $ci;
	protected $tinymce;
	
	
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
		$this->ci =& get_instance();
		
		//	Load defaults from config
		$this->ci->config->load( 'tinymce' );
		$settings = $this->ci->config->item( 'setting' );
		
		foreach( $settings AS $k=>$v) :
		
			$this->add_setting( $k, $v );
		
		endforeach;
		
		//	Load assets
		$this->ci->asset->load( NAILS_URL . 'libs/tiny_mce/jquery.tinymce.js' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Add a Setting
	 *
	 * @access	public
	 * @param	string
 	 * @param	string
	 * @return	void
	 * @author	Pablo
	 **/
	public function add_setting( $setting, $value )
	{
		$this->tinymce->setting->{$setting} = $value;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Remove a Setting
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 * @author	Pablo
	 **/
	public function remove_setting( $setting )
	{
		unset( $this->tinymce->setting->{$setting} );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Load the script to memory
	 *
	 * @access	public
 	 * @param	string
 	 * @param	boolean
 	 * @param	boolean
	 * @return	void
	 * @author	Pablo
	 **/
	public function load( $element = 'textarea', $echo = FALSE, $script_tags = TRUE )
	{
		//	Build inline Javascript
		$script = ( $script_tags === TRUE || $echo === FALSE  ) ? "<script type=\"text/javascript\">\n$(function() {" : '';
		
		//	CSS Selector
		$script .= "$('" . $element . "').tinymce({\n";
		
		foreach ($this->tinymce->setting AS $k=>$v) :
			
			if ( is_bool( $v ) ) :
				$v = ( $v ) ? 'true' : 'false' ;
			elseif ( is_array( $v ) || is_object( $v ) ) :			
				$v = '"'.implode( (array)$v, ',' ).'"';
			elseif ( substr( trim( $v ), 0, 8 ) == 'function' ) :
				$v = $v;
			else :
				$v = '"'.$v.'"';
			endif;
			
			$script .= "{$k}:{$v},\n";
		
		endforeach;
		
		//	Remove trailing comma
		$script = substr( $script, 0, -2 )."\n";
		
		//	Close CSS Selector
		$script .= "});\n";
		
		//	Finish the inline Javascript
		$script .= ( $script_tags === TRUE || $echo === FALSE ) ? "});\n</script>" : '';
		
		//	Add to assets
		if ( $echo ) :
			echo $script;
		else :
			$this->ci->asset->inline( $script );
		endif;
	}
}

/* End of file tinymce.php */
/* Location: ./application/libraries/tinymce.php */