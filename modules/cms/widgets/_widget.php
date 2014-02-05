<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class NAILS_CMS_Widget
{
	static function details()
	{
		$_d	= new stdClass();

		$_d->label			= 'Widget';
		$_d->slug			= 'Widget';
		$_d->iam			= get_called_class();
		$_d->description	= '';
		$_d->keywords		= '';
		$_d->grouping		= '';

		//	If a widget should be restricted to a specific templates or areas
		//	then specify the appropriate slugs below

		$_d->restrict_to_template	= array();
		$_d->restrict_to_area		= array();

		//	If a widget should appear anywhere BUT a certain template or area,
		//	then define that here

		$_d->restrict_from_template	= array();
		$_d->restrict_from_area		= array();

		return $_d;
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


	// --------------------------------------------------------------------------



	protected function _editor_function_start()
	{
		return '';
	}


	// --------------------------------------------------------------------------


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

/* End of file _widget.php */
/* Location: ./modules/cms/widgets/_widget.php */