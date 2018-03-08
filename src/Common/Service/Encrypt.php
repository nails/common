<?php

/**
 * The class abstracts CI's Encryption class.
 *
 * @todo        - remove dependency on CI
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

class Encrypt extends \CI_Encrypt
{
    /**
     * Encrypt constructor. Overriding so as not to require `get_instance()`
     */
    public function __construct()
    {
        if (function_exists('mcrypt_encrypt') === false) {
            show_error('The Encrypt library requires the Mcrypt extension.');
        }

        log_message('debug', "Encrypt Class Initialized");
    }

    // --------------------------------------------------------------------------

    /**
     * Fetch the encryption key
     *
     * Returns it as MD5 in order to have an exact-length 128 bit key.
     * Mcrypt is sensitive to keys that are not the correct length
     *
     * @access	public
     * @param	string
     * @return	string
     */
    function get_key($key = '')
    {
        if ($key == '') {
            if ($this->encryption_key != '') {
                return $this->encryption_key;
            }

            $key = defined('APP_PRIVATE_KEY') ? md5(APP_PRIVATE_KEY): md5('');
            if ($key == FALSE) {
                show_error('In order to use the encryption class requires that you set an encryption key in your config file.');
            }
        }

        return md5($key);
    }
}
