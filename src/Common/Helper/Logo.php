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
            Config::get('NAILS_APP_PATH') . 'assets/img/logo.png',
            Config::get('BASE_URL') . 'assets/img/logo.png',
        ],
        [
            Config::get('NAILS_APP_PATH') . 'assets/img/logo.jpg',
            Config::get('BASE_URL') . 'assets/img/logo.jpg',
        ],
        [
            Config::get('NAILS_APP_PATH') . 'assets/img/logo.gif',
            Config::get('BASE_URL') . 'assets/img/logo.gif',
        ],
        [
            Config::get('NAILS_APP_PATH') . 'assets/img/logo/logo.png',
            Config::get('BASE_URL') . 'assets/img/logo/logo.png',
        ],
        [
            Config::get('NAILS_APP_PATH') . 'assets/img/logo/logo.jpg',
            Config::get('BASE_URL') . 'assets/img/logo/logo.jpg',
        ],
        [
            Config::get('NAILS_APP_PATH') . 'assets/img/logo/logo.gif',
            Config::get('BASE_URL') . 'assets/img/logo/logo.gif',
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

        if (Config::get('NAILS_BRANDING') {
            return Config::get('NAILS_URL') . 'module-asset/assets/img/nails/icon/icon@2x.png';
        }

        return null;
    }
}
