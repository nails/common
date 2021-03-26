<?php

/**
 * Logo helper
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Helper;

use Nails\Components;
use Nails\Config;

class Logo
{
    const PATHS = [
        [
            NAILS_APP_PATH . 'assets/img/logo.png',
            BASE_URL . 'assets/img/logo.png',
        ],
        [
            NAILS_APP_PATH . 'assets/img/logo.jpg',
            BASE_URL . 'assets/img/logo.jpg',
        ],
        [
            NAILS_APP_PATH . 'assets/img/logo.gif',
            BASE_URL . 'assets/img/logo.gif',
        ],
        [
            NAILS_APP_PATH . 'assets/img/logo/logo.png',
            BASE_URL . 'assets/img/logo/logo.png',
        ],
        [
            NAILS_APP_PATH . 'assets/img/logo/logo.jpg',
            BASE_URL . 'assets/img/logo/logo.jpg',
        ],
        [
            NAILS_APP_PATH . 'assets/img/logo/logo.gif',
            BASE_URL . 'assets/img/logo/logo.gif',
        ],
    ];

    /**
     * Attempts to locate the project's logo
     * 1. Explicitly set in composer.json
     * 2. Explicitly set as a constant
     * 3. Looking in various paths
     *
     * @return string|null
     */
    public static function discover(): ?string
    {
        $oAppData = Components::getApp()->data;

        if (property_exists($oAppData, 'logo_url')) {
            return $oAppData->logo_url;
        } elseif (Config::get('APP_LOGO_URL')) {
            return Config::get('APP_LOGO_URL');
        }

        //  Go huntin'
        foreach (static::PATHS as $aPath) {
            if (fileExistsCS($aPath[0])) {
                return $aPath[1];
            }
        }

        if (Config::get('NAILS_BRANDING')) {
            return Config::get('NAILS_ASSETS_URL'). 'img/nails-logo.png';
        }

        return null;
    }
}
