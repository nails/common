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
<body class="<?=!$has_modules ? 'no-modules' : ''?>">
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

                $_link = 'admin/accounts/edit/' . active_user('id');
                $_attr = 'class="fancybox admin-branding-text-primary" data-fancybox-type="iframe"';

                if (active_user('profile_img')) {

                    $_img = img(array('src' => cdn_thumb(active_user('profile_img'), 30, 30), 'class' => 'avatar'));

                } else {

                    $_img =  img(array('src' => cdn_blank_avatar(30, 30), 'class' => 'avatar'));
                }

                echo anchor($_link, '<span class="name">' . active_user('first_name,last_name') . '</span>' . $_img, $_attr);

            ?>
            </div>
            <div class="shortcut to-frontend" rel="tipsy" title="Switch to front end">
                <?=anchor('', '<span class="fa fa-reply-all"></span>', 'class="admin-branding-text-primary"')?>
            </div>
            <?php

                $_admin_recovery = $this->session->userdata('admin_recovery');

                if ($this->session->userdata('admin_recovery')) {

                    echo '<div class="shortcut admin-recovery" rel="tipsy" title="Log back in as ' . $_admin_recovery->name . '">';
                        echo anchor('auth/override/login_as/' . $_admin_recovery->id . '/' . $_admin_recovery->hash, '<span class="fa fa-sign-out"></span>', 'class="admin-branding-text-primary"');
                    echo '</div>';
                }

            ?>
            <div class="shortcut logout" rel="tipsy" title="Log out">
                <?=anchor('auth/logout', '<span class="fa fa-power-off"></span>', 'class="admin-branding-text-primary"')?>
            </div>
        </div>
    </div>
    <div class="sidebar">
        <div class="nav-search admin-branding-background-secondary">
            <input type="search" placeholder="Type to search menu" />
        </div>
        <ul class="modules">
        <?php

            $_acl           = active_user('acl');
            $_mobile_menu   = array();
            $_counter       = 0;

            foreach ($loaded_modules as $config) {

                //  Get any notifications for this module if applicable
                $_class_name    = $config->class_name;
                $_notifications = $_class_name::notifications($config->class_index);

                $_class = '';

                if ($_counter == 0) {

                    $_class = 'first';
                }

                if ($_counter == (count($loaded_modules) - 1)) {

                    $_class = 'last';
                }

                $_counter++;

                // --------------------------------------------------------------------------

                /**
                 * Loop all the module methods and prepare an array, we do this so that we
                 * can make sure there'll be some output before we render the box header (i.e
                 * if a user only has access to an unlisted method they won't have an options
                 * here - e.g edit member - themselves - but not view members).
                 */

                $_options = array();

                foreach ($config->funcs as $method => $label) {

                    $_temp                      = new stdClass();
                    $_temp->is_active           = FALSE;
                    $_temp->label               = $label;
                    $_temp->method              = $method;
                    $_temp->url                 = 'admin/' . $config->class_name . '/' . $method;
                    $_temp->notification        = new stdClass();
                    $_temp->notification->type  = '';
                    $_temp->notification->title = '';
                    $_temp->notification->value = '';

                    //  Method enabled?
                    $_temp->is_active = $this->uri->rsegment(1) == $config->class_name && $this->uri->rsegment(2) == $method ? 'current' : '';

                    //  Notifications for this method?
                    if (!empty($_notifications[$method])) {

                        $_temp->notification->type      = isset($_notifications[$method]['type']) ? $_notifications[$method]['type'] : 'neutral';
                        $_temp->notification->title     = isset($_notifications[$method]['title']) ? $_notifications[$method]['title'] : '';
                        $_temp->notification->value     = isset($_notifications[$method]['value']) ? $_notifications[$method]['value'] : '';
                        $_temp->notification->options   = isset($_notifications[$method]['options']) ? $_notifications[$method]['options'] : '';
                    }

                    // --------------------------------------------------------------------------

                    //  Add to main $_options array
                    $_options[] = $_temp;
                }

                // --------------------------------------------------------------------------

                //  Render the options (if there are any)
                if ($_options) {

                    //  Add this to the mobile version of the menu
                    $_mobile_menu[$config->class_name]          = new stdClass();
                    $_mobile_menu[$config->class_name]->module  = $config->name;
                    $_mobile_menu[$config->class_name]->url     = NULL;
                    $_mobile_menu[$config->class_name]->subs    = array();

                    // --------------------------------------------------------------------------

                    //  Some modules are not sortable
                    $_not_sortable      = array();
                    $_not_sortable[]    = 'dashboard';
                    $_not_sortable[]    = 'settings';
                    $_not_sortable[]    = 'utilities';

                    $_sortable = array_search($config->class_name, $_not_sortable) !== FALSE ? 'no-sort' : '';

                    // --------------------------------------------------------------------------

                    //  Initial open/close state?
                    $_user_nav_pref = $this->admin_model->get_admin_data('nav');

                    if ($_user_nav_pref) {

                        if (empty($_user_nav_pref->{$config->class_index}->open)) {

                            $_state = 'closed';

                        } else {

                            $_state = 'open';
                        }

                    } else {

                        //  Closed by default
                        $_state = 'closed';
                    }

                    ?>
                    <li class="module <?=$_sortable?> admin-branding-background-primary" data-module="<?=$config->class_index?>" data-initial-state="<?=$_state?>">
                        <div class="box <?=$_class?>" id="box_<?=str_replace(':', '__', $config->class_index)?>">
                            <h2 class="<?=$config->class_name?>">
                                <div class="icon admin-branding-text-highlight">
                                <?php

                                    echo $_sortable !== 'no-sort' ? '<span class="handle admin-branding-background-primary fa fa-navicon"></span>' : '';
                                    echo !empty($config->icon) ? '<b class="fa fa-fw ' . $config->icon . '"></b>' : '<b class="fa fa-fw fa-cog"></b>';

                                ?>
                                </div>
                                <span class="module-name">
                                    <?=$config->name?>
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

                                    foreach ($_options as $option) {

                                        //  Add to the mobile menu
                                        $_mobile_menu[$config->class_name]->subs[$option->method]           = new stdClass();
                                        $_mobile_menu[$config->class_name]->subs[$option->method]->label    = $option->label;
                                        $_mobile_menu[$config->class_name]->subs[$option->method]->url      = $option->url;

                                        //  Render
                                        echo '<li class="' . $option->is_active . '">';

                                            //  Notification
                                            switch ($option->notification->type) {

                                                case 'split':

                                                    $_mobile_notification   = array();

                                                    foreach ($option->notification->options as $notification) {

                                                        $_split_type    = isset($notification['type']) ? $notification['type'] : 'neutral';
                                                        $_split_title   = isset($notification['title']) ? $notification['title'] : '';

                                                        if ($notification['value']) {

                                                            $_tipsy = $_split_title ? ' title="' . $_split_title . '" rel="tipsy-right"' : '';
                                                            echo '<span class="indicator split ' . $_split_type .  '"' . $_tipsy . '>' . number_format($notification['value']) . '</span>';

                                                            //  Update mobile menu
                                                            if ($_split_title) {

                                                                $_mobile_notification[] = $_split_title . ': ' . number_format($notification['value']);

                                                            } else {

                                                                $_mobile_notification[] = number_format($notification['value']);
                                                            }
                                                        }
                                                    }

                                                    $_mobile_menu[$config->class_name]->subs[$option->method]->label .= ' (' . implode(', ', $_mobile_notification) . ')';
                                                    break;

                                                default:

                                                    if ($option->notification->value) {

                                                        $_tipsy = $option->notification->title ? ' title="' . $option->notification->title . '" rel="tipsy-right"' : '';
                                                        echo '<span class="indicator ' . $option->notification->type . '"' . $_tipsy . '>' . number_format($option->notification->value) . '</span>';

                                                        if ($option->notification->title) {

                                                            $_mobile_menu[$config->class_name]->subs[$option->method]->label .= ' (' . $option->notification->title . ': ' . number_format($option->notification->value) . ')';

                                                        } else {

                                                            $_mobile_menu[$config->class_name]->subs[$option->method]->label .= ' (' . number_format($option->notification->value) . ')';
                                                        }
                                                    }
                                                    break;
                                            }

                                            //  Link
                                            echo anchor($option->url, $option->label);

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
        <?php

            //  Build the Dropdown menu
            echo '<div id="mobile-menu-main">';
                echo '<select name="mobile-menu">';
                    echo '<option value="" disabled>' . lang('admin_nav_menu') . '</option>';

                    $_module    = $this->uri->rsegment(1);
                    $_method    = $this->uri->rsegment(2);

                    foreach ($_mobile_menu as $module => $item) {

                        echo '<optgroup label="' . str_replace('"', '\"', $item->module) . '">';
                        foreach ($item->subs as $method => $sub) {

                            $_selected = $_module == $module && $_method == $method ? 'selected="selected"' : '';
                            echo '<option value="' . $sub->url . '" ' . $_selected . '>' . $sub->label . '</option>';
                        }
                        echo '</optgroup>';
                    }
                echo '</select>';
            echo '</div>';

        ?>
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
