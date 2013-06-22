<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nails_CMS_Widget_slider extends Nails_CMS_Widget
{
	static function details()
	{
		$_d			= parent::details();
		$_d->name	= 'Slider';
		$_d->slug	= 'Widget_slider';
		$_d->iam	= 'Nails_CMS_Widget_slider';
		$_d->info	= 'Embed easily configurable photo sliders into your page.';
		
		return $_d;
	}
	
	// --------------------------------------------------------------------------
	
	
	private $_slides;
	
	
	// --------------------------------------------------------------------------
	
	public function __construct()
	{
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function setup( $data )
	{
	}
	
	// --------------------------------------------------------------------------
	
	public function render()
	{
		return '';
	}
	
	// --------------------------------------------------------------------------
	
	public function get_editor_html()
	{
		$_details = self::details();
		
		//	Return editor HTML
		$_out  = '<p class="coming-soon">';
		$_out .= '<strong>Slider CMS Widget coming soon!</strong>';
		$_out .= 'Soon you\'ll be able to place stunning, completely customisable photo slideshows beside your content.';
		$_out .= '</p>';
		
		// --------------------------------------------------------------------------
		
		return $_out;
	}
}