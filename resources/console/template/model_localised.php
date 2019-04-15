<?php

/**
 * This file is the template for the contents of models
 * Used by the console command when creating models.
 */

use Nails\Common\Traits\Model\Localised;

return <<<'EOD'
<?php

/**
 * This model handles interactions with the app's "{{TABLE_WITH_PREFIX}}" table.
 *
 * @package  App\Model
 * @category model
 */

namespace {{NAMESPACE}};

use Nails\Common\Model\Base;
use Nails\Common\Traits\Model\Localised;

class {{CLASS_NAME}} extends Base
{
    use Localised;

    // --------------------------------------------------------------------------

    /**
     * The table this model represents
     *
     * @var string
     */
    const TABLE = APP_DB_PREFIX . '{{TABLE}}';

    /**
     * The name of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_NAME = '{{SERVICE_NAME}}';

    /**
     * The provider of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_PROVIDER = 'app';
}

EOD;
