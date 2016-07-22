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

if (!function_exists('possessionise')) {
    function possessionise($sString)
    {
        return \Nails\Common\Helper\Inflector::possessionise($sString);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('pluralise')) {
    function pluralise($iValue, $sSingular, $sPlural = null)
    {
        return \Nails\Common\Helper\Inflector::pluralise($iValue, $sSingular, $sPlural);
    }
}

// --------------------------------------------------------------------------

//  Include the CodeIgniter original
include FCPATH . 'vendor/rogeriopradoj/codeigniter/system/helpers/inflector_helper.php';
