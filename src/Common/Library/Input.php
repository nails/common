<?php

/**
 * The class abstracts CI's Input class
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Library;

use Nails\Factory;
use CI_Input;

class Input extends CI_Input
{
    /**
     * Returns the user's IP Address. Extended to allow this method to be called from a command line environment.
     * This override may not be needed in future implementations of CodeIgniter.
     * @return string
     */
    public function ipAddress()
    {
        if ($this->is_cli_request()) {

            $hostname = gethostname();
            return gethostbyname($hostname);

        } else {

            return $this->ip_address();
        }
    }
}
