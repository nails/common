<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nails_CMS_Template_sidebar_right extends Nails_CMS_Template
{
	static function details()
	{
		//	Base object
		$_d = parent::_details_template();

		//	Basic details; describe the template for the user
		$_d->label		= 'Sidebar Right';
		$_d->info		= 'Main body with a sidebar to the right.';
		$_d->img->icon	= NAILS_URL . 'img/admin/modules/cms/pages/templates/icons/sidebar_right.png';

		//	Which view should be called by this template?
		//	Nails' views are stored at NAILS_PATH . 'modules/cms/views/page/templates'
		//	App views should mimic the Nails path, easily override a Nails view by
		//	placing a replacement at the same path.

		$_d->view	= 'sidebar_right';

		//	Widget areas; give each a unique index, the index will be passed as
		//	the variable to the view

		$_d->widget_areas['mainbody']			= parent::_editable_area_template();
		$_d->widget_areas['mainbody']->title	= 'Main Body';
		$_d->widget_areas['sidebar']			= parent::_editable_area_template();
		$_d->widget_areas['sidebar']->title		= 'Sidebar';

		// --------------------------------------------------------------------------

		return $_d;
	}
}

/* End of file _template_sidebar_right.php */
/* Location: ./modules/cms/templates/_template_sidebar_right.php */