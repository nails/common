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

use Nails\Common\Interfaces;
use Nails\Common\Traits;

/**
 * Class Migration{{INDEX}}
 *
 * @package App\Database\Migration
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
