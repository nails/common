<?php

/**
 * This file provides inflection related helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

if (!function_exists('possessive')) {
    function possessive(string $sString)
    {
        return \Nails\Common\Helper\Inflector::possessive($sString);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('possessionise')) {
    function possessionise(string $sString)
    {
        return \Nails\Common\Helper\Inflector::possessionise($sString);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('pluralise')) {
    function pluralise(int $iValue, string $sSingular, $sPlural = '')
    {
        return \Nails\Common\Helper\Inflector::pluralise($iValue, $sSingular, $sPlural);
    }
}

// --------------------------------------------------------------------------

//  Include the CodeIgniter original
include NAILS_CI_SYSTEM_PATH . 'helpers/inflector_helper.php';
