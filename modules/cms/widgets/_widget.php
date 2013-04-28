<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nails_CMS_Widget
{
	static function details()
	{
		$_d	= new stdClass();
		
		$_d->name	= 'Widget';
		$_d->slug	= 'Widget';
		$_d->iam	= 'Nails_CMS_Widget';
		$_d->info	= '';
		
		return $_d;
	}
	
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
		return '';
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_editor_functions()
	{
		$_out		= '';
		
		//	Called when starting the sort/widget is received
		$_out		.= 'function start_' . $this::details()->slug . '() {';
		$_out		.= $this->_editor_function_start();
		$_out		.= '};'."\n";
		
		//	Called when user has stopped sorting
		$_out		.= 'function stop_' . $this::details()->slug . '() {';
		$_out		.= $this->_editor_function_stop();
		$_out		.= '};'."\n";
		
		return $_out;
	}
	
	protected function _editor_function_start()
	{
		return '';
	}
	
	protected function _editor_function_stop()
	{
		return '';
	}
	
	// --------------------------------------------------------------------------
	
	public function get_validation_rules( $field )
	{
		return '';
	}
}