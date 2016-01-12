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

class AppSetting extends Base
{
    protected $settings;

    // --------------------------------------------------------------------------

    /**
     * Construct the setting model, set defaults
     */
    public function __construct()
    {
        parent::__construct();

        // --------------------------------------------------------------------------

        $this->table        = NAILS_DB_PREFIX . 'app_setting';
        $this->tablePrefix = 'n';
        $this->settings      = array();
    }

    // --------------------------------------------------------------------------

    /**
     * Get's emails associated with a particular group/key
     * @param  string  $key          The key to retrieve
     * @param  string  $grouping     The group the key belongs to
     * @param  boolean $forceRefresh Whether to force a group refresh
     * @return array
     */
    public function get($key = null, $grouping = 'app', $forceRefresh = false)
    {
        if (!isset($this->settings[$grouping]) || $forceRefresh) {

            $this->db->where('grouping', $grouping);
            $settings = $this->db->get($this->table)->result();
            $this->settings[$grouping] = array();

            foreach ($settings as $setting) {

                $this->settings[$grouping][$setting->key] = json_decode($setting->value);

                if (!empty($setting->is_encrypted)) {

                    $decoded = $this->encrypt->decode($this->settings[$grouping][$setting->key], APP_PRIVATE_KEY);
                    $this->settings[$grouping][$setting->key] = $decoded;
                }
            }
        }

        // --------------------------------------------------------------------------

        if (empty($key)) {

            return $this->settings[$grouping];

        } else {

            return isset($this->settings[$grouping][$key]) ? $this->settings[$grouping][$key] : null;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Set a group/key either by passing an array of key=>value pairs as the $key
     * or by passing a string to $key and setting $value
     * @param mixed   $key      The key to set, or an array of key => value pairs
     * @param string  $grouping The grouping to store the keys under
     * @param mixed   $value    The data to store, only used if $key is a string
     * @param boolean $encrypt  Whether to encrypt the data or not
     * @return boolean
     */
    public function set($key, $grouping = 'app', $value = null, $encrypt = false)
    {
        $this->db->trans_begin();

        if (is_array($key)) {

            foreach ($key as $key => $value) {

                $this->doSet($key, $grouping, $value, $encrypt);
            }

        } else {

            $this->doSet($key, $grouping, $value, $encrypt);
        }

        if ($this->db->trans_status() === false) {

            $this->db->trans_rollback();
            return false;

        } else {

            $this->db->trans_commit();
            return true;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Inserts/Updates a group/key value
     * @param string  $key      The key to set
     * @param string  $grouping The key's grouping
     * @param mixed   $value    The value of the group/key
     * @param boolean $encrypt  Whether to encrypt the data or not
     * @return void
     */
    protected function doSet($key, $grouping, $value, $encrypt)
    {
        if ($encrypt) {

            $value       = $this->encrypt->encode($value, APP_PRIVATE_KEY);
            $isEncrypted = true;

        } else {

            $isEncrypted = false;
        }

        $this->db->where('key', $key);
        $this->db->where('grouping', $grouping);

        if ($this->db->count_all_results($this->table)) {

            $this->db->set('value', json_encode($value));
            $this->db->set('is_encrypted', $isEncrypted);
            $this->db->where('grouping', $grouping);
            $this->db->where('key', $key);
            $this->db->update($this->table);

        } else {

            $this->db->set('value', json_encode($value));
            $this->db->set('grouping', $grouping);
            $this->db->set('key', $key);
            $this->db->set('is_encrypted', $isEncrypted);
            $this->db->insert($this->table);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Deletes a key for a particular group
     * @param string  $key      The key to delete
     * @param string  $grouping The key's grouping
     * @return bool
     */
    public function delete($key, $grouping)
    {
        $this->db->trans_begin();

        if (is_array($key)) {

            foreach ($key as $key) {

                $this->doDelete($key, $grouping);
            }

        } else {

            $this->doDelete($key, $grouping);
        }

        if ($this->db->trans_status() === false) {

            $this->db->trans_rollback();
            return false;

        } else {

            $this->db->trans_commit();
            return true;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Actually performs the deletion of the row.
     * @param  string $key      The key to delete
     * @param  string $grouping They key's grouping
     * @return bool
     */
    protected function doDelete($key, $grouping)
    {
        $this->db->where('key', $key);
        $this->db->where('grouping', $grouping);
        $this->db->delete($this->table);

        return (bool) $this->db->affected_rows();
    }

    // --------------------------------------------------------------------------

    /**
     * Deletes all keys for a particular group.
     * @param  string $grouping The group to delete
     * @return bool
     */
    public function deleteGroup($grouping)
    {
        $this->db->where('grouping', $grouping);
        $this->db->delete($this->table);

        return (bool) $this->db->affected_rows();
    }
}
