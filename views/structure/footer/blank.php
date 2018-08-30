<?php

use Nails\Factory;

$oAsset = Factory::service('Asset');
$oAsset->output('JS');
$oAsset->output('JS-INLINE-FOOTER');
echo appSetting('site_custom_markup');
echo '</body></html>';
