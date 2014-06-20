<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Tests
 *
 * Description:	This controller handles the execution of Nails and App tests
 *
 **/

//	Include System_controller; executes common tests functionality.
require_once '_system.php';

/**
 * OVERLOADING NAILS' TEST MODULE
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Test extends NAILS_System_Controller
{
	protected $_tests;
	private $_info;
	private $_result;

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

		if ( ! $this->user_model->is_superuser() && ! $this->input->is_cli_request() && ! $this->input->get( 'token' ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Set defaults for $this->_info and $this->_result
		$this->_info	= new stdClass();
		$this->_info->test			= '';
		$this->_info->label			= '';
		$this->_info->description	= '';
		$this->_info->testing		= '';
		$this->_info->expecting		= '';

		$this->_result				= new stdClass();
		$this->_result->pass		= '';
		$this->_result->errors		= array();
		$this->_result->info		= new stdClass();

		// --------------------------------------------------------------------------

		//	Define the default list of tests
		//	Each test listed here should be a valid callback, if a callback is not found the test will be silently discarded

		$this->_tests = array();

		if ( $this->input->post( 'test' ) ) :

			$this->_tests	= $this->input->post( 'test' );

		else :

			$this->_tests[] = module_is_enabled( 'shop' ) ? 'shop' : NULL;
			$this->_tests[] = module_is_enabled( 'cdn' ) ? 'cdn' : NULL;
			$this->_tests[] = 'canwritedirs';
			$this->_tests[] = 'cansendemail';

		endif;

		$this->_tests = array_filter( $this->_tests );
		$this->_tests = array_values( $this->_tests );

		$this->data['tests'] =& $this->_tests;

		// --------------------------------------------------------------------------

		//	Clear assets
		$this->asset->clear_all();

		// --------------------------------------------------------------------------

		//	Prepare tests
		for ( $i=0; $i < count( $this->_tests ); $i++ ) :

			if ( method_exists( $this, '_info_' . $this->_tests[$i] ) ) :

				$_test	= $this->_tests[$i];
				$_info	= $this->{'_info_' . $this->_tests[$i] }();

				if ( $_info ) :

					$this->_tests[$i] = clone( $_info );
					$this->_tests[$i]->test = $_test;

				else :

					$this->_tests[$i] = FALSE;

				endif;

			else :

				$this->_tests[$i] = FALSE;

			endif;

		endfor;

		$this->_tests = array_values( array_filter( $this->_tests ) );
	}


	// --------------------------------------------------------------------------


	/**
	 * Lists the tests, the purpose of each, the test to be executed and the expected result
	 *
	 * @access	public
	 * @return	void
	 *
	 **/
	public function view()
	{
		//	Overrides & page data
		$this->data['header_override']	= 'structure/header/blank';
		$this->data['footer_override']	= 'structure/footer/blank';
		$this->data['page']->title		= APP_NAME . ' Tests';

		// --------------------------------------------------------------------------

		//	Determine what type of view to return
		switch( $this->uri->segment( 4 ) ) :

			case 'json' :

				$this->output->set_content_type( 'application/json' );
				$this->output->set_header( 'Cache-Control: no-store, no-cache, must-revalidate' );
				$this->output->set_header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
				$this->output->set_header( 'Pragma: no-cache' );
				$this->output->set_output( json_encode( $this->_tests ) );

			break;

			// --------------------------------------------------------------------------

			case 'html' :

				$this->load->view( 'structure/header',	$this->data );
				$this->load->view( 'tests/view/html',	$this->data );
				$this->load->view( 'structure/footer',	$this->data );

			break;

			// --------------------------------------------------------------------------

			case 'text' :

				$this->load->view( 'tests/view/text',	$this->data );

			break;

			// --------------------------------------------------------------------------

			default :

				//	Attempt to auto-detect; text if on command line, html otherwise
				if ( $this->input->is_cli_request() ) :

					$this->load->view( 'tests/view/text',	$this->data );

				else :

					$this->load->view( 'structure/header',	$this->data );
					$this->load->view( 'tests/view/html',	$this->data );
					$this->load->view( 'structure/footer',	$this->data );

				endif;

			break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	/**
	 * Actually runs the tests
	 *
	 * @access	public
	 * @return	void
	 *
	 **/
	public function run()
	{
		//	Which tests are we running?
		$_tests =& $this->_tests;


		// --------------------------------------------------------------------------

		//	Run each test
		$_results	= array();	//	Stores the result of tests

		for ( $i=0; $i < count( $_tests ); $i++ ) :

			if ( method_exists( $this, '_test_' . $_tests[$i]->test ) ) :

				//	Reset defaults
				$this->_result->pass	= TRUE;
				$this->_result->errors	= array();

				// --------------------------------------------------------------------------

				$_result		= clone( $this->{'_test_' . $_tests[$i]->test }() );
				$_result->info	= clone( $this->{'_info_' . $_tests[$i]->test }() );
				$_results[]		= $_result;

			endif;

		endfor;

		// --------------------------------------------------------------------------

		//	Summarise
		$this->data['summary']	= new stdClass();
		$this->data['summary']->total	= count( $_results );
		$this->data['summary']->pass	= 0;
		$this->data['summary']->fail	= 0;
		$this->data['summary']->results	=& $_results;

		foreach ( $_results AS $result ) :

			if ( $result->pass ) :

				$this->data['summary']->pass++;

			else :

				$this->data['summary']->fail++;

			endif;

		endforeach;

		// --------------------------------------------------------------------------

		//	Overrides & page data
		$this->data['header_override']	= 'structure/header/blank';
		$this->data['footer_override']	= 'structure/footer/blank';
		$this->data['page']->title		= APP_NAME . ' Test Results';

		// --------------------------------------------------------------------------

		//	Determine what type of view to return
		switch( $this->uri->segment( 4 ) ) :

			case 'json' :

				$this->output->set_header( 'Cache-Control: no-store, no-cache, must-revalidate' );
				$this->output->set_header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
				$this->output->set_header( 'Content-type: application/json' );
				$this->output->set_header( 'Pragma: no-cache' );
				$this->output->set_output( json_encode( $this->data['summary'] ) );

			break;

			// --------------------------------------------------------------------------

			case 'html' :

				$this->load->view( 'structure/header',		$this->data );
				$this->load->view( 'tests/results/html',	$this->data );
				$this->load->view( 'structure/footer',		$this->data );

			break;

			// --------------------------------------------------------------------------

			case 'text' :

				$this->load->view( 'tests/results/text',	$this->data );

			break;

			// --------------------------------------------------------------------------

			default :

				//	Attempt to auto-detect; text if on command line, html otherwise
				if ( $this->input->is_cli_request() ) :

					$this->load->view( 'tests/results/text',	$this->data );

				else :

					$this->load->view( 'structure/header',		$this->data );
					$this->load->view( 'tests/results/html',	$this->data );
					$this->load->view( 'structure/footer',		$this->data );

				endif;

			break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	//	THE TESTS

	//	Tests are written as two seperate functions: _test_TESTNAME() and _info_TESTNAME.
	//	It is important that TESTNAME is the same for both.
	//	_test_TESTNAME() methods update the $this->_result variable
	//	_info_TESTNAME() methods update the $this->_info variable


	// --------------------------------------------------------------------------


	protected function _test_shop()
	{
		//	Reset result
		$this->_result->pass	= TRUE;
		$this->_result->errors	= array();

		// --------------------------------------------------------------------------

		$_buckets	= array();
		$_buckets[]	= 'shop-product-images';
		$_buckets[]	= 'shop-brand-logos';
		$_buckets[]	= 'shop-download';

		// --------------------------------------------------------------------------

		//	CDN Enabled?
		if ( ! module_is_enabled( 'cdn' ) ) :

			$this->_result->pass		= FALSE;
			$this->_result->errors[]	= 'CDN is not enabled.';
			return $this->_result;

		else :

			$this->load->library( 'cdn' );

		endif;

		// --------------------------------------------------------------------------

		//	Execute test
		foreach ( $_buckets AS $bucket ) :

			$this->db->where( 'slug', $bucket );
			$_bucket = $this->db->get( NAILS_DB_PREFIX . 'cdn_bucket' );

			if ( ! $_bucket ) :

				//	Attempt to create
				if ( ! $this->cdn->bucket_create( $bucket ) ) :

					$this->_result->pass		= FALSE;
					$this->_result->errors[]	= '"' . $bucket . '" does not exist and is required, could not crete bucket (' . $this->cdn->last_error() . ').';
					continue;

				endif;

			endif;

		endforeach;

		// --------------------------------------------------------------------------

		return $this->_result;
	}

	protected function _info_shop()
	{
		$this->_info->label			= 'Shop is configured correctly';
		$this->_info->description	= 'This test will check that the shop is configured correctly.';
		$this->_info->testing		= 'Tests that the databse is correctly formed and that the appropriate CDN bucket exists.';
		$this->_info->expecting		= 'Normal databases and presence of CDN buckets (if they don\'t exist, the test will attempt to create them).';

		// --------------------------------------------------------------------------

		return $this->_info;
	}


	// --------------------------------------------------------------------------


	protected function _test_cdn()
	{
		//	Reset result
		$this->_result->pass	= TRUE;
		$this->_result->errors	= array();

		// --------------------------------------------------------------------------

		//	Execute tests
		$this->load->library( 'cdn' );
		if ( ! $this->cdn->run_tests() ) :

			$this->_result->pass	= FALSE;
			$this->_result->errors	= $this->cdn->get_errors();

		endif;

		// --------------------------------------------------------------------------

		return $this->_result;
	}

	protected function _info_cdn()
	{
		$this->_info->label			= 'CDN is functioning correctly';
		$this->_info->description	= 'This test will check that the CDN is configured correctly and functioning.';
		$this->_info->testing		= 'Tests that each bucket is configured correcty and that a small file can be written, moved, copied, deleted then destroyed.';
		$this->_info->expecting		= 'All buckets to exist and be writeable.';

		// --------------------------------------------------------------------------

		return $this->_info;
	}


	// --------------------------------------------------------------------------


	protected function _test_canwritedirs()
	{
		//	Reset result
		$this->_result->pass	= TRUE;
		$this->_result->errors	= array();

		// --------------------------------------------------------------------------

		//	Directories to test
		$_dirs		= array();

		//	Cache directory
		$_dirs[]	= DEPLOY_CACHE_DIR;

		//	Log folder
		$_dirs[]	= DEPLOY_LOG_DIR;

		// --------------------------------------------------------------------------

		//	Rhe routes_app.php file should exist
		$_dirs[] = DEPLOY_CACHE_DIR . 'routes_app.php';

		// --------------------------------------------------------------------------

		//	Execute test
		foreach ( $_dirs AS $dir ) :

			if ( ! file_exists( $dir ) ) :

				$this->_result->pass		= FALSE;
				$this->_result->errors[]	= '"' . $dir . '" does not exist.';
				continue;

			endif;

			if ( ! is_writable( $dir ) ) :

				$this->_result->pass		= FALSE;
				$this->_result->errors[]	= '"' . $dir . '" is not writable.';

			endif;

		endforeach;

		// --------------------------------------------------------------------------

		return $this->_result;
	}

	protected function _info_canwritedirs()
	{
		$this->_info->label			= 'Directories &amp; Files are writeable';
		$this->_info->description	= 'This test will check that the application can write to all directories and files that it needs to.';
		$this->_info->testing		= 'Tests that required writeable directories & files are writeable by the app.';
		$this->_info->expecting		= 'All folders and files to be writable.';

		// --------------------------------------------------------------------------

		return $this->_info;
	}


	// --------------------------------------------------------------------------


	protected function _test_cansendemail()
	{
		//	Reset result
		$this->_result->pass	= TRUE;
		$this->_result->errors	= array();

		// --------------------------------------------------------------------------

		$_email				= new stdClass();
		$_email->type		= 'test_email';

		if ( ! APP_DEVELOPER_EMAIL ) :

			$this->_result->pass		= FALSE;
			$this->_result->errors[]	= 'APP_DEVELOPER_EMAIL is not defined.';

		else :

			//	Send the email
			$_email->to_email = APP_DEVELOPER_EMAIL;

			$_config = array( 'graceful_startup' => TRUE );
			$this->load->library( 'emailer', $_config );

			//	Any startup errors?
			if ( $this->emailer->get_errors() ) :

				$this->_result->pass	= FALSE;
				$this->_result->errors	= $this->_result->errors + $this->emailer->get_errors();

			else :

				if ( ! $this->emailer->send( $_email, TRUE ) ) :

					$this->_result->pass		= FALSE;
					$this->_result->errors[]	= 'Email failed to send; error: ' . implode( ', ', $this->emailer->get_errors() );

				endif;

			endif;

		endif;

		// --------------------------------------------------------------------------

		return $this->_result;
	}

	protected function _info_cansendemail()
	{
		$this->_info->label			= 'Can send email';
		$this->_info->description	= 'This test checks that the app can send email; provided email credentials are provided';
		$this->_info->testing		= 'Tests that an email can be sent without error.';
		$this->_info->expecting		= 'The email to send without error';

		// --------------------------------------------------------------------------

		return $this->_info;
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' TESTS MODULES
 *
 * The following block of code makes it simple to extend one of the core tests
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_TESTS' ) ) :

	class Test extends NAILS_Test
	{
	}

endif;

/* End of file test.php */
/* Location: ./modules/system/controllers/test.php */