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
<!--[if IE 8 ]>
<html class="ie ie8 <?=$sHtmlClasses?>" lang="<?=$sHtmlLang?>>"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html class="<?=$sHtmlClasses?>" lang="<?=$sHtmlLang?>"> <!--<![endif]-->
    <head>
        <?php

        echo '<title>';
        echo $oMetaData->getTitles()->implode();
        echo '</title>';

        // --------------------------------------------------------------------------

        //  Meta tags
        /** @var Service\Meta $oMetaService */
        $oMetaService = Factory::service('Meta');
        echo $oMetaService->outputStr();

        // --------------------------------------------------------------------------

        //  Assets
        /** @var Service\Asset $oAssetService */
        $oAssetService = Factory::service('Asset');
        $oAssetService->output('CSS');
        $oAssetService->output('CSS-INLINE');
        $oAssetService->output('JS-INLINE-HEADER');

        ?>
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="<?=NAILS_ASSETS_URL . 'bower_components/html5shiv/dist/html5shiv.js'?>"></script>
        <script src="<?=NAILS_ASSETS_URL . 'bower_components/respond/dest/respond.min.js'?>"></script>
        <![endif]-->
    </head>
    <body class="<?=$sBodyClasses?>">
