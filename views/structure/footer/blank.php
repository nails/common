<?php

use Nails\Factory;

/** @var \Nails\Common\Service\Asset $oAsset */
$oAsset = Factory::service('Asset');
$oAsset->output($oAsset::TYPE_JS);
$oAsset->output($oAsset::TYPE_JS_INLINE_FOOTER);
echo appSetting('site_custom_markup', 'site');
echo '</body></html>';
