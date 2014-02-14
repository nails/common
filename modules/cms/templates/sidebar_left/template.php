<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nails_CMS_Template_sidebar_left extends Nails_CMS_Template
{
	static function details()
	{
		//	Base object
		$_d = parent::_details();

		//	Basic details; describe the template for the user
		$_d->label			= 'Sidebar Left';
		$_d->description	= 'Main body with a sidebar to the left.';

		//	Widget areas; give each a unique index, the index will be passed as
		//	the variable to the view

		$_d->widget_areas['sidebar']			= parent::_editable_area_template();
		$_d->widget_areas['sidebar']->title		= 'Sidebar';
		$_d->widget_areas['mainbody']			= parent::_editable_area_template();
		$_d->widget_areas['mainbody']->title	= 'Main Body';

		// --------------------------------------------------------------------------

		return $_d;
	}
}

/* End of file _template_sidebar_left.php */
/* Location: ./modules/cms/templates/_template_sidebar_left.php */