<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nails_CMS_Widget_plaintext
{
	static function details()
	{
		$_d	= new stdClass();
		
		$_d->name	= 'Plain Text';
		$_d->slug	= 'Widget_plaintext';
		
		return $_d;
	}
	
	// --------------------------------------------------------------------------
	
	
	private $_body;
	
	// --------------------------------------------------------------------------
	
	public function __construct()
	{
		$this->_body = '';
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function setup( $data )
	{
		if ( isset( $data->body ) ) :
		
			$this->_body = $data->body;
		
		endif;
	}
	
	// --------------------------------------------------------------------------
	
	public function render()
	{
		return $this->_body;
	}
	
	// --------------------------------------------------------------------------
	
	public function get_editor_draggable_html()
	{
		$_details = self::details();
		
		//	Return editor HTML
		$_out  = '<li class="widget ' . $_details->slug . '" data-template="' . $_details->slug . '">';
		$_out .= $_details->name;
		$_out .= '</li>';
		
		// --------------------------------------------------------------------------
		
		return $_out;
	}
	
	// --------------------------------------------------------------------------
	
	public function get_editor_html()
	{
		$_details = self::details();
		
		//	Return editor HTML
		$_out  = '<li class="holder">';
		
		$_out .= '<h2 class="handle">';
		$_out .= $_details->name;
		$_out .= '<a href="#" class="close">Close</a>';
		$_out .= '</h2>';
		$_out .= '<div class="editor-content">';
		$_out .= '<p class="coming-soon">';
		$_out .= '<strong>Plain Text CMS Widget coming soon!</strong>';
		$_out .= 'Soon you\'ll be able to place blocks of plain text into your pages.';
		$_out .= '</p>';
		$_out .= '</div>';

		$_out .= '</li>';
		
		// --------------------------------------------------------------------------
		
		return $_out;
	}
}