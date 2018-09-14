<?php

use Nails\Factory;

$aPageTitle = [];
if (!empty($page->seo->title)) {
    $aPageTitle[] = $page->seo->title;
} elseif (!empty($page->title)) {
    $aPageTitle[] = $page->title;
}

$aPageTitle[] = APP_NAME;

if (!empty($page->html_lang)) {
    $sHtmlLang = $page->html_lang;
} else {
    $sHtmlLang = 'en';
}

if (!empty($page->html_class) && is_array($page->html_class)) {
    $aHtmlClass = $page->html_class;
} elseif (!empty($page->html_class)) {
    $aHtmlClass = [$page->html_class];
} else {
    $aHtmlClass = [];
}

if (!empty($page->body_class) && is_array($page->body_class)) {
    $aBodyClass = $page->body_class;
} elseif (!empty($page->body_class)) {
    $aBodyClass = [$page->body_class];
} else {
    $aBodyClass = [];
}

?>
<!DOCTYPE html>
<!--[if IE 8 ]>
<html class="ie ie8 <?=implode(' ', $aHtmlClass)?>" lang="<?=$sHtmlLang?>>"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html class="<?=implode(' ', $aHtmlClass)?>" lang="<?=$sHtmlLang?>"> <!--<![endif]-->
    <head>
        <?php

        echo '<title>';
        echo implode(' - ', $aPageTitle);
        echo '</title>';

        // --------------------------------------------------------------------------

        //  Meta tags
        $oMeta = Factory::service('Meta');
        echo $oMeta->outputStr();

        // --------------------------------------------------------------------------

        //  Assets
        $oAsset = Factory::service('Asset');
        $oAsset->output('CSS');
        $oAsset->output('CSS-INLINE');
        $oAsset->output('JS-INLINE-HEADER');

        ?>
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="<?=NAILS_ASSETS_URL . 'bower_components/html5shiv/dist/html5shiv.js'?>"></script>
        <script src="<?=NAILS_ASSETS_URL . 'bower_components/respond/dest/respond.min.js'?>"></script>
        <![endif]-->
    </head>
    <body class="<?=implode(' ', $aBodyClass)?>">
