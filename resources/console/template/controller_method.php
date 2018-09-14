<?php

/**
 * This file is the template for the contents of controller methods
 * Used by the console command when creating controllers.
 */

return <<<'EOD'
public function {{METHOD_NAME}}()
{
    $oView = Factory::service('View');
    $oView->load('structure/header', $this->data);
    $oView->load('{{METHOD_VIEW}}', $this->data);
    $oView->load('structure/footer', $this->data);
}
EOD;
