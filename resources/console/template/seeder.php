<?php

/**
 * This file is the template for the contents of database seeders
 * Used by the console command when creating database seeders.
 */

return <<<'EOD'
<?php

namespace App\Database\Seed;

use Nails\Common\Console\Seed\Model;

/**
 * Class {{MODEL_NAME}}
 *
 * @package App\Database\Seed
 */
class {{MODEL_NAME}} extends Model
{
    const CONFIG_MODEL_NAME = '{{MODEL_NAME}}';
}

EOD;
