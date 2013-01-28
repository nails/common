<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			System 
*
* Docs:			-
*
* Created:		24/11/2010
* Modified:		12/12/2011
*
* Description:	Used for various misc. functionality
* 
*/

class System extends NAILS_Controller
{
	/**
	 * Handle 404 errors
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function render_404()
	{
		//	Send correct header
		header( 'HTTP/1.0 404 Not Found' );
		
		// --------------------------------------------------------------------------
		
		//	Set header data
		$this->data['page']->title = 'Page Not Found';
		
		// --------------------------------------------------------------------------
		
		//	Load the views; using the auth_model view loader as we need to check if
		//	an overload file exists which should be used instead
		
		$this->nails->load_view( 'structure/header',	'views/structure/header',	$this->data );
		$this->nails->load_view( 'system/404',			'modules/system/404',		$this->data );
		$this->nails->load_view( 'structure/footer',	'views/structure/footer',	$this->data );
	}
}

/* End of file system.php */
/* Location: ./application/modules/system/controllers/system.php */