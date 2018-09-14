<?php

use Nails\Factory;

?>
    <hr />
    <div class="row">
        <p class="text-center">
            <small>
                &copy; <?=APP_NAME . ' ' . date('Y')?>
                <br />
                <?=lang('nails_footer_powered_by', array(NAILS_PACKAGE_URL, NAILS_PACKAGE_NAME))?>
            </small>
        </p>
    </div>
</div>
<?php

$oView = Factory::service('View');
$oView->load('structure/footer/blank');
