<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class NAILS_CMS_Widget
{
	protected $_details;
	protected $_data;


	// --------------------------------------------------------------------------

	/**
	 * Defines the base widget details object
	 *
	 * Widgets should extend this object and customise to their own needs
	 *
	 * @param none
	 * @return stdClass
	 *
	 **/
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
		$_d->assets_editor = array();
		$_d->assets_render = array();

		//	Path
		$_d->path = dirname( $_reflect->getFileName() ) . '/';

		//	Define any JS callbacks; these will be properly scoped by the framework
		$_d->callbacks					= new stdClass();
		$_d->callbacks->dropped			= '';
		$_d->callbacks->sort_start		= '';
		$_d->callbacks->sort_stop		= '';
		$_d->callbacks->remove_start	= '';
		$_d->callbacks->remove_stop		= '';

		//	Attempt to auto-populate these fields
		foreach( $_d->callbacks AS $property => &$callback ) :

			if ( is_file( $_d->path . 'js/' . $property . '.min.js' ) )  :

				$callback = file_get_contents( $_d->path . 'js/' . $property . '.min.js' );

			elseif ( is_file( $_d->path . 'js/' . $property . '.js' ) )  :

				$callback = file_get_contents( $_d->path . 'js/' . $property . '.js' );

			endif;

		endforeach;

		return $_d;
	}


	// --------------------------------------------------------------------------


	/**
	 * Widget constructor
	 *
	 * Sets the widgets details as a class variable
	 *
	 * @param none
	 * @return void
	 *
	 **/
	public function __construct()
	{
		$this->_details = $this::details();
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders the HTML for the editor view
	 *
	 * Returns the HTML for the editor view. Any passed data will be used to
	 * populate the values of the form elements.
	 *
	 * @param stdClass $_wgt_data A normal widget_data object, prefixed to avoid naming collisions
	 * @return string
	 *
	 **/
	public function get_editor( $_wgt_data = array() )
	{
		if ( is_file( $this->_details->path . 'views/editor.php' ) ) :

			//	Extract the variables, so that the view can use them
			if ( $_wgt_data ) :

				extract( $_wgt_data );

			endif;

			//	Start the buffer, basically copying how CI does it's view loading
			ob_start();

			include $this->_details->path . 'views/editor.php';

			//	Flush buffer
			$_buffer = ob_get_contents();
			@ob_end_clean();

			//	Return the HTML
			return $_buffer;

		endif;

		return '';
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders the HTML for a widget
	 *
	 * Called by the template, this method renders the widget's HTML using the
	 * passed data.
	 *
	 * @param stdClass $_wgt_data A normal widget_data object, prefixed to avoid naming collisions
	 * @return string
	 *
	 **/
	public function render( $_wgt_data = array(), $_tpl_additional_data = array() )
	{
		if ( is_file( $this->_details->path . 'views/render.php' ) ) :

			//	If passed, extract any controller data
			$_NAILS_CONTROLLER_DATA =& get_controller_data();

			if ( $_NAILS_CONTROLLER_DATA ) :

				extract( $_NAILS_CONTROLLER_DATA );

			endif;

			//	Extract the variables, so that the view can use them
			if ( $_wgt_data ) :

				extract( $_wgt_data );

			endif;
			
			if ( $_tpl_additional_data ) :

				extract( $_tpl_additional_data );

			endif;

			//	Start the buffer, basically copying how CI does it's view loading
			ob_start();

			include $this->_details->path . 'views/render.php';

			//	Flush buffer
			$_buffer = ob_get_contents();
			@ob_end_clean();

			//	Return the HTML
			return $_buffer;

		endif;

		return '';
	}
}

/* End of file _widget.php */
/* Location: ./modules/cms/widgets/_widget.php */