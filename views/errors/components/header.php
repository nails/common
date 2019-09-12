<?php

$sLogoUrl = logoDiscover();
if (!empty($sLogoUrl)) {
    ?>
    <h1>
        <div id="logo-container">
            <img src="<?=$sLogoUrl?>" id="logo"/>
        </div>
    </h1>
    <?php
}

