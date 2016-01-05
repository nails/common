<!DOCTYPE html>
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
    <head>
        <?php

        echo '<title>';

        if (!empty($page->seo->title)) {

            echo $page->seo->title . ' - ';

        } elseif (!empty($page->title)) {

            echo $page->title . ' - ';
        }

        echo APP_NAME;

        echo '</title>';

        // --------------------------------------------------------------------------

        //  Meta tags
        echo $this->meta->outputStr();

        // --------------------------------------------------------------------------

        //  Assets
        $this->asset->output('CSS');
        $this->asset->output('CSS-INLINE');
        $this->asset->output('JS-INLINE-HEADER');

        ?>
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="<?=NAILS_ASSETS_URL . 'bower_components/html5shiv/dist/html5shiv.js'?>"></script>
          <script src="<?=NAILS_ASSETS_URL . 'bower_components/respond/dest/respond.min.js'?>"></script>
        <![endif]-->
    </head>
    <body>
