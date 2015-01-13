<?php

class NAILS_System extends NAILS_System_Controller
{
    /**
     * Constructs the controlelr
     */
    public function __construct()
    {
        parent::__construct();

        // --------------------------------------------------------------------------

        //  Load language file
        $this->lang->load('system');
    }

    // --------------------------------------------------------------------------

    /**
     * Handles 404 errors
     * @return void
     */
    public function render_404()
    {
        show_404();
    }
}

// --------------------------------------------------------------------------

/**
 * OVERLOADING NAILS' SYSTEM MODULES
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

if (! defined('NAILS_ALLOW_EXTENSION_SYSTEM')) {

    class System extends NAILS_System
    {
    }
}