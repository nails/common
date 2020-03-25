<?php

use Nails\Factory;

/** @var \Nails\Common\Service\Asset $oAsset */
$oAsset = Factory::service('Asset');
$oAsset->compileGlobalData();
$oAsset->output('JS');
$oAsset->output('JS-INLINE-FOOTER');
echo appSetting('site_custom_markup', 'site');
echo '</body></html>';
