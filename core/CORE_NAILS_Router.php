<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Modular Extensions HMVC
 * 
 * @package		Nails
 * @subpackage	MX
 * @author		wiredesignz
 * @link		https://bitbucket.org/wiredesignz/codeigniter-modular-extensions-hmvc
 */
 
/* load the MX Router class */
require NAILS_PATH . 'MX/Router.php';

class CORE_NAILS_Router extends MX_Router {

	public function current_module()
	{
		return $this->module;
	}
}