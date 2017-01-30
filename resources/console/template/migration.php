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
 * Finalised:
 */

namespace Nails\Database\Migration\App;

use Nails\Common\Console\Migrate\Base;

class Migration{{INDEX}} extends Base
{
    /**
     * Execute the migration
     * @return void
     */
    public function execute()
    {
        $this->query('');
    }
}

EOD;
