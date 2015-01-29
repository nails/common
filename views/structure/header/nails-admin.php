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
        echo !empty($page->module->name) ? $page->module->name . ' - ' : NULL;
        echo !empty($page->title) ? $page->title . ' - ' : NULL;
        echo APP_NAME;

    ?></title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <!--    NAILS JS GLOBALS    -->
    <script style="text/javascript">
        window.ENVIRONMENT      = '<?=strtoupper(ENVIRONMENT)?>';
        window.SITE_URL         = '<?=site_url('', isPageSecure())?>';
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

        $brandColorPrimary   = app_setting('primary_colour', 'admin')   ? app_setting('primary_colour', 'admin')   : '#171D20';
        $brandColorSecondary = app_setting('secondary_colour', 'admin') ? app_setting('secondary_colour', 'admin') : '#515557';
        $brandColorHighlight = app_setting('highlight_colour', 'admin') ? app_setting('highlight_colour', 'admin') : '#F09634';

    ?>
    <style type="text/css">

        .admin-branding-text-primary
        {
            color: <?=$brandColorPrimary?>;
        }
        .admin-branding-background-primary
        {
            background: <?=$brandColorPrimary?>;
        }

        .admin-branding-text-secondary
        {
            color: <?=$brandColorSecondary?>;
        }
        .admin-branding-background-secondary
        {
            background: <?=$brandColorSecondary?>;
        }

        .admin-branding-text-highlight
        {
            color: <?=$brandColorHighlight?>;
        }
        .admin-branding-background-highlight
        {
            background: <?=$brandColorHighlight?>;
        }

        table thead tr th
        {
            background-color: <?=$brandColorPrimary?>;
        }

    </style>
</head>
<body class="<?=!$adminControllers ? 'no-modules' : ''?>">
    <div class="header">
        <div class="app-name">
            <a href="<?=site_url('admin')?>">
                <span class="app-name admin-branding-text-primary">
                    <?=APP_NAME?>
                </span>
            </a>
        </div>
        <div class="user-shortcuts">
            <div class="shortcut loggedin-as" rel="tipsy" title="Logged in as <?=active_user('first_name,last_name')?>">
            <?php

                $url  = 'admin/accounts/edit/' . active_user('id');
                $attr = 'class="fancybox admin-branding-text-primary" data-fancybox-type="iframe"';

                if (active_user('profile_img')) {

                    $img = img(array('src' => cdn_thumb(active_user('profile_img'), 30, 30), 'class' => 'avatar'));

                } else {

                    $img = img(array('src' => cdn_blank_avatar(30, 30), 'class' => 'avatar'));
                }

                echo anchor(
                    $url,
                    '<span class="name">' . active_user('first_name,last_name') . '</span>' . $img,
                    $attr
                );

            ?>
            </div>
            <div class="shortcut to-frontend" rel="tipsy" title="Switch to front end">
                <?=anchor('', '<span class="fa fa-reply-all"></span>', 'class="admin-branding-text-primary"')?>
            </div>
            <?php

                $adminRecovery = $this->session->userdata('admin_recovery');

                if ($this->session->userdata('admin_recovery')) {

                    echo '<div class="shortcut admin-recovery" rel="tipsy" title="Log back in as ' . $adminRecovery->name . '">';
                        echo anchor(
                            'auth/override/login_as/' . $adminRecovery->id . '/' . $adminRecovery->hash,
                            '<span class="fa fa-sign-out"></span>',
                            'class="admin-branding-text-primary"'
                        );
                    echo '</div>';
                }

            ?>
            <div class="shortcut logout" rel="tipsy" title="Log out">
            <?php

                echo anchor(
                    'auth/logout',
                    '<span class="fa fa-power-off"></span>',
                    'class="admin-branding-text-primary"'
                );

            ?>
            </div>
        </div>
    </div>
    <div class="sidebar">
        <div class="nav-search admin-branding-background-secondary">
            <input type="search" placeholder="Type to search menu" />
        </div>
        <ul class="modules">
        <?php

            //  An array of modules which shouldn't be sorted
            $notSortable = array('admin', 'utilities', 'settings');

            foreach ($adminControllers as $urlModule => $module) {

                foreach ($module->controllers as $urlController => $controller) {

                    $sortable   = !in_array($urlController, $notSortable) ? 'sortable' : '';
                    $moduleName = !empty($controller['details']->name) ? $controller['details']->name : '';
                    $options    = !empty($controller['details']->funcs) ? $controller['details']->funcs : '';
                    $icon       = !empty($controller['details']->icon) ? $controller['details']->icon : 'fa-cog';

                    ?>
                    <li class="module admin-branding-background-primary <?=$sortable?>" data-initial-state="closed">
                        <div class="box">
                            <h2>
                                <div class="icon admin-branding-text-highlight">
                                <?php

                                    //  Sorting handle
                                    if ($sortable) {
                                        echo '<span class="handle admin-branding-background-primary fa fa-navicon"></span>';
                                    }

                                    //  Icon
                                    echo  '<b class="fa fa-fw ' . $icon . '"></b>';

                                ?>
                                </div>
                                <span class="module-name">
                                    <?=$moduleName?>
                                </span>
                                <a href="#" class="toggle">
                                    <span class="toggler">
                                        <span class="close">
                                            <b class="fa fa-minus"></b>
                                        </span>
                                        <span class="open">
                                            <b class="fa fa-plus"></b>
                                        </span>
                                    </span>
                                </a>
                            </h2>
                            <div class="box-container">
                                <ul>
                                <?php

                                    foreach ($options as $method => $label) {

                                        echo '<li>';
                                            echo anchor(
                                                'admin/' . $urlModule . '/' . $urlController . '/' . $method,
                                                $label
                                            );
                                        echo '</li>';
                                    }

                                ?>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <?php

                }
            }
        ?>
        </ul>
        <p class="text-center">
            <a href="#" id="admin-nav-reset">Reset Nav</a>
        </p>
        <div class="no-modules">
            <p class="system-alert error">
                <strong>No modules available.</strong>
                </br>
                This is a configuration error and should be reported to the app developers.
            </p>
        </div>
    </div>
    <div class="content">
        <div class="content_inner">
            <?php

                echo '<div class="page-title">';

                    //  Page title
                    if (!empty($page->module->name) && !empty($page->title)) {

                        echo '<h1>';
                            echo $page->module->name . ' &rsaquo; ' . $page->title;
                        echo '</h1>';

                    } elseif (empty($page->module->name) && !empty($page->title)) {

                        echo '<h1>';
                            echo $page->title;
                        echo '</h1>';

                    } elseif (!empty($page->module->name)) {

                        echo '<h1>';
                            echo $page->module->name;
                        echo '</h1>';
                    }

                echo '</div>';

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

            ?>
            <div class="js_error" style="display:none;">
                <p>
                    <span class="title"><?=lang('js_error_header')?></span>
                    <span class="message"></span>
                </p>
            </div>
