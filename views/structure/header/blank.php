<?php

use Nails\Factory;
use Nails\Common\Service;
use Nails\Common\Resource;

/**
 * @var Service\Asset     $oAssetService
 * @var Service\Meta      $oMetaService
 * @var Resource\MetaData $oMetaData
 */

$oMetaService  = Factory::service('Meta');
$oAssetService = Factory::service('Asset');

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
        echo $oAssetService->criticalCss()->render();

        // --------------------------------------------------------------------------

        //  Meta tags
        $oMetaService->compileFromMetaData($oMetaData);
        echo $oMetaService->outputStr();

        // --------------------------------------------------------------------------

        //  Assets
        $oAssetService->output('CSS');
        $oAssetService->output('CSS-INLINE');
        $oAssetService->output('JS-INLINE-HEADER');

        ?>
    </head>
    <body class="<?=$sBodyClasses?>">
