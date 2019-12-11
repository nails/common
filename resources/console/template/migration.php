<?php

/**
 * This file is the template for the contents of database migrations
 * Used by the console command when creating database migrations.
 */

return <<<'EOD'
<?php

/**
 * Migration:   {{INDEX}}
 * Started:     {{DATE_START}}
 */

namespace Nails\Database\Migration\App;

use Nails\Common\Console\Migrate\Base;

/**
 * Class Migration{{INDEX}}
 *
 * @package Nails\Database\Migration\App
 */
class Migration{{INDEX}} extends Base
{
    /**
     * Execute the migration
     */
    public function execute(): void
    {
        {{QUERIES}}
    }
}

EOD;
