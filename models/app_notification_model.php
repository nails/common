<?php

class NAILS_App_notification_model extends NAILS_Model
{
	protected $_notifications;
	protected $_emails;

	// --------------------------------------------------------------------------

	/**
	 * Construct the notification model, set defaults
	 */
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_table			= NAILS_DB_PREFIX . 'app_notification';
		$this->_table_prefix	= 'n';
		$this->_notifications	= array();
		$this->_emails			= array();

		// --------------------------------------------------------------------------

		$this->_set_definitions();
	}

	// --------------------------------------------------------------------------

	/**
	 * Defines the notifications
	 */
	protected function _set_definitions()
	{
		//	Look for notification definitions defined by enabled modules
		$_modules = _NAILS_GET_AVAILABLE_MODULES();

		foreach ($_modules AS $module) :

			$_module	= explode('-', $module);
			$_path		= FCPATH . 'vendor/' . $module . '/' . $_module[1] . '/config/app_notifications.php';

			if (file_exists($_path)) :

				include $_path;

				if (!empty($config['notification_definitions'])) :

					$this->_notifications = array_merge($this->_notifications, $config['notification_definitions']);

				endif;

			endif;

		endforeach;

		//	Finally, look for app notification definitions
		$_path = FCPATH . APPPATH . 'config/app_notifications.php';

		if (file_exists($_path)) :

			include $_path;

			if (!empty($config['notification_definitions'])) :

				$this->_notifications = array_merge($this->_notifications, $config['notification_definitions']);

			endif;

		endif;

		//	Put into a vague order
		ksort($this->_notifications);
	}

	// --------------------------------------------------------------------------

	/**
	 * Returns the notification defnintions, optionally limited per group
	 * @param  string $grouping The group to limit to
	 * @return array
	 */
	public function get_definitions($grouping = NULL)
	{
		if (is_null($grouping)) :

			return $this->_notifications;

		elseif (isset($this->_notifications[$grouping])) :

			return $this->_notifications[$grouping];

		else :

			return array();

		endif;
	}

	// --------------------------------------------------------------------------

	/**
	 * Get's emails associated with a particular group/key
	 * @param  string  $key           The key to retrieve
	 * @param  string  $grouping      The group the key belongs to
	 * @param  boolean $force_refresh Whether to force a group refresh
	 * @return array
	 */
	public function get($key = NULL, $grouping = 'app', $force_refresh = false)
	{
		//	Check that it's a valid key/grouping pair
		if (!isset($this->_notifications[$grouping]->options[$key])) :

			$this->_set_error($grouping . '/' . $key . ' is not a valid group/key pair.');
			return false;

		endif;

		// --------------------------------------------------------------------------

		if (empty($this->_emails[$grouping]) || $force_refresh) :

			$this->db->where('grouping', $grouping);
			$_notifications = $this->db->get($this->_table)->result();
			$this->_emails[$grouping] = array();

			foreach ($_notifications AS $setting) :

				$this->_emails[$grouping][ $setting->key ] = json_decode($setting->value);

			endforeach;

		endif;

		// --------------------------------------------------------------------------

		if (empty($key)) :

			return $this->_emails[$grouping];

		else :

			return isset($this->_emails[$grouping][$key]) ? $this->_emails[$grouping][$key] : array();

		endif;
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
	public function set($key, $grouping = 'app', $value = NULL)
	{
		$this->db->trans_begin();

		if (is_array($key)) :

			foreach ($key AS $key => $value) :

				$this->_set($key, $grouping, $value);

			endforeach;

		else :

			$this->_set($key, $grouping, $value);

		endif;

		if ($this->db->trans_status() === false) :

			$this->db->trans_rollback();
			return false;

		else :

			$this->db->trans_commit();
			return true;

		endif;
	}

	// --------------------------------------------------------------------------

	/**
	 * Inserts/Updates a group/key value
	 * @param string $key      The key to set
	 * @param string $grouping The key's grouping
	 * @param mixed  $value    The value of the group/key
	 * @return void
	 */
	protected function _set($key, $grouping, $value)
	{
		//	Check that it's a valid key/grouping pair
		if (!isset($this->_notifications[$grouping]->options[$key])) :

			$this->_set_error($grouping . '/' . $key . ' is not a valid group/key pair.');
			return false;

		endif;

		// --------------------------------------------------------------------------

		$this->db->where('key', $key);
		$this->db->where('grouping', $grouping);
		if ($this->db->count_all_results($this->_table)) :

			$this->db->where('grouping', $grouping);
			$this->db->where('key', $key);
			$this->db->set('value', json_encode($value));
			$this->db->update($this->_table);

		else :

			$this->db->set('grouping', $grouping);
			$this->db->set('key', $key);
			$this->db->set('value', json_encode($value));
			$this->db->insert($this->_table);

		endif;
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
		//	Check that it's a valid key/grouping pair
		if (!isset($this->_notifications[$grouping]->options[$key])) :

			$this->_set_error($grouping . '/' . $key . ' is not a valid group/key pair.');
			return false;

		endif;

		// --------------------------------------------------------------------------

		//	Fetch emails
		$_emails = $this->get($key, $grouping);

		if (empty($_emails)) :

			//	Notification disabled, silently fail
			return true;

		endif;

		//	Definition to use; clone so overrides aren't permenant
		$_definition = clone $this->_notifications[$grouping]->options[$key];

		//	Overriding the definition?
		if (!empty($override) && is_array($override)) :

			foreach ($override AS $or_key => $or_value) :

				if (isset($_definition->{$or_key})) :

					$_definition->{$or_key} = $or_value;

				endif;

			endforeach;

		endif;

		if (empty($_definition->email_tpl)) :

			$this->_set_error('No email template defined for ' . $grouping . '/' . $key);
			return false;

		endif;

		// --------------------------------------------------------------------------

		//	Send the email
		$this->load->library('emailer');

		//	Build the email
		$_email			= new stdClass();
		$_email->type	= 'app_notification';
		$_email->data	= $data;

		if (!empty($_definition->email_subject)) :

			$_email->data['email_subject'] = $_definition->email_subject;

		endif;

		if (!empty($_definition->email_tpl)) :

			$_email->data['email_template'] = $_definition->email_tpl;

		endif;

		foreach ($_emails AS $e) :

			log_message('debug', 'Sending notification (' . $grouping . '/' . $key . ') to ' . $e);

			$_email->to_email = $e;

			if (!$this->emailer->send($_email, true)) :

				$this->_set_error($this->emailer->last_error());
				return false;

			endif;

		endforeach;

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
