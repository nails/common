<?php

if (!isset($aPaths)) {
    $aPaths = [];
}

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
        NAILS_PATH . 'module-asset/assets/img/nails/icon/icon@2x.png',
        NAILS_URL . 'module-asset/assets/img/nails/icon/icon@2x.png',
    ];
}

foreach ($aPaths as $aPath) {
    if (is_file($aPath[0])) {
        ?>
        <h1>
            <div id="logo-container">
                <img src="<?=$aPath[1]?>" id="logo"/>
            </div>
        </h1>
        <?php
        break;
    }
}
