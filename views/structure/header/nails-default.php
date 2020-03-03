<?php

use Nails\Config;
use Nails\Factory;

$oView = Factory::service('View');
$oView->load('structure/header/blank');

?>
<div class="container">
    <div class="row text-center" style="margin-top:1em;">
        <h1>
            <?=anchor('', Config::get('APP_NAME'), 'style="text-decoration:none;color:inherit;"')?>
        </h1>
    </div>
    <hr />
    <?php

    $aMessages = [
        'error'    => 'error',
        'negative' => 'error',
        'success'  => 'success',
        'positive' => 'success',
        'info'     => 'info',
        'warning'  => 'warning',
    ];


    foreach ($aMessages as $sVariable => $sClass) {
        if (!empty(${$sVariable})) {
            ?>
            <p class="alert alert-<?=$sClass?>">
                <?=${$sVariable}?>
            </p>
            <?php
        }
    }
