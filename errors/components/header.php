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

    if (NAILS_BRANDING) {

        $paths[] = array(
            FCPATH . 'vendor/nailsapp/module-asset/assets/img/nails/icon/icon@2x.png',
            BASE_URL . 'vendor/nailsapp/module-asset/assets/img/nails/icon/icon@2x.png'
        );
    }
}

foreach ($paths as $path) {

    if (is_file($path[0])) {

        ?>
        <h1>
            <div id="logoContainer">
                <img src="<?=$path[1]?>" id="logo" />
            </div>
        </h1>
        <?php
        break;
    }
}
