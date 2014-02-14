<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nails_CMS_Template_fullwidth extends Nails_CMS_Template
{
	static function details()
	{
		//	Base object
		$_d = parent::_details();

		//	Basic details; describe the template for the user
		$_d->label			= 'Full Width';
		$_d->description	= 'A full width template';


		//	Widget areas; give each a unique index, the index will be passed as
		//	the variable to the view

		$_d->widget_areas['mainbody']			= parent::_editable_area_template();
		$_d->widget_areas['mainbody']->title	= 'Main Body';

		// --------------------------------------------------------------------------

		return $_d;
	}
}

/* End of file _template_fullwidth.php */
/* Location: ./modules/cms/templates/_template_fullwidth.php */