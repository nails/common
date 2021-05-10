<?php

use Nails\Factory;

/** @var \Nails\Common\Service\Asset $oAsset */
$oAsset = Factory::service('Asset');
$oAsset->output($oAsset::TYPE_JS);
$oAsset->output($oAsset::TYPE_JS_INLINE_FOOTER);
echo appSetting(\Nails\Common\Settings\Site::KEY_CUSTOM_MARKUP, \Nails\Common\Constants::MODULE_SLUG);
echo '</body></html>';
