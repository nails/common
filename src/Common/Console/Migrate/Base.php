<?php

namespace Nails\Common\Console\Migrate;

use Nails\Common\Interfaces;
use Nails\Common\Traits;

/**
 * Class Base
 *
 * @package    Nails\Common\Console\Migrate
 * @deprecated Implement interface and trait directly
 */
abstract class Base implements Interfaces\Database\Migration
{
    use Traits\Database\Migration;
}
