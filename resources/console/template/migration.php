<?php

/**
 * This file is the template for the contents of database migrations
 * Used by the console command when creating database migrations.
 */

return <<<'EOD'
<?php

/**
 * Migration: {{INDEX}}
 * Started:   {{DATE_START}}
 */

namespace App\Database\Migration;

use Nails\Common\Console\Migrate\Base;
use Nails\Common\Interfaces;

/**
 * Class Migration{{INDEX}}
 *
 * @package Nails\Database\Migration\App
 */
class Migration{{INDEX}} implements Interfaces\Database\Migration
{
    use Traits\Database\Migration;

    // --------------------------------------------------------------------------

    /**
     * Execute the migration
     */
    public function execute(): void
    {
        {{QUERIES}}
    }
}

EOD;
