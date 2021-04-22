<?php

/**
 * Migration:   2
 * Started:     06/04/2018
 * Finalised:   06/04/2018
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

class Migration2 extends Base
{
    /**
     * Execute the migration
     * @return Void
     */
    public function execute()
    {
        /**
         * Migrate encrypted app settings to use new Encryption library
         */
        $oResult  = $this->query('SELECT id, value FROM {{NAILS_DB_PREFIX}}app_setting WHERE `is_encrypted` = 1');
        $oEncrypt = Factory::service('Encrypt');
        while ($oRow = $oResult->fetch(\PDO::FETCH_OBJ)) {

            $sNewCipher = $oEncrypt->migrate($oRow->value, \Nails\Config::get('PRIVATE_KEY'));

            //  Update the record
            $sQuery = '
                UPDATE `{{NAILS_DB_PREFIX}}app_setting`
                SET
                    `value` = :newValue
                WHERE
                    `id` = :id
            ';

            $oSth = $this->prepare($sQuery);

            $oSth->bindParam(':newValue', $sNewCipher, \PDO::PARAM_STR);
            $oSth->bindParam(':id', $oRow->id, \PDO::PARAM_INT);

            $oSth->execute();
        }
    }
}
