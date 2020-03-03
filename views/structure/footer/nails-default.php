<?php

use Nails\Factory;

?>
    <hr />
    <div class="row">
        <p class="text-center">
            <small>
                &copy; <?=\Nails\Config::get('APP_NAME') . ' ' . date('Y')?>
                <br />
                <?=lang('nails_footer_powered_by', [\Nails\Config::get('NAILS_PACKAGE_URL'), \Nails\Config::get('NAILS_PACKAGE_NAME')])?>
            </small>
        </p>
    </div>
</div>
<?php

$oView = Factory::service('View');
$oView->load('structure/footer/blank');
