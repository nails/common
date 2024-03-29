<?php

/**
 * This file provides email related helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

/**
 * Validate email address
 *
 * @access  public
 * @return  bool
 */
if (!function_exists('valid_email')) {

    function valid_email(string $sAddress): bool
    {
        if (function_exists('filter_var')) {
            return (bool) filter_var($sAddress, FILTER_VALIDATE_EMAIL);

        } else {

            $sPattern = "/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix";
            return (!preg_match($sPattern, $sAddress)) ? false : true;
        }
    }
}

// --------------------------------------------------------------------------

//  Include the CodeIgniter original
include NAILS_CI_SYSTEM_PATH . 'helpers/email_helper.php';
