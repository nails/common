<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nails_CMS_Template
{
	static protected function _details_template()
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
				if ( preg_match( '#^' . NAILS_PATH . '#', $_icon ) ) :

					//	Nails asset
					$_d->img->icon = preg_replace( '#^' . NAILS_PATH . '#', NAILS_URL, $_icon );

				elseif ( preg_match( '#^' . FCPATH . APPPATH . '#', $_icon ) ) :

					if ( page_is_secure() ) :

						$_d->img->icon = preg_replace( '#^' . FCPATH . APPPATH . '#', SECURE_BASE_URL . APPPATH . '', $_icon );

					else :

						$_d->img->icon = preg_replace( '#^' . FCPATH . APPPATH . '#', BASE_URL . APPPATH . '', $_icon );

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

		//	Path
		$_d->path = dirname( $_reflect->getFileName() ) . '/';

		// --------------------------------------------------------------------------

		return $_d;
	}


	// --------------------------------------------------------------------------


	static protected function _editable_area_template()
	{
		$_d				= new stdClass();
		$_d->title			= '';
		$_d->description	= '';
		$_d->view			= '';

		return $_d;
	}
}

/* End of file _template.php */
/* Location: ./modules/cms/templates/_template.php */