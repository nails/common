<?php

use Nails\Common\Service\Asset;
use Nails\Common\Service\Input;
use Nails\Common\Service\MetaData;
use Nails\Config;
use Nails\Factory;

/** @var MetaData $metaDataService */
$metaDataService = Factory::service('MetaData');
/** @var Input $inputService */
$inputService = Factory::service('Input');
/** @var Asset $assetService */
$assetService = Factory::service('Asset');

$assetService->clear();
$assetService->load('nails.min.css', \Nails\Common\Constants::MODULE_SLUG);
$assetService->inline('.nails-password-protected { max-width: 500px }', $assetService::TYPE_CSS_INLINE);

$currentUrl = sprintf('%s%s', rtrim(siteUrl(), '/'), $inputService->server('REQUEST_URI'));

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>401 Unauthorised - <?=$metaDataService->getAppName()?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php

        $assetService->output($assetService::TYPE_CSS);
        $assetService->output($assetService::TYPE_CSS_INLINE);
        $assetService->output($assetService::TYPE_JS_HEADER);
        $assetService->output($assetService::TYPE_JS_INLINE_HEADER);

        ?>
    </head>
    <body>
        <div id="container">
            <div class="nails-password-protected login container center-screen">
                <div class="panel">
                    <div class="panel__header">
                        <h1 class="panel__title text-center">
                            This area is restricted
                        </h1>
                    </div>
                    <div class="panel__body">
                        <?php

                        echo form_open($currentUrl, 'class="form"');

                        if (!empty($message)) {
                            ?>
                            <p class="alert alert--danger">
                                <?=$message?>
                            </p>
                            <?php
                        }

                        ?>
                        <div class="form__group">
                            <label class="form__label" for="input-auth-user">Username</label>
                            <?=form_input('AUTH_USER', '', 'id="input-auth-user" placeholder="Username" class="form__control"')?>
                        </div>
                        <div class="form__group">
                            <label class="form__label" for="input-auth-pw">Password</label>
                            <?=form_password('AUTH_PW', '', 'id="input-auth-pw" placeholder="Password" class="form__control"')?>
                        </div>
                        <div class="form__actions">
                            <button type="submit" class="btn btn--block btn--primary">
                                Sign in
                            </button>
                        </div>
                        <?=form_close()?>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
