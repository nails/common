<?php

/**
 * Migration:   4
 * Started:     14/09/2018
 * Finalised:   14/09/2018
 *
 * @package     Nails
 * @subpackage  common
 * @category    Database Migration
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Database\Migration\Nails\Common;

use Nails\Common\Console\Migrate\Base;
use Nails\Factory;

class Migration4 extends Base
{
    /**
     * Execute the migration
     * @return Void
     */
    public function execute()
    {
        $this->query("DROP TABLE `{{NAILS_DB_PREFIX}}app_notification`;");
    }
}
