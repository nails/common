<?php

class NAILS_App_notification_model extends NAILS_Model
{
    protected $notifications;
    protected $emails;

    // --------------------------------------------------------------------------

    /**
     * Construct the notification model, set defaults
     */
    public function __construct()
    {
        parent::__construct();

        // --------------------------------------------------------------------------

        $this->_table        = NAILS_DB_PREFIX . 'app_notification';
        $this->_table_prefix = 'n';
        $this->notifications = array();
        $this->emails        = array();

        // --------------------------------------------------------------------------

        $this->setDefinitions();
    }

    // --------------------------------------------------------------------------

    /**
     * Defines the notifications
     */
    protected function setDefinitions()
    {
        //  Define where we should look
        $definitionLocations   = array();
        $definitionLocations[] = NAILS_COMMON_PATH . 'config/app_notifications.php';

        $modules = _NAILS_GET_AVAILABLE_MODULES();
        foreach ($modules AS $module) {

            $moduleBits            = explode('-', $module);
            $definitionLocations[] = FCPATH . 'vendor/' . $module . '/' . $moduleBits[1] . '/config/app_notifications.php';
        }

        $definitionLocations[] = FCPATH . APPPATH . 'config/app_notifications.php';

        //  Find definitions
        foreach ($definitionLocations as $path) {

            $this->loadDefinitions($path);
        }

        //  Put into a vague order
        ksort($this->notifications);
    }

    // --------------------------------------------------------------------------

    /**
     * Loads definitions located at $path
     * @param  string $path The path to load
     * @return void
     */
    protected function loadDefinitions($path)
    {
        if (file_exists($path)) {

            include $path;

            if (!empty($config['notification_definitions'])) {

                $this->notifications = array_merge($this->notifications, $config['notification_definitions']);
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the notification defnintions, optionally limited per group
     * @param  string $grouping The group to limit to
     * @return array
     */
    public function getDefinitions($grouping = null)
    {
        if (is_null($grouping)) {

            return $this->notifications;

        } elseif (isset($this->notifications[$grouping])) {

            return $this->notifications[$grouping];

        } else {

            return array();
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Gets emails associated with a particular group/key
     * @param  string  $key           The key to retrieve
     * @param  string  $grouping      The group the key belongs to
     * @param  boolean $force_refresh Whether to force a group refresh
     * @return array
     */
    public function get($key = null, $grouping = 'app', $force_refresh = false)
    {
        //  Check that it's a valid key/grouping pair
        if (!isset($this->notifications[$grouping]->options[$key])) {

            $this->_set_error($grouping . '/' . $key . ' is not a valid group/key pair.');
            return false;
        }

        // --------------------------------------------------------------------------

        if (empty($this->emails[$grouping]) || $force_refresh) {

            $this->db->where('grouping', $grouping);
            $notifications = $this->db->get($this->_table)->result();
            $this->emails[$grouping] = array();

            foreach ($notifications as $setting) {

                $this->emails[$grouping][ $setting->key ] = json_decode($setting->value);
            }
        }

        // --------------------------------------------------------------------------

        if (empty($key)) {

            return $this->emails[$grouping];

        } else {

            return isset($this->emails[$grouping][$key]) ? $this->emails[$grouping][$key] : array();
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Set a group/key either by passing an array of key=>value pairs as the $key
     * or by passing a string to $key and setting $value
     * @param mixed  $key      The key to set, or an array of key => value pairs
     * @param string $grouping The grouping to store the keys under
     * @param mixed  $value    The data to store, only used if $key is a string
     * @return boolean
     */
    public function set($key, $grouping = 'app', $value = null)
    {
        $this->db->trans_begin();

        if (is_array($key)) {

            foreach ($key as $key => $value) {

                $this->doSet($key, $grouping, $value);
            }

        } else {

            $this->doSet($key, $grouping, $value);
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
     * @param string $key      The key to set
     * @param string $grouping The key's grouping
     * @param mixed  $value    The value of the group/key
     * @return void
     */
    protected function doSet($key, $grouping, $value)
    {
        //  Check that it's a valid key/grouping pair
        if (!isset($this->notifications[$grouping]->options[$key])) {

            $this->_set_error($grouping . '/' . $key . ' is not a valid group/key pair.');
            return false;
        }

        // --------------------------------------------------------------------------

        $this->db->where('key', $key);
        $this->db->where('grouping', $grouping);
        if ($this->db->count_all_results($this->_table)) {

            $this->db->where('grouping', $grouping);
            $this->db->where('key', $key);
            $this->db->set('value', json_encode($value));
            $this->db->update($this->_table);

        } else {

            $this->db->set('grouping', $grouping);
            $this->db->set('key', $key);
            $this->db->set('value', json_encode($value));
            $this->db->insert($this->_table);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Sends a notification to the email addresses associated with a particular key/grouping
     * @param  string $key      The key to send to
     * @param  string $grouping The key's grouping
     * @param  array  $data     An array of values to pass to the email template
     * @param  array  $override Override any of the definition values (this time only). Useful for defining custom email templates etc.
     * @return boolean
     */
    public function notify($key, $grouping = 'app', $data = array(), $override = array())
    {
        //  Check that it's a valid key/grouping pair
        if (!isset($this->notifications[$grouping]->options[$key])) {

            $this->_set_error($grouping . '/' . $key . ' is not a valid group/key pair.');
            return false;
        }

        // --------------------------------------------------------------------------

        //  Fetch emails
        $emails = $this->get($key, $grouping);

        if (empty($emails)) {

            //  Notification disabled, silently fail
            return true;
        }

        //  Definition to use; clone so overrides aren't permenant
        $definition = clone $this->notifications[$grouping]->options[$key];

        //  Overriding the definition?
        if (!empty($override) && is_array($override)) {

            foreach ($override as $or_key => $or_value) {

                if (isset($definition->{$or_key})) {

                    $definition->{$or_key} = $or_value;
                }
            }
        }

        if (empty($definition->email_tpl)) {

            $this->_set_error('No email template defined for ' . $grouping . '/' . $key);
            return false;
        }

        // --------------------------------------------------------------------------

        //  Send the email
        $this->load->library('emailer');

        //  Build the email
        $email       = new stdClass();
        $email->type = 'app_notification';
        $email->data = $data;

        if (!empty($definition->email_subject)) {

            $email->data['email_subject'] = $definition->email_subject;
        }

        if (!empty($definition->email_tpl)) {

            $email->data['email_template'] = $definition->email_tpl;
        }

        foreach ($emails as $e) {

            log_message('debug', 'Sending notification (' . $grouping . '/' . $key . ') to ' . $e);

            $email->to_email = $e;

            if (!$this->emailer->send($email, true)) {

                $this->_set_error($this->emailer->last_error());
                return false;
            }
        }

        return true;
    }
}

// --------------------------------------------------------------------------

/**
 * OVERLOADING NAILS' MODELS
 *
 * The following block of code makes it simple to extend one of the core
 * models. Some might argue it's a little hacky but it's a simple 'fix'
 * which negates the need to massively extend the CodeIgniter Loader class
 * even further (in all honesty I just can't face understanding the whole
 * Loader class well enough to change it 'properly').
 *
 * Here's how it works:
 *
 * CodeIgniter instantiate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclare class X' errors
 * and if we call our overloading class something else it will never get instantiated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instantiated et voila.
 *
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if (!defined('NAILS_ALLOW_EXTENSION_APP_NOTIFICATION_MODEL')) {

    class App_notification_model extends NAILS_App_notification_model
    {
    }
}
