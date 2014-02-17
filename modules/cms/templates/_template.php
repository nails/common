<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nails_CMS_Template
{

	protected $load;
	protected $data;

	// --------------------------------------------------------------------------


	/**
	 * Defines the base template details object
	 *
	 * Templates should extend this object and customise to their own needs
	 *
	 * @param none
	 * @return stdClass
	 *
	 **/
	static protected function _details()
	{
		$_d			= new stdClass();
		$_d->iam	= get_called_class();

		$_reflect	= new ReflectionClass( $_d->iam );

		//	The human friendly name of this template
		$_d->label			= 'Widget';

		//	A brief description fo the template, optional
		$_d->description	= '';

		$_d->img			= new stdClass();
		$_d->img->icon		= '';

		//	Try to detect the icon
		$_extensions = array( 'png','jpg','jpeg','gif' );

		$_path	= $_reflect->getFileName();
		$_path	= dirname( $_path );

		foreach ( $_extensions AS $ext ) :

			$_icon = $_path . '/icon.' . $ext;

			if ( is_file( $_icon ) ) :

				$_url = '';
				if ( preg_match( '#^' . preg_quote( NAILS_PATH, '#' ) . '#', $_icon ) ) :

					//	Nails asset
					$_d->img->icon = preg_replace( '#^' . preg_quote( NAILS_PATH, '#' ) . '#', NAILS_URL, $_icon );

				elseif ( preg_match( '#^' . preg_quote( FCPATH . APPPATH, '#' ) . '#', $_icon ) ) :

					if ( page_is_secure() ) :

						$_d->img->icon = preg_replace( '#^' . preg_quote( FCPATH . APPPATH, '#' ) . '#', SECURE_BASE_URL . APPPATH . '', $_icon );

					else :

						$_d->img->icon = preg_replace( '#^' . preg_quote( FCPATH . APPPATH, '#' ) . '#', BASE_URL . APPPATH . '', $_icon );

					endif;

				endif;

				break;

			endif;

		endforeach;

		//	an array of the widget-able areas
		$_d->widget_areas	= array();

		// --------------------------------------------------------------------------

		//	Automatically calculated properties
		$_d->slug	= '';


		// --------------------------------------------------------------------------

		//	Work out slug - this should uniquely identify a type of template
		$_d->slug	= $_reflect->getFileName();
		$_d->slug	= pathinfo( $_d->slug );
		$_d->slug	= explode( '/', $_d->slug['dirname'] );
		$_d->slug	= array_pop( $_d->slug  );

		// --------------------------------------------------------------------------

		//	Define any assets need to be loaded by the template
		$_d->assets = array();

		// --------------------------------------------------------------------------

		//	Path
		$_d->path = dirname( $_reflect->getFileName() ) . '/';

		// --------------------------------------------------------------------------

		return $_d;
	}


	// --------------------------------------------------------------------------

	/**
	 * Defines the base widget area object
	 *
	 * Each editable area needs to have certain properties defined. The template
	 * clone this object for each area and set the values appropriately.
	 *
	 * @param none
	 * @return stdClass
	 *
	 **/
	static protected function _editable_area_template()
	{
		$_d				= new stdClass();
		$_d->title			= '';
		$_d->description	= '';
		$_d->view			= '';

		return $_d;
	}


	// --------------------------------------------------------------------------


	/**
	 * Template constructor
	 *
	 * Sets the templates details as a class variable
	 *
	 * @param none
	 * @return void
	 *
	 **/
	public function __construct()
	{
		$this->_details = $this::details();
		$this->load	=& get_instance()->load;
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders the template with the provided data
	 *
	 * This method accepts a template data and renders the page appropriately
	 *
	 * @param stdClass $_tpl_data A normal template_data object, prefixed to avoid naming collisions
	 * @param array $view_data An array of properties to pass to the views. Synonymous with the $this->data variable under normal controllers.
	 * @return string
	 *
	 **/
	public function render( $_tpl_data = array(), &$view_data = array() )
	{
		//	If the template wishes to execute any custom pre/post code then this method
		//	should be extended and parent::render( $_data ) called at the appropriate
		//	point. But that's obvious, isn't it...?

		// --------------------------------------------------------------------------

		get_instance()->load->model( 'cms/cms_page_model' );


		// --------------------------------------------------------------------------

		//	Process each widget area and render the HTML
		$_widget_areas = array();
		foreach ( $this->_details->widget_areas AS $key => $details ) :

			$_widget_areas[$key] = '';

			//	Loop through all defined widgets and render each one
			if ( ! empty( $_tpl_data[$key] ) ) :

				foreach ( $_tpl_data[$key] AS $widget_data ) :

					try
					{
						$_widget = get_instance()->cms_page_model->get_widget( $widget_data->widget );

						if ( $_widget ) :

							$_data = array();

							if ( $widget_data->data ) :

								foreach ( $widget_data->data AS $item ) :

									if ( isset( $item->name ) ) :

										$_data[$item->name] = isset( $item->value ) ? $item->value : NULL;

									endif;

								endforeach;

							endif;

							$WIDGET = new $_widget->iam();
							$_widget_areas[$key] .= $WIDGET->render( $_data );

						endif;
					}
					catch ( Exception $e )
					{
						log_message( 'error', 'Failed to render widget' );
					}

				endforeach;

			endif;

		endforeach;

		// --------------------------------------------------------------------------

		if ( is_file( $this->_details->path . 'view.php' ) ) :

			//	If passed, extract any view data
			if ( $view_data ) :

				extract( $view_data );

			endif;

			//	Extract the variables, so that the view can use them
			if ( $_widget_areas ) :

				extract( $_widget_areas );

			endif;

			//	Start the buffer, basically copying how CI does it's view loading
			ob_start();

			include $this->_details->path . 'view.php';

			//	Flush buffer
			$_buffer = ob_get_contents();
			@ob_end_clean();

			//	Return the HTML
			return $_buffer;

		endif;

		return '';
	}
}

/* End of file _template.php */
/* Location: ./modules/cms/templates/_template.php */