<?php

/**
 * This class allows the Nails Factory to load CodeIgniter helpers in the same way as it loads native helpers.
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

if (!function_exists('nl2br_except_pre')) {
    /**
     * Convert newlines to HTML line breaks except within PRE tags
     *
     * @param  string $sString The input string
     *
     * @return string
     */
    function nl2br_except_pre($sString)
    {
        $oTypography = \Nails\Factory::service('Typography');
        return $oTypography->nl2br_except_pre($sString);
    }
}

// ------------------------------------------------------------------------

if (!function_exists('auto_typography')) {
    /**
     * Auto Typography Wrapper Function
     *
     * @param    string $sString           The input string
     * @param    bool   $bReduceLineBreaks Whether to reduce multiple instances of double newlines to two
     *
     * @return    string
     */
    function auto_typography($sString, $bReduceLineBreaks = false)
    {
        $oTypography = \Nails\Factory::service('Typography');
        return $oTypography->auto_typography($sString, $bReduceLineBreaks);
    }
}

// --------------------------------------------------------------------------

include FCPATH . 'vendor/codeigniter/framework/system/helpers/typography_helper.php';
