<?php

/**
 * This file provides langauge related helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

if (!function_exists('lang'))
{

    /**
     * Overriding the helper to incorporate language parameters
     * @param  string $sLine   The Language line
     * @param  array  $aParams The parameters to sub in
     * @param  string $sId     The ID of the form element
     * @return string
     */
    function lang($sLine, $aParams = array(), $sId = '')
    {
        $oCi   =& get_instance();
        $sLine = $oCi->lang->line($sLine, $aParams);

        if ($sId != '')
        {
            $sLine = '<label for="' . $sId . '">' . $sLine . "</label>";
        }

        return $sLine;
    }
}

// --------------------------------------------------------------------------

//  Include the CodeIgniter original
include 'vendor/rogeriopradoj/codeigniter/system/helpers/language_helper.php';
