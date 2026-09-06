<?php

/**
 * This file provides language related helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

use Nails\Common\Service\Translation;
use Nails\Factory;

if (!function_exists('lang')) {

    /**
     * Returns a language line, with parameters substituted (see Translation::line())
     *
     * @param string       $sLine   The language line
     * @param array|string $aParams The parameters to sub in
     * @param string       $sId     The ID of the form element (wraps the line in a <label>)
     *
     * @return string|false
     */
    function lang($sLine, $aParams = [], $sId = '')
    {
        /** @var Translation $oTranslation */
        $oTranslation = Factory::service('Translation');
        $sLine        = $oTranslation->line((string) $sLine, $aParams ?: null);

        if ($sId != '') {
            $sLine = '<label for="' . $sId . '">' . $sLine . "</label>";
        }

        return $sLine;
    }
}

// --------------------------------------------------------------------------

//  Include the CodeIgniter original
include NAILS_CI_SYSTEM_PATH . 'helpers/language_helper.php';
