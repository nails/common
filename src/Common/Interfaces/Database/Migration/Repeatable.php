<?php

namespace Nails\Common\Interfaces\Database\Migration;

use Nails\Common\Interfaces\Database\Migration;

/**
 * Interface Repeatable
 *
 * A repeatable migration is evaluated on every run of db:migrate, regardless of the
 * module's recorded version. Implementations must therefore detect for themselves
 * whether there is any work to do, and must be safe to execute any number of times.
 *
 * This exists so that a migration's reachability does not depend on its number. Where
 * a module is maintained on more than one branch the same change may carry a different
 * number on each, so an app moving between branches can otherwise skip a migration
 * entirely.
 *
 * @package Nails\Common\Interfaces\Database\Migration
 */
interface Repeatable extends Migration
{
}
