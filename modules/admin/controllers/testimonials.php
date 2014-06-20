<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Admin: Testimonials
 * Description:	Manage testimonials
 *
 **/

//	Include Admin_Controller; executes common admin functionality.
require_once '_admin.php';

/**
 * OVERLOADING NAILS' ADMIN MODULES
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Testimonials extends NAILS_Admin_Controller
{

	/**
	 * Announces this module's details to those in the know.
	 *
	 * @access	static
	 * @param	none
	 * @return	void
	 **/
	static function announce()
	{
		$d = new stdClass();

		// --------------------------------------------------------------------------

		//	Load the laguage file
		get_instance()->lang->load( 'admin_testimonials' );

		// --------------------------------------------------------------------------

		//	Configurations
		$d->name = lang( 'testimonials_module_name' );

		// --------------------------------------------------------------------------

		//	Navigation options
		$d->funcs				= array();
		$d->funcs['index']		= lang( 'testimonials_nav_index' );
		$d->funcs['create']		= lang( 'testimonials_nav_create' );

		// --------------------------------------------------------------------------

		//	Only announce the controller if the user has permisison to know about it
		return self::_can_access( $d, __FILE__ );
	}


	// --------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->load->model( 'testimonials/testimonial_model' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Manage evcents
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function index()
	{
		//	Page Title
		$this->data['page']->title = lang( 'testimonials_index_title' );

		// --------------------------------------------------------------------------

		$this->data['testimonials'] = $this->testimonial_model->get_all();

		// --------------------------------------------------------------------------

		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'admin/testimonials/index',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	// --------------------------------------------------------------------------


	public function create()
	{
		//	Page Title
		$this->data['page']->title = lang( 'testimonials_create_title' );

		// --------------------------------------------------------------------------

		if ( $this->input->post() ) :

			$this->load->library( 'form_validation' );

			$this->form_validation->set_rules( 'quote',		'', 'xss_clean|required' );
			$this->form_validation->set_rules( 'quote_by',	'', 'xss_clean|required' );
			$this->form_validation->set_rules( 'order',		'', 'xss_clean' );

			$this->form_validation->set_message( 'required', lang( 'fv_required' ) );

			if ( $this->form_validation->run() ) :

				$_data				= array();
				$_data['quote']		= $this->input->post( 'quote' );
				$_data['quote_by']	= $this->input->post( 'quote_by' );
				$_data['order']		= (int) $this->input->post( 'order' );

				if ( $this->testimonial_model->create( $_data ) ) :

					$this->session->set_flashdata( 'success', lang( 'testimonials_create_ok' ) );
					redirect( 'admin/testimonials' );
					return;

				else :

					$this->data['error'] = lang( 'testimonials_create_fail' );

				endif;

			else :

				$this->data['error'] = lang( 'fv_there_were_errors' );

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'admin/testimonials/create',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	// --------------------------------------------------------------------------


	public function edit()
	{
		$this->data['testimonial'] = $this->testimonial_model->get_by_id( $this->uri->segment( 4 ) );

		if ( ! $this->data['testimonial'] ) :

			$this->session->set_flashdata( 'error', lang( 'testimonials_common_bad_id' ) );
			redirect( 'admin/testimonials' );
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Page Title
		$this->data['page']->title = lang( 'testimonials_edit_title' );

		// --------------------------------------------------------------------------

		if ( $this->input->post() ) :

			$this->load->library( 'form_validation' );

			$this->form_validation->set_rules( 'quote',		'', 'xss_clean|required' );
			$this->form_validation->set_rules( 'quote_by',	'', 'xss_clean|required' );
			$this->form_validation->set_rules( 'order',		'', 'xss_clean' );

			$this->form_validation->set_message( 'required', lang( 'fv_required' ) );

			if ( $this->form_validation->run() ) :

				$_data				= array();
				$_data['quote']		= $this->input->post( 'quote' );
				$_data['quote_by']	= $this->input->post( 'quote_by' );
				$_data['order']		= (int) $this->input->post( 'order' );

				if ( $this->testimonial_model->update( $this->data['testimonial']->id, $_data ) ) :

					$this->session->set_flashdata( 'success', lang( 'testimonials_edit_ok' ) );
					redirect( 'admin/testimonials' );
					return;

				else :

					$this->data['error'] = lang( 'testimonials_edit_fail' );

				endif;

			else :

				$this->data['error'] = lang( 'fv_there_were_errors' );

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'admin/testimonials/edit',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	// --------------------------------------------------------------------------


	public function delete()
	{
		$_testimonial = $this->testimonial_model->get_by_id( $this->uri->segment( 4 ) );

		if ( ! $_testimonial ) :

			$this->session->set_flashdata( 'error', lang( 'testimonials_common_bad_id' ) );
			redirect( 'admin/testimonials' );
			return;

		endif;

		// --------------------------------------------------------------------------

		if ( $this->testimonial_model->delete( $_testimonial->id ) ) :

			$this->session->set_flashdata( 'success', lang( 'testimonials_delete_ok' ) );

		else :

			$this->session->set_flashdata( 'error', lang( 'testimonials_delete_fail' ) );

		endif;

		// --------------------------------------------------------------------------

		redirect( 'admin/testimonials' );
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' ADMIN MODULES
 *
 * The following block of code makes it simple to extend one of the core admin
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_DASHBOARD' ) ) :

	class Testimonials extends NAILS_Testimonials
	{
	}

endif;


/* End of file testimonials.php */
/* Location: ./modules/admin/controllers/testimonials.php */