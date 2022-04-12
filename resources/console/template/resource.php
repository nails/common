<?php

/**
 * This file is the template for the contents of model resources
 * Used by the console command when creating models.
 */

return <<<'EOD'
<?php

/**
 * This class represents objects dispensed by the {{CLASS_NAME_NORMALISED}} model
 *
 * @package  App\Resource
 * @category resource
 */

namespace {{NAMESPACE}};

use Nails\Common\Resource\Entity;

/**
 * Class {{CLASS_NAME}}
 *
 * @package {{NAMESPACE}}
 */
class {{CLASS_NAME}} extends Entity
{
}

EOD;
