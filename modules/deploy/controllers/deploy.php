<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Deploy
 *
 * Description:	These methods can be run after a deploy to perform any pre or post deployment actions (e.g run tests or migrate the database)
 *
 **/

/**
 * OVERLOADING NAILS' DEPLOY MODULE
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Deploy extends CORE_NAILS_Controller
{
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		if ( ENVIRONMENT == 'production' && ! $this->input->is_cli_request() ) :

			show_404();

		endif;
	}


	// --------------------------------------------------------------------------


	public function pre()
	{
		$this->_start();

		// --------------------------------------------------------------------------

		//	NO NAILS TASKS TO BE RUN PRE DEPLOYMENT

		// --------------------------------------------------------------------------

		//	App hook
		$this->_pre_app();

		// --------------------------------------------------------------------------

		$this->_end();
	}

	protected function _pre_app(){}


	// --------------------------------------------------------------------------


	public function post()
	{
		$this->_start();

		// --------------------------------------------------------------------------

		//	Migrate DB
		$this->load->library( 'migration' );

		//	NAILS Migrations
		_LOG( 'Migrating Nails DB...' );
		$this->migration->set_nails();
		$this->migration->latest();
		_LOG( '... done' );

		//	APP Migrations
		_LOG( 'Migrating App DB...' );
		$this->migration->set_app();
		$this->migration->latest();
		_LOG( '... done' );

		// --------------------------------------------------------------------------

		//	App hook
		$this->_post_app();

		// --------------------------------------------------------------------------

		//	Rewrite Routes file
		_LOG( 'Rewriting app routes...' );
		$this->load->model( 'system/routes_model', 'routes_model', array( 'set_session' => FALSE ) );

		if ( $this->routes_model->update() ) :

			_LOG( '... done' );

		else :

			_LOG( '... failed: ' . $this->routes_model->last_error() );

		endif;

		// --------------------------------------------------------------------------

		$this->_end();
	}


	// --------------------------------------------------------------------------


	protected function _post_app(){}


	// --------------------------------------------------------------------------


	protected function _start()
	{
		if ( ENVIRONMENT !== 'production' && ! $this->input->is_cli_request() ) :

			echo $this->load->view( 'deploy/header', NULL, TRUE );
			echo $this->load->view( 'deploy/' . $this->uri->segment( 2 ), NULL, TRUE );

			echo '<hr />';
			echo '<h2>Deployment Log &rsaquo; ' . ucfirst( $this->uri->segment( 2 ) ) . '</h2>';
			echo '<pre>';

		endif;

		//	Note start
		$this->benchmark->mark( 'deploy_start' );
	}


	// --------------------------------------------------------------------------


	protected function _end()
	{
		//	Note End
		$this->benchmark->mark( 'deploy_end' );

		_LOG( '-----------------------------------------' );
		_LOG( ucfirst( $this->uri->segment( 2 ) ) . ' Deployment Tasks took ' . $this->benchmark->elapsed_time('deploy_start', 'deploy_end') . ' seconds' );

		// --------------------------------------------------------------------------

		if ( ENVIRONMENT !== 'production' && ! $this->input->is_cli_request() ) :

			echo '</pre>';
			echo $this->load->view( 'deploy/footer',	NULL,	TRUE );

		endif;
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' DEPLOY MODULE
 *
 * The following block of code makes it simple to extend one of the core deploy
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_DEPLOY' ) ) :

	class Deploy extends NAILS_Deploy
	{
	}

endif;