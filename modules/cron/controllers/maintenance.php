<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Cron Maintenance Controller
 *
 * Description:	Holders for common cron jobs
 *
 **/

/**
 * OVERLOADING NAILS' CRON MODULE
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

require_once '_cron.php';

class NAILS_Maintenance extends NAILS_Cron_Controller
{
	public function index()
	{
		//	TODO: Running the index method should automatically determine
		//	which tasks should be run. Either it knows that it runs every hour
		//	and tracks what's been going on, or it looks at the time and works
		//	out when the last items were run etc. Think about it.
	}

	// --------------------------------------------------------------------------


	public function hourly()
	{
		$this->_start( 'maintenance', 'hourly', 'Hourly Maintenance Tasks' );

		// --------------------------------------------------------------------------

		//	Hourly Tasks

		// --------------------------------------------------------------------------

		$this->_end();
	}


	// --------------------------------------------------------------------------


	public function daily()
	{
		$this->_start( 'maintenance', 'daily', 'Daily Maintenance Tasks' );

		// --------------------------------------------------------------------------

		//	Daily Tasks

		//	Shop related tasks
		if ( module_is_enabled( 'shop' ) ) :

			_LOG( 'Shop Module Enabled. Beginning Shop Tasks.' );

			// --------------------------------------------------------------------------

			//	Load models
			$this->load->model( 'shop/shop_model' );
			$this->load->model( 'shop/shop_currency_model' );

			// --------------------------------------------------------------------------

			//	Sync Currencies
			_LOG( '... Synching Currencies' );
			if ( ! $this->shop_currency_model->sync() ) :

				_LOG( '... ... FAILED: ' . $this->shop_currency_model->last_error() );

			endif;

			// --------------------------------------------------------------------------

			_LOG( 'Finished Shop Tasks' );

		endif;

		//	Site map related tasks, makes sense for this one to come last in case any of
		//	the previous have an impact

		if ( module_is_enabled( 'sitemap' ) ) :

			_LOG( 'Sitemap Module Enabled. Beginning Sitemap Tasks.' );

			// --------------------------------------------------------------------------

			//	Load models
			$this->load->model( 'sitemap/sitemap_model' );

			// --------------------------------------------------------------------------

			//	Generate sitemap
			_LOG( '... Generating Sitemap data' );
			if ( ! $this->sitemap_model->generate() ) :

				_LOG( '... ... FAILED: ' . $this->sitemap_model->last_error() );

			endif;

			// --------------------------------------------------------------------------

			_LOG( 'Finished Site Tasks' );

		endif;


		// --------------------------------------------------------------------------

		$this->_end();
	}


	// --------------------------------------------------------------------------


	public function weekly()
	{
		$this->_start( 'maintenance', 'weekly', 'Weekly Maintenance Tasks' );

		// --------------------------------------------------------------------------

		//	Weekly Tasks

		//	CDN AWS Sourcefile clearout
		//	Log file zip up and cleanup

		// --------------------------------------------------------------------------

		$this->_end();
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' CRON MODULE
 *
 * The following block of code makes it simple to extend one of the core cron
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
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if ( ! defined( 'NAILS_ALLOW_EXTENSION_CRON_MAINTENANCE' ) ) :

	class Maintenance extends NAILS_Maintenance
	{
	}

endif;