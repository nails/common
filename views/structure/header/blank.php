<?php

use Nails\Factory;
use Nails\Common\Service;
use Nails\Common\Resource;

/**
 * @var Service\Asset     $oAsset
 * @var Service\Meta      $oMetaService
 * @var Resource\MetaData $oMetaData
 */

$oMetaService = Factory::service('Meta');
$oAsset       = Factory::service('Asset');

$sHtmlLang    = $oMetaData->getLocale()->getLanguage()->getLabel();
$sHtmlClasses = $oMetaData->getHtmlClasses()->implode();
$sBodyClasses = $oMetaData->getBodyClasses()->implode();

?>
<!DOCTYPE html>
<html class="<?=$sHtmlClasses?>" lang="<?=$sHtmlLang?>">
    <head>
        <?php

        echo '<title>';
        echo $oMetaData->getTitles()->implode();
        echo '</title>';

        // --------------------------------------------------------------------------

        //  Critical CSS
        echo $oAsset->criticalCss()->render();

        // --------------------------------------------------------------------------

        //  Meta tags
        $oMetaService->compileFromMetaData($oMetaData);
        echo $oMetaService->outputStr();

        // --------------------------------------------------------------------------

        //  Assets
        $oAsset->output($oAsset::TYPE_CSS);
        $oAsset->output($oAsset::TYPE_CSS_INLINE);
        $oAsset->output($oAsset::TYPE_JS_HEADER);
        $oAsset->output($oAsset::TYPE_JS_INLINE_HEADER);

        ?>
    </head>
    <body class="<?=$sBodyClasses?>">
