<?php

/**
 * Manage app settings
 *
 * @package     Nails
 * @subpackage  common
 * @category    model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Model;

use Nails\Common\Traits\ErrorHandling;
use Nails\Factory;

class AppSetting
{
    use ErrorHandling;

    // --------------------------------------------------------------------------

    protected $sTable;
    protected $aSettings;

    // --------------------------------------------------------------------------

    /**
     * Construct the setting model, set defaults
     */
    public function __construct()
    {
        $this->sTable    = NAILS_DB_PREFIX . 'app_setting';
        $this->aSettings = [];
    }

    // --------------------------------------------------------------------------

    /**
     * Gets settings associated with a particular group/key
     *
     * @param  string  $sKey          The key to retrieve
     * @param  string  $sGrouping     The group the key belongs to
     * @param  boolean $bForceRefresh Whether to force a group refresh
     *
     * @return array
     */
    public function get($sKey = null, $sGrouping = 'app', $bForceRefresh = false)
    {
        if (!isset($this->aSettings[$sGrouping]) || $bForceRefresh) {

            $oDb = Factory::service('Database');
            $oDb->where('grouping', $sGrouping);
            $aSettings = $oDb->get($this->sTable)->result();

            $this->aSettings[$sGrouping] = [];

            foreach ($aSettings as $oSetting) {

                $sValue = $oSetting->value;

                if (!empty($oSetting->is_encrypted)) {
                    $oEncrypt = Factory::service('Encrypt');
                    $sValue   = $oEncrypt->decode($sValue);
                }

                $this->aSettings[$sGrouping][$oSetting->key] = json_decode($sValue);
            }
        }

        // --------------------------------------------------------------------------

        if (empty($sKey)) {

            return $this->aSettings[$sGrouping];

        } else {

            return isset($this->aSettings[$sGrouping][$sKey]) ? $this->aSettings[$sGrouping][$sKey] : null;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Set a group/key either by passing an array of key=>value pairs as the $key
     * or by passing a string to $key and setting $value
     *
     * @param  mixed   $mKey      The key to set, or an array of key => value pairs
     * @param  string  $sGrouping The grouping to store the keys under
     * @param  mixed   $mValue    The data to store, only used if $mKey is a string
     * @param  boolean $bEncrypt  Whether to encrypt the data or not
     *
     * @return boolean
     */
    public function set($mKey, $sGrouping = 'app', $mValue = null, $bEncrypt = false)
    {
        $oDb = Factory::service('Database');
        $oDb->trans_begin();

        if (is_array($mKey)) {

            foreach ($mKey as $sKey => $mValue) {

                $this->doSet($sKey, $sGrouping, $mValue, $bEncrypt);
            }

        } else {

            $this->doSet($mKey, $sGrouping, $mValue, $bEncrypt);
        }

        if ($oDb->trans_status() === false) {

            $oDb->trans_rollback();

            return false;

        } else {

            $oDb->trans_commit();

            return true;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Inserts/Updates a group/key value
     *
     * @param string  $sKey      The key to set
     * @param string  $sGrouping The key's grouping
     * @param mixed   $mValue    The value of the group/key
     * @param boolean $bEncrypt  Whether to encrypt the data or not
     *
     * @return void
     */
    protected function doSet($sKey, $sGrouping, $mValue, $bEncrypt)
    {
        $sValue   = json_encode($mValue);
        $bEncrypt = (bool) $bEncrypt;

        if ($bEncrypt) {
            $oEncrypt = Factory::service('Encrypt');
            $sValue   = $oEncrypt->encode($sValue);
        }

        $oDb = Factory::service('Database');
        $oDb->where('key', $sKey);
        $oDb->where('grouping', $sGrouping);

        if ($oDb->count_all_results($this->sTable)) {

            $oDb->set('value', $sValue);
            $oDb->set('is_encrypted', $bEncrypt);
            $oDb->where('grouping', $sGrouping);
            $oDb->where('key', $sKey);
            $oDb->update($this->sTable);

        } else {

            $oDb->set('value', $sValue);
            $oDb->set('grouping', $sGrouping);
            $oDb->set('key', $sKey);
            $oDb->set('is_encrypted', $bEncrypt);
            $oDb->insert($this->sTable);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Deletes a key for a particular group
     *
     * @param mixed  $mKey      The key to delete
     * @param string $sGrouping The key's grouping
     *
     * @return bool
     */
    public function delete($mKey, $sGrouping)
    {
        $oDb = Factory::service('Database');
        $oDb->trans_begin();

        if (is_array($mKey)) {

            foreach ($mKey as $sKey) {
                $this->doDelete($sKey, $sGrouping);
            }

        } else {

            $this->doDelete($mKey, $sGrouping);
        }

        if ($oDb->trans_status() === false) {

            $oDb->trans_rollback();

            return false;

        } else {

            $oDb->trans_commit();

            return true;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Actually performs the deletion of the row.
     *
     * @param  string $sKey      The key to delete
     * @param  string $sGrouping They key's grouping
     *
     * @return bool
     */
    protected function doDelete($sKey, $sGrouping)
    {
        $oDb = Factory::service('Database');
        $oDb->where('key', $sKey);
        $oDb->where('grouping', $sGrouping);

        return $oDb->delete($this->sTable);
    }

    // --------------------------------------------------------------------------

    /**
     * Deletes all keys for a particular group.
     *
     * @param  string $sGrouping The group to delete
     *
     * @return bool
     */
    public function deleteGroup($sGrouping)
    {
        $oDb = Factory::service('Database');
        $oDb->where('grouping', $sGrouping);

        return $oDb->delete($this->sTable);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the table name used by app settings
     * @return string
     */
    public function getTableName()
    {
        return $this->sTable;
    }
}
