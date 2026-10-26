<?php

/**
 * Migration:   7
 * Started:     26/10/2026
 *
 * @package     Nails
 * @subpackage  common
 * @category    Database Migration
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Database\Migration;

use Nails\Common\Console\Migrate\Base;

class Migration7 extends Base
{
    /**
     * Execute the migration
     *
     * @return void
     */
    public function execute()
    {
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}app_setting` CHANGE `key` `key` VARCHAR(150) NULL DEFAULT NULL;");
    }
}
