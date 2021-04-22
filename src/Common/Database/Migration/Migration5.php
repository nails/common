<?php

/**
 * Migration:   5
 * Started:     19/07/2019
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

class Migration5 extends Base
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
