<?php

/**
 * This file is the template for the contents of admin controllers for models
 * Used by the console command when creating models.
 */

return <<<'EOD'
<?php

namespace App\Admin\App;

use Nails\Admin\Controller\DefaultController;

class {{ADMIN_CLASS_NAME}} extends DefaultController
{
    const CONFIG_MODEL_NAME     = '{{SERVICE_NAME}}';
    const CONFIG_MODEL_PROVIDER = 'app';
}


EOD;
