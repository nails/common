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
	 * @author	Pablo
	 *
	 **/
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		if ( ! $this->user->is_superuser() && ! $this->input->is_cli_request() && ! $this->input->get( 'token' ) ) :

			if ( module_is_enabled( 'auth' ) ) :

				unauthorised();

			else :

				show_404();

			endif;

		endif;

		$this->_validate_token();

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
		$this->_tests[] = 'canwritedirs';
		$this->_tests[] = 'cansendemail';

		$this->data['tests'] =& $this->_tests;
	}


	// --------------------------------------------------------------------------


	/**
	 * Lists the tests, the purpose of each, the test to be executed and the expected result
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 *
	 **/
	public function view()
	{
		for ( $i=0; $i < count( $this->_tests ); $i++ ) :

			if ( method_exists( $this, '_info_' . $this->_tests[$i] ) ) :

				$_test = $this->_tests[$i];
				$this->_tests[$i] = clone( $this->{'_info_' . $this->_tests[$i] }() );
				$this->_tests[$i]->test = $_test;

			else :

				$this->_tests[$i] = FALSE;

			endif;

		endfor;

		$this->_tests = array_values( array_filter( $this->_tests ) );

		// --------------------------------------------------------------------------

		//	Overrides & page data
		$this->data['header_override']	= 'structure/header/blank';
		$this->data['footer_override']	= 'structure/footer/blank';
		$this->data['page']->title		= APP_NAME . ' Tests';

		// --------------------------------------------------------------------------

		//	Determine what type of view to return
		switch( $this->uri->segment( 4 ) ) :

			case 'json' :

				$this->output->set_header( 'Cache-Control: no-store, no-cache, must-revalidate' );
				$this->output->set_header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
				$this->output->set_header( 'Content-type: application/json' );
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
	 * @author	Pablo
	 *
	 **/
	public function run()
	{
		//	which tests are we running?
		if ( $this->input->post( 'test' ) ) :

			//	The posted tests!
			$_tests =& $this->input->post( 'test' );

		else :

			//	All the tests!
			$_tests =& $this->_tests;

		endif;

		// --------------------------------------------------------------------------

		//	Run each test
		$_results	= array();	//	Stores the result of tests

		for ( $i=0; $i < count( $_tests ); $i++ ) :

			if ( method_exists( $this, '_test_' . $_tests[$i] ) ) :

				//	Reset defaults
				$this->_result->pass	= TRUE;
				$this->_result->errors	= array();

				// --------------------------------------------------------------------------

				$_result		= clone( $this->{'_test_' . $_tests[$i] }() );
				$_result->info	= clone( $this->{'_info_' . $_tests[$i] }() );
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


	protected function _test_canwritedirs()
	{
		//	Reset result
		$this->_result->pass	= TRUE;
		$this->_result->errors	= array();

		// --------------------------------------------------------------------------

		//	Directories to test
		$_dirs		= array();

		//	Cache directory
		if ( defined( 'CACHE_DIR' ) ) :

			$_dirs[]	= CACHE_DIR;

		endif;

		// --------------------------------------------------------------------------

		//	Check CDN buckets/dirs
		if ( module_is_enabled( 'cdn' ) && CDN_DRIVER == 'LOCAL' ) :

			$_dirs[]	= CDN_PATH;

			//	Get all the buckets and check that directories exist and are writable
			$this->load->library( 'cdn' );
			$_buckets = $this->cdn->get_buckets();

			foreach ( $_buckets AS $bucket ) :

				$_dirs[] = CDN_PATH . $bucket->slug . '/';

			endforeach;

		endif;

		// --------------------------------------------------------------------------

		//	Is the app routes file writeable? Various modules might require access to it
		if ( module_is_enabled( 'cms' ) || module_is_enabled( 'blog' ) || module_is_enabled( 'shop' ) ) :

			$_dirs[] = FCPATH . APPPATH . 'config/routes_cms_page.php';

		endif;

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
		$_email->to_email	= APP_EMAIL_DEVELOPER ? APP_EMAIL_DEVELOPER : NAILS_EMAIL_DEVELOPER;

		if ( ! $_email->to_email ) :

			$this->_result->pass		= FALSE;
			$this->_result->errors[]	= 'APP_EMAIL_DEVELOPER and NAILS_EMAIL_DEVELOPER are blank.';

		else :

			//	Send the email
			$_config = array( 'graceful_startup' => TRUE );
			$this->load->library( 'emailer', $_config );

			//	Any startup errorS?
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
 * CodeIgniter  instanciate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclre class X' errors
 * and if we call our overloading class something else it will never get instanciated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instanciated et voila.
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

/* End of file admin.php */
/* Location: ./application/modules/api/controllers/admin.php */