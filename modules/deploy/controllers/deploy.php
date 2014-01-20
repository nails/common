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

class NAILS_Deploy extends NAILS_Controller
{
	public function pre()
	{

	}


	// --------------------------------------------------------------------------


	public function post()
	{

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