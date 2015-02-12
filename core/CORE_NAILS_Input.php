<?php

/**
 * This class extends the CodeIgniter Input class
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

class CORE_NAILS_Input extends CI_Input
{
    /**
     * Returns the user's IP Address. Extended to allow this method to be called from a command line environment.
     * This override may not be needed in future implementations of CodeIgniter.
     * @return string
     */
    public function ip_address()
    {
        if ($this->is_cli_request()) {

            $hostname = gethostname();
            return gethostbyname($hostname);

        } else {

            return parent::ip_address();
        }
    }
}
