<?php

use Nails\Common\Service\Asset;
use Nails\Factory;

if (!function_exists('asset')) {
    function asset(string $sAsset)
    {
        /** @var Asset $oAsset */
        $oAsset = Factory::service('Asset');
        return $oAsset->buildUrl($sAsset);
    }
}
