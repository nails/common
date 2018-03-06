<?php

if (!isset($aPaths)) {

    $aPaths = [];
}

if (defined('FCPATH') && defined('BASE_URL')) {

    $aPaths[] = [
        FCPATH . 'assets/img/logo.png',
        BASE_URL . 'assets/img/logo.png',
    ];

    $aPaths[] = [
        FCPATH . 'assets/img/logo.jpg',
        BASE_URL . 'assets/img/logo.jpg',
    ];

    $aPaths[] = [
        FCPATH . 'assets/img/logo.gif',
        BASE_URL . 'assets/img/logo.gif',
    ];

    $aPaths[] = [
        FCPATH . 'assets/img/logo/logo.png',
        BASE_URL . 'assets/img/logo/logo.png',
    ];

    $aPaths[] = [
        FCPATH . 'assets/img/logo/logo.jpg',
        BASE_URL . 'assets/img/logo/logo.jpg',
    ];

    $aPaths[] = [
        FCPATH . 'assets/img/logo/logo.gif',
        BASE_URL . 'assets/img/logo/logo.gif',
    ];

    if (NAILS_BRANDING) {

        $aPaths[] = [
            FCPATH . 'vendor/nailsapp/module-asset/assets/img/nails/icon/icon@2x.png',
            BASE_URL . 'vendor/nailsapp/module-asset/assets/img/nails/icon/icon@2x.png',
        ];
    }
}

foreach ($aPaths as $path) {

    if (is_file($path[0])) {

        ?>
        <h1>
            <div id="logo-container">
                <img src="<?=$path[1]?>" id="logo"/>
            </div>
        </h1>
        <?php
        break;
    }
}
