<?php

/**
 * This file is the template for the contents of models
 * Used by the console command when creating models.
 */

return <<<'EOD'
<?php

/**
 * This model handles interactions with the app's "{{TABLE}}" table.
 *
 * @package  App
 * @category model
 */

namespace {{NAMESPACE}};

use Nails\Common\Model\Base;

class {{CLASS_NAME}} extends Base
{
    /**
     * {{CLASS_NAME}} constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->table = APP_DB_PREFIX . '{{TABLE}}';
    }
}

EOD;
