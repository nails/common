<?php

/**
 * This file is the template for the contents of database seeders
 * Used by the console command when creating database seeders.
 */

return <<<'EOD'
<?php

namespace App\Seed;

use Nails\Common\Console\Seed\DefaultSeed;

/**
 * Class {{MODEL_NAME}}
 *
 * @package App\Seed
 */
class {{MODEL_NAME}} extends DefaultSeed
{
    const CONFIG_MODEL_NAME     = '{{MODEL_NAME}}';
    const CONFIG_MODEL_PROVIDER = 'app';
}

EOD;
