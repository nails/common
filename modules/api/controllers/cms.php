<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		CMS API
 *
 * Description:	This controller handles CMS API calls
 *
 **/

require_once '_api.php';

/**
 * OVERLOADING NAILS' API MODULES
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Cms extends NAILS_API_Controller
{
	private $_authorised;
	private $_error;


	// --------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 *
	 **/
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_authorised	= TRUE;
		$this->_error		= '';

		// --------------------------------------------------------------------------

		//	Constructor mabobs.

		//	Only logged in users
		if ( ! $this->user->is_logged_in() ) :


			$this->_authorised	= FALSE;
			$this->_error		= lang( 'auth_require_session' );

		endif;

		// --------------------------------------------------------------------------

		//	Only admins
		if ( ! $this->user->is_admin() ) :

			$this->_authorised	= FALSE;
			$this->_error		= lang( 'auth_require_administrator' );

		endif;
	}


	// --------------------------------------------------------------------------


	public function pages()
	{
		if ( ! $this->_authorised ) :

			$this->_out( array( 'status' => 401, 'error' => $this->_error ) );
			return;

		endif;

		// --------------------------------------------------------------------------

		switch( $this->uri->segment( 4 ) ) :

			case 'widget'	: $this->_pages_widget();								break;
			case 'save'		: $this->_pages_save();									break;
			default			: $this->_method_not_found( $this->uri->segment( 4 ) );	break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	protected function _pages_widget()
	{
		switch( $this->uri->segment( 5 ) ) :

			case 'get_editor'	: $this->_pages_widget_get_editor();					break;
			default				: $this->_method_not_found( $this->uri->segment( 5 ) );	break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	protected function _pages_widget_get_editor()
	{
		$_out		= array();
		$_widget	= $this->input->get_post( 'widget' );
		$_data_raw	= json_decode( $this->input->get_post( 'data' ) );
		$_data		= array();
		$_template	= $this->input->get_post( 'template' );

		if ( $_data_raw ) :

			foreach ( $_data_raw AS $item ) :

				if ( isset( $item->name ) ) :

					$_data[$item->name] = isset( $item->value ) ? $item->value : NULL;

				endif;

			endforeach;

		endif;

		if ( $_widget ) :

			$this->load->model( 'cms/cms_page_model' );

			$_widget = $this->cms_page_model->get_widget( $_widget );

			if ( $_widget ) :

				//	Instantiate the widget
				include_once $_widget->path . 'widget.php';

				$WIDGET = new $_widget->iam();

				$WIDGET->setup( $_data );

				$_editor = $WIDGET->get_editor( $_data );

				if ( ! empty( $_editor ) ) :

					$_out['HTML'] = $_editor;

				else :

					$_out['status']	= 500;
					$_out['error']	= 'This widget has not been configured correctly. Please contact the developer quoting this error message: <strong>"#3:' . $_widget->iam . ':GetEditor"</strong>';

				endif;

			else :

				$_out['status']	= 400;
				$_out['error']	= 'Invalid Widget - Error number 2';

			endif;

		else :

			$_out['status']	= 400;
			$_out['error']	= 'Widget slug must be specified - Error number 1';

		endif;

		$this->_out( $_out );
	}


	// --------------------------------------------------------------------------


	protected function _pages_save()
	{
		$this->_out(array('id'=>1));
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' API MODULES
 *
 * The following block of code makes it simple to extend one of the core API
 * controllers. Some might argue it's a little hacky but it's a simple 'fix'
 * which negates the need to massively extend the CodeIgniter Loader class
 * even further (in all honesty I just can't face understanding the whole
 * Loader class well enough to change it 'properly').
 *
 * Here's how it works:
 *
 * CodeIgniter instantiate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclare class X' errors
 * and if we call our overloading class something else it will never get instantiated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instantiated et voila.
 *
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION_CLASSNAME
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if ( ! defined( 'NAILS_ALLOW_EXTENSION_CMS' ) ) :

	class Cms extends NAILS_Cms
	{
	}

endif;

/* End of file cms.php */
/* Location: ./modules/api/controllers/cms.php */