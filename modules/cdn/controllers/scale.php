<?php

/**
 * Name:		Scale
 *
 * Description:	Generates a scaled version of an image
 *
 **/

//	Include _cdn.php; executes common functionality
require_once '_cdn.php';
require_once 'thumb.php';

/**
 * OVERLOADING NAILS' CDN MODULES
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Scale extends Thumb
{
	/**
	 * Generate the thumbnail
	 *
	 * @access	public
	 * @return	void
	 **/
	public function index()
	{
		return parent::index( 'SCALE' );
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' CDN MODULES
 *
 * The following block of code makes it simple to extend one of the core CDN
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SCALE' ) ) :

	class Scale extends NAILS_Scale
	{
	}

endif;


/* End of file scale.php */
/* Location: ./modules/cdn/controllers/scale.php */