<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Uploadify
**
*
* Docs:			-
*
* Created:		17/02/2011
* Modified:		05/12/2012
*
* Description:	A Library to take the pain out of using Uploadify in your apps.
* 
*/

class Uploadify {
	
	private $ci;
	protected $uploadify;

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
		$this->ci->config->load( 'uploadify' );
		$settings = $this->ci->config->item( 'setting' );
		
		foreach( $settings AS $k=>$v) :
		
			$this->add_setting( $k, $v );
		
		endforeach;
		
		//	Load asset
		$this->ci->asset->load( 'uploadify/uploadify.css', TRUE );
		$this->ci->asset->load( 'uploadify/uploadify.js', TRUE );
	}
	
	
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
		$this->uploadify->setting->{$setting} = $value;
	}
	
	
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
		unset( $this->uploadify->setting->{$setting} );
	}
	
	
	/**
	 * Add a Callback
	 *
	 * @access	public
	 * @param	string
 	 * @param	string
	 * @return	void
	 * @author	Pablo
	 **/
	public function add_callback( $callback, $value )
	{
		$this->uploadify->callback->{$callback} = $value;
	}
	
	
	/**
	 * Remove a Callback
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 * @author	Pablo
	 **/
	public function remove_callback( $callback )
	{
		unset( $this->uploadify->callback->{$callback} );
	}
	
	
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
	public function load( $element, $echo = FALSE, $script_tags = TRUE )
	{
		//	Check basic settings have been set
		if ( empty( $this->uploadify->setting->uploader ) )
			show_error( 'Uploadify: the <code>uploader</code> setting hasn\'t been set properly.' );
		
		//	Build inline Javascript
		$script = ( $script_tags === TRUE || $echo === FALSE  ) ? "<script type=\"text/javascript\">\n$(function() {" : '';
		
		//	CSS Selector
		$script .= "$('" . $element . "').uploadify({\n";
		
		//	Generate a token to identify the [logged in] user
		$this->uploadify->setting->postData['user_token'] = $this->generate_token();
		
		
		//	Settings...
		foreach ($this->uploadify->setting AS $k=>$v) :
			
			if ( is_bool( $v ) ) :
				$v = ( $v ) ? 'true' : 'false' ;
			elseif ( is_array( $v ) || is_object( $v ) ) :			
				$v = json_encode( $v );
			else :
				$v = '"'.$v.'"';
			endif;
			
			$script .= "{$k}:{$v},\n";
		
		endforeach;
		
		
		//	Callbacks now...
		foreach ( $this->uploadify->callback AS $k => $v ) :
			
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
	
	public function generate_token()
	{
		$_id	= active_user( 'id' );
		
		if ( ! $_id )
			return FALSE;
		
		$_guid	= microtime( TRUE ) * 10000;
		$_hash	= sha1( $_id . $_guid . $this->ci->config->item( 'secret' ) );
		
		$_out['id']		= $_id;
		$_out['guid']	= $_guid;
		$_out['hash']	= $_hash;

		return implode( $_out, '|' );
	}
	
	public function validate_token( $token )
	{
		$_token = explode( '|', $token );
		
		if ( count( $_token ) != 3 )
			return FALSE;
		
		list( $_id, $_guid, $_hash ) = $_token;
		
		$_check = sha1( $_id . $_guid . $this->ci->config->item( 'secret' ) );
		
		return ( $_check === $_hash );
	}
}

/* End of file uploadify.php */
/* Location: ./application/libraries/uploadify.php */