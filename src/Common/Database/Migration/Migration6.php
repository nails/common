<?php

/**
 * Migration:  6
 * Started:    10/05/2021
 *
 * @package    Nails
 * @subpackage common
 * @category   Database Migration
 * @author     Nails Dev Team
 */

namespace Nails\Common\Database\Migration;

use Nails\Common\Console\Migrate\Base;
use Nails\Common\Constants;
use Nails\Factory;

class Migration6 extends Base
{
    /**
     * Execute the migration
     *
     * @return void
     */
    public function execute()
    {
        $this->query("UPDATE `nails_app_setting` SET `grouping` = '" . Constants::MODULE_SLUG . "' WHERE `grouping` = 'site';");
    }
}
