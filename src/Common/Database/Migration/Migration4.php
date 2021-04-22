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

namespace Nails\Common\Database\Migration;

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
        $this->query("UPDATE `{{NAILS_DB_PREFIX}}app_setting` SET `grouping` = REPLACE(`grouping`, 'nailsapp', 'nails');");
    }
}
