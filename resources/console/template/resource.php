<?php

/**
 * This file is the template for the contents of model resources
 * Used by the console command when creating models.
 */

return <<<'EOD'
<?php

/**
 * This class represents objects dispensed by the {{SERVICE_NAME}} model
 *
 * @package  App\Resource
 * @category resource
 */

namespace {{RESOURCE_NAMESPACE}};

use Nails\Common\Resource\Entity;

/**
 * Class {{RESOURCE_CLASS_NAME}}
 *
 * @package {{RESOURCE_NAMESPACE}}
 */
class {{RESOURCE_CLASS_NAME}} extends Entity
{
}

EOD;
