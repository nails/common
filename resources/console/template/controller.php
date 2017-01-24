<?php

/**
 * This file is the template for the contents of controllers
 * Used by the console command when creating controllers.
 */

return <<<'EOD'
<?php

/**
 * The {{CONTROLLER_NAME}} controller
 *
 * @package  App
 * @category controller
 */

use App\Controller\Base;
use Nails\Factory;

class {{CONTROLLER_CLASS}} extends Base
{
    {{METHODS}}
}

EOD;
