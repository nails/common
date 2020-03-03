<?php

/**
 * Manage app settings
 *
 * @package     Nails
 * @subpackage  common
 * @category    service
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

use Nails\Common\Exception\EnvironmentException;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Resource;
use Nails\Common\Traits\Caching;
use Nails\Common\Traits\ErrorHandling;
use Nails\Config;
use Nails\Factory;

/**
 * Class AppSetting
 *
 * @package Nails\Common\Service
 */
class AppSetting
{
    use ErrorHandling;
    use Caching;

    // --------------------------------------------------------------------------

    /** @var string */
    protected $sTable;

    /** @var array */
    protected $aSettings = [];

    // --------------------------------------------------------------------------

    /**
     * AppSetting constructor.
     */
    public function __construct()
    {
        $this->sTable = \Nails\Config::get('NAILS_DB_PREFIX') . 'app_setting';
        $this->load();
    }

    // --------------------------------------------------------------------------

    /**
     * Loads app settings from the database
     *
     * @return $this
     * @throws FactoryException
     */
    public function load(): self
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');

        $this->aSettings = array_map(
            function (\stdClass $oSetting) {
                return Factory::resource('AppSetting', null, $oSetting);
            },
            $oDb->get($this->sTable)->result()
        );

        $this->clearCache();

        return $this;
    }

    // --------------------------------------------------------------------------

    protected function generateCacheKey(string $sKey, string $sGrouping): string
    {
        return md5($sKey, $sGrouping);
    }

    // --------------------------------------------------------------------------

    /**
     * Gets settings associated with a particular group/key
     *
     * @param string $sKey          The key to retrieve
     * @param string $sGrouping     The group the key belongs to
     * @param bool   $bForceRefresh Whether to force a group refresh
     *
     * @return Resource\AppSetting[]|Resource\AppSetting|null
     * @throws FactoryException
     */
    public function get(string $sKey = null, string $sGrouping = 'app', bool $bForceRefresh = false)
    {
        if ($bForceRefresh) {
            $this->load();
        }

        $sCacheKey = $this->generateCacheKey($sKey, $sGrouping);
        $oCache    = $this->getCache($sCacheKey);
        if (isset($oCache)) {
            return $oCache;
        }

        $aSettings = array_values(
            array_filter(
                $this->aSettings,
                function (Resource\AppSetting $oSetting) use ($sGrouping) {
                    return $oSetting->grouping === $sGrouping;
                }
            )
        );

        if (empty($sKey)) {
            $this->setCache($sCacheKey, $aSettings);
            return $aSettings;
        } else {
            $aSettings = array_filter(
                $aSettings,
                function (Resource\AppSetting $oSetting) use ($sKey) {
                    return $oSetting->key === $sKey;
                }
            );

            $mOut = reset($aSettings) ?: null;
            $this->setCache($sCacheKey, $mOut);
            return $mOut;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Set a group/key either by passing an array of key=>value pairs as the $key
     * or by passing a string to $key and setting $value
     *
     * @param string|string[] $mKey      The key to set, or an array of key => value pairs
     * @param string          $sGrouping The grouping to store the keys under
     * @param mixed           $mValue    The data to store, only used if $mKey is a string
     * @param bool            $bEncrypt  Whether to encrypt the data or not
     *
     * @return bool
     * @throws FactoryException
     */
    public function set($mKey, string $sGrouping = 'app', $mValue = null, bool $bEncrypt = false): bool
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');
        $oDb->trans_begin();

        try {

            if (is_array($mKey)) {
                foreach ($mKey as $sKey => $mValue) {
                    $this->doSet($sKey, $sGrouping, $mValue, $bEncrypt);
                }
            } else {
                $this->doSet($mKey, $sGrouping, $mValue, $bEncrypt);
            }
            $oDb->trans_commit();
            return true;

        } catch (\Exception $e) {
            $oDb->trans_rollback();
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Inserts/Updates a group/key value
     *
     * @param string $sKey      The key to set
     * @param string $sGrouping The key's grouping
     * @param mixed  $mValue    The value of the setting
     * @param bool   $bEncrypt  Whether to encrypt the data or not
     *
     * @return void
     * @throws FactoryException
     * @throws EnvironmentException
     */
    protected function doSet(string $sKey, string $sGrouping, $mValue, bool $bEncrypt): void
    {
        $sValue = json_encode($mValue);

        if ($bEncrypt) {
            /** @var Encrypt $oEncrypt */
            $oEncrypt = Factory::service('Encrypt');
            $sValue   = $oEncrypt->encode($sValue);
        }

        /** @var Database $oDb */
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

        $this->unsetCache(
            $this->generateCacheKey($sKey, $sGrouping)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Deletes a key(s) for a particular group
     *
     * @param string|string[] $mKey      The key to delete
     * @param string          $sGrouping The key's grouping
     *
     * @return bool
     * @throws FactoryException
     */
    public function delete($mKey, string $sGrouping): bool
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');
        $oDb->trans_begin();

        try {

            if (is_array($mKey)) {
                foreach ($mKey as $sKey) {
                    $this->doDelete($sKey, $sGrouping);
                }
            } else {
                $this->doDelete($mKey, $sGrouping);
            }

            $oDb->trans_commit();
            return true;

        } catch (\Exception $e) {
            $oDb->trans_rollback();
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Actually performs the deletion of the row.
     *
     * @param string $sKey      The key to delete
     * @param string $sGrouping They key's grouping
     *
     * @return bool
     * @throws FactoryException
     */
    protected function doDelete(string $sKey, string $sGrouping): bool
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');
        $oDb->where('key', $sKey);
        $oDb->where('grouping', $sGrouping);

        return $oDb->delete($this->sTable);
    }

    // --------------------------------------------------------------------------

    /**
     * Deletes all keys for a particular group.
     *
     * @param string $sGrouping The group to delete
     *
     * @return bool
     * @throws FactoryException
     */
    public function deleteGroup(string $sGrouping): bool
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');
        $oDb->where('grouping', $sGrouping);

        return $oDb->delete($this->sTable);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the table name used by app settings
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->sTable;
    }
}
