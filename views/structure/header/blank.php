<?php

use Nails\Factory;
use Nails\Common\Service;

/**
 * @var \Nails\Common\Resource\MetaData $oMetaData
 */

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

        //  Meta tags
        /** @var Service\Meta $oMetaService */
        $oMetaService = Factory::service('Meta');
        $oMetaService->compileFromMetaData($oMetaData);
        echo $oMetaService->outputStr();

        // --------------------------------------------------------------------------

        //  Assets
        /** @var Service\Asset $oAssetService */
        $oAssetService = Factory::service('Asset');
        $oAssetService->compileGlobalData();
        $oAssetService->output('CSS');
        $oAssetService->output('CSS-INLINE');
        $oAssetService->output('JS-INLINE-HEADER');

        ?>
    </head>
    <body class="<?=$sBodyClasses?>">
