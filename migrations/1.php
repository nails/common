<?php

/**
 * Migration:   2
 * Started:     25/01/2015
 * Finalised:   25/01/2015
 *
 * @package     Nails
 * @subpackage  common
 * @category    Database Migration
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Database\Migration\nails\Common;

use Nails\Common\Console\Migrate\Base;

class Migration1 extends Base
{
    /**
     * Execute the migration
     * @return Void
     */
    public function execute()
    {
        /**
         * Convert app settings into JSON strings rather than use serialize
         */

        $oResult = $this->query('SELECT id, value FROM {{NAILS_DB_PREFIX}}app_setting');
        while ($oRow = $oResult->fetch(\PDO::FETCH_OBJ)) {

            $mOldValue = unserialize($oRow->value);
            $sNewValue = json_encode($mOldValue);

            //  Update the record
            $sQuery = '
                UPDATE `{{NAILS_DB_PREFIX}}app_setting`
                SET
                    `value` = :newValue
                WHERE
                    `id` = :id
            ';

            $oSth = $this->prepare($sQuery);

            $oSth->bindParam(':newValue', $sNewValue, \PDO::PARAM_STR);
            $oSth->bindParam(':id', $oRow->id, \PDO::PARAM_INT);

            $oSth->execute();
        }
    }
}
