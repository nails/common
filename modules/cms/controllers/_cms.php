<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class NAILS_CMS_Controller extends NAILS_Controller
{
	protected $_cdn_root;

	// --------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Check this module is enabled in settings
		if ( ! module_is_enabled( 'cms' ) ) :

			//	Cancel execution, module isn't enabled
			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Load language file
		$this->lang->load( 'cms' );
	}
}