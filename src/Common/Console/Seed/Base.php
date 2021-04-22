<?php

namespace Nails\Common\Console\Seed;

use Nails\Common\Interfaces;
use Nails\Common\Traits;

/**
 * Class Base
 *
 * @package    Nails\Common\Console\Migrate
 * @deprecated Implement interface and trait directly
 */
abstract class Base implements Interfaces\Database\Seeder
{
    use Traits\Database\Seeder;
}
