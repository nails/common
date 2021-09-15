<?php

use Nails\Config;
use Nails\Common\Service\UserFeedback;
use Nails\Common\Service\View;
use Nails\Factory;

/** @var View $oView */
$oView = Factory::service('View');
/** @var UserFeedback $oUserFeedback */
$oUserFeedback = Factory::service('UserFeedback');


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

    foreach ($oUserFeedback->getTypes() as $sType) {

        $sValue = (string) $oUserFeedback->get($sType);

        if (!empty($sValue)) {
            ?>
            <p class="alert alert-<?=strtolower($sType)?>">
                <?=$sValue?>
            </p>
            <?php
        }
    }
