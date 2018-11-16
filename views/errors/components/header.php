<?php

if (!isset($aPaths)) {

    $aPaths = [];
}

if (defined('NAILS_APP_PATH') && defined('BASE_URL')) {

    $aPaths[] = [
        NAILS_APP_PATH . 'assets/img/logo.png',
        BASE_URL . 'assets/img/logo.png',
    ];

    $aPaths[] = [
        NAILS_APP_PATH . 'assets/img/logo.jpg',
        BASE_URL . 'assets/img/logo.jpg',
    ];

    $aPaths[] = [
        NAILS_APP_PATH . 'assets/img/logo.gif',
        BASE_URL . 'assets/img/logo.gif',
    ];

    $aPaths[] = [
        NAILS_APP_PATH . 'assets/img/logo/logo.png',
        BASE_URL . 'assets/img/logo/logo.png',
    ];

    $aPaths[] = [
        NAILS_APP_PATH . 'assets/img/logo/logo.jpg',
        BASE_URL . 'assets/img/logo/logo.jpg',
    ];

    $aPaths[] = [
        NAILS_APP_PATH . 'assets/img/logo/logo.gif',
        BASE_URL . 'assets/img/logo/logo.gif',
    ];

    if (NAILS_BRANDING) {

        $aPaths[] = [
            NAILS_APP_PATH . 'vendor/nails/module-asset/assets/img/nails/icon/icon@2x.png',
            BASE_URL . 'vendor/nails/module-asset/assets/img/nails/icon/icon@2x.png',
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
