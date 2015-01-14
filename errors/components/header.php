<h1>
<?php

    if (!isset($paths)) {

        $paths = array();
    }

    if (defined('FCPATH') && defined('BASE_URL')) {

        $paths[] = array(
            FCPATH . 'assets/img/logo.png',
            BASE_URL . 'assets/img/logo.png'
        );

        $paths[] = array(
            FCPATH . 'assets/img/logo.jpg',
            BASE_URL . 'assets/img/logo.jpg'
        );

        $paths[] = array(
            FCPATH . 'assets/img/logo.gif',
            BASE_URL . 'assets/img/logo.gif'
        );

        $paths[] = array(
            FCPATH . 'assets/img/logo/logo.png',
            BASE_URL . 'assets/img/logo/logo.png'
        );

        $paths[] = array(
            FCPATH . 'assets/img/logo/logo.jpg',
            BASE_URL . 'assets/img/logo/logo.jpg'
        );

        $paths[] = array(
            FCPATH . 'assets/img/logo/logo.gif',
            BASE_URL . 'assets/img/logo/logo.gif'
        );

        $paths[] = array(
            FCPATH . 'vendor/nailsapp/module-asset/asset/assets/img/nails/icon/icon@2x.png',
            BASE_URL . 'vendor/nailsapp/module-asset/asset/assets/img/nails/icon/icon@2x.png'
        );
    }

    foreach ($paths AS $path) {

        if (is_file($path[0])) {

            echo '<div id="logoContainer">';
                echo '<img src="' . $path[1] . '" id="logo" />';
            echo '</div>';
            break;
        }
    }
?>
</h1>