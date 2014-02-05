<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class NAILS_CMS_Widget
{
	protected $_details;
	protected $_data;

	// --------------------------------------------------------------------------


	static function details()
	{
		$_d					= new stdClass();
		$_d->iam			= get_called_class();

		$_reflect 			= new ReflectionClass( $_d->iam );

		$_d->label			= 'Widget';
		$_d->description	= '';
		$_d->keywords		= '';
		$_d->grouping		= '';

		//	Work out the slug, this should uniquely identify the widget
		//	Work out slug - this should uniquely identify a type of widget

		$_d->slug	= $_reflect->getFileName();
		$_d->slug	= pathinfo( $_d->slug );
		$_d->slug	= explode( '/', $_d->slug['dirname'] );
		$_d->slug	= array_pop( $_d->slug  );

		//	If a widget should be restricted to a specific templates or areas
		//	then specify the appropriate slugs below

		$_d->restrict_to_template	= array();
		$_d->restrict_to_area		= array();

		//	If a widget should appear anywhere BUT a certain template or area,
		//	then define that here

		$_d->restrict_from_template	= array();
		$_d->restrict_from_area		= array();

		//	Define any assets need to be loaded by the widget
		$_d->assets = array();

		//	Define the views to use for the widget, defaults to 'cms/page/widgets/' . widget_slug . '_[editor/slug]'

		$_d->views			= new stdClass();
		$_d->views->editor	= '';
		$_d->views->render	= '';

		//	Define any JS callbacks; these will be properly scoped by the framework
		$_d->callbacks					= new stdClass();
		$_d->callbacks->before_drop		= '';
		$_d->callbacks->after_drop		= '';
		$_d->callbacks->before_sort		= '';
		$_d->callbacks->after_sort		= '';
		$_d->callbacks->before_remove	= '';
		$_d->callbacks->after_remove	= '';

		//	Path
		$_d->path = dirname( $_reflect->getFileName() ) . '/';

		return $_d;
	}

	// --------------------------------------------------------------------------


	public function __construct()
	{
		$this->_details = $this::details();
	}


	// --------------------------------------------------------------------------


	public function setup( $_data = NULL )
	{
		$this->_data = $_data;
	}

	// --------------------------------------------------------------------------


	public function get_editor()
	{
		$_view = $this->_details->views->editor ? $this->_details->views->editor : 'cms/page/widgets/' . $this->_details->slug . '/views/editor';

		if ( is_file( $this->_details->path . 'views/editor.php' ) ) :

			return file_get_contents( $this->_details->path . 'views/editor.php' );

		endif;

		return '';
	}


	// --------------------------------------------------------------------------


	public function render()
	{

	}
}

/* End of file _widget.php */
/* Location: ./modules/cms/widgets/_widget.php */