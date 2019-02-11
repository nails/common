<?php

/**
 * This file is the template for the contents of controller methods
 * Used by the console command when creating controllers.
 */

return <<<'EOD'
public function {{METHOD_NAME}}(): void
{
    Factory::service('View')
        ->load([
            'structure/header',
            '{{METHOD_VIEW}}',
            'structure/footer',
        ]);
}
EOD;
