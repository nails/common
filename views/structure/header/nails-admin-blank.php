<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
    <meta charset="UTF-8" />
    <title>
    <?php

        echo lang('admin_word_short') . ' - ';
        echo isset($page->module->name) ? $page->module->name . ' - ' : NULL;
        echo isset($page->title) ? $page->title . ' - ' : NULL;
        echo APP_NAME;

    ?></title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <!--    NAILS JS GLOBALS    -->
    <script style="text/javascript">
        window.ENVIRONMENT      = '<?=strtoupper(ENVIRONMENT)?>';
        window.SITE_URL         = '<?=site_url('', page_is_secure())?>';
        window.NAILS            = {};
        window.NAILS.URL        = '<?=NAILS_ASSETS_URL?>';
        window.NAILS.LANG       = {};
        window.NAILS.USER       = {};
        window.NAILS.USER.ID    = <?=active_user('id')?>;
        window.NAILS.USER.FNAME = '<?=active_user('first_name')?>';
        window.NAILS.USER.LNAME = '<?=active_user('last_name')?>';
        window.NAILS.USER.EMAIL = '<?=active_user('email')?>';
    </script>
    <noscript>
        <style type="text/css">

            .js-only
            {
                display:none;
            }

        </style>
    </noscript>
    <!--    JS LOCALISATION -->
    <script style="text/javascript">
        window.NAILS.LANG.non_html5 = '<?=str_replace("'", "\'", lang('js_error_non_html5'))?>';
        window.NAILS.LANG.no_save   = '<?=str_replace("'", "\'", lang('js_error_saving'))?>';
    </script>
    <!--    ASSETS  -->
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700" rel="stylesheet" type="text/css">
    <?php

        echo $this->asset->output('CSS');
        echo $this->asset->output('CSS-INLINE');
        echo $this->asset->output('JS');

    ?>
    <link rel="stylesheet" type="text/css" media="print" href="<?=NAILS_ASSETS_URL . 'css/nails.admin.print.css'?>" />
    <?php

        $_primary   = app_setting('primary_colour', 'admin')        ? app_setting('primary_colour', 'admin')        : '#171D20';
        $_secondary = app_setting('secondary_colour', 'admin')  ? app_setting('secondary_colour', 'admin')  : '#515557';
        $_highlight = app_setting('highlight_colour', 'admin')  ? app_setting('highlight_colour', 'admin')  : '#F09634';

    ?>
    <style type="text/css">

        .admin-branding-text-primary
        {
            color: <?=$_primary?>;
        }
        .admin-branding-background-primary
        {
            background: <?=$_primary?>;
        }

        .admin-branding-text-secondary
        {
            color: <?=$_secondary?>;
        }
        .admin-branding-background-secondary
        {
            background: <?=$_secondary?>;
        }

        .admin-branding-text-highlight
        {
            color: <?=$_highlight?>;
        }
        .admin-branding-background-highlight
        {
            background: <?=$_highlight?>;
        }

        table thead tr th
        {
            background-color : <?=$_primary?>;
        }

    </style>
</head>
<body class="blank">
<?php

    if (!empty($error)) {

        echo '<div class="system-alert error">';
            echo '<p>' . $error . '</p>';
        echo '</div>';
    }

    if (!empty($success)) {

        echo '<div class="system-alert success">';
            echo '<p>' . $success . '</p>';
        echo '</div>';
    }

    if (!empty($message)) {

        echo '<div class="system-alert message">';
            echo '<p>' . $message . '</p>';
        echo '</div>';
    }

    if (!empty($notice)) {

        echo '<div class="system-alert notice">';
            echo '<p>' . $notice . '</p>';
        echo '</div>';
    }
