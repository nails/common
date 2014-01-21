<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nails_CMS_Template
{
	static protected function _details_template()
	{
		$obj				= new stdClass();

		//	The human friendly name of this template
		$obj->name			= 'Widget';

		//	A brief description fo the template, optional
		$obj->description	= '';

		$obj->img			= new stdClass();
		$obj->img->icon		= '';
		$obj->img->preview	= '';

		//	an array of the widget-able areas
		$obj->widget_areas	= array();

		// --------------------------------------------------------------------------

		//	Automatically calculated properties
		$obj->slug	= '';
		$obj->iam	= get_called_class();

		// --------------------------------------------------------------------------

		//	Work out slug - this should uniquely identify a type of widget
		$reflect	= new ReflectionClass( $obj->iam );
		$obj->slug	= $reflect->getFileName();
		$obj->slug	= basename( $obj->slug );
		$obj->slug	= explode( '.', $obj->slug );
		array_pop( $obj->slug  );
		$obj->slug	= implode( '.', $obj->slug );
		$obj->slug	= ucfirst( $obj->slug );

		// --------------------------------------------------------------------------

		return $obj;
	}


	// --------------------------------------------------------------------------


	static protected function _editable_area_template()
	{
		$obj				= new stdClass();
		$obj->title			= '';
		$obj->description	= '';
		$obj->view			= '';

		return $obj;
	}
}

/* End of file _template.php */
/* Location: ./modules/cms/templates/_template.php */