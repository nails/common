<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Migration
{
	protected $_migration_enabled = FALSE;
	protected $_migration_path = NULL;
	protected $_migration_version = 0;
	protected $_migration_column = NULL;

	protected $_error_string = '';

	public function __construct( $config = array() )
	{
		# Only run this constructor on main library load
		if (get_parent_class($this) !== FALSE && get_called_class() !== 'NAILS_Migration')
		{
			return;
		}

		foreach ($config as $key => $val)
		{
			$this->{'_' . $key} = $val;
		}

		log_message('debug', 'Migrations class initialized');

		// Are they trying to use migrations while it is disabled?
		if ($this->_migration_enabled !== TRUE)
		{
			show_error('Migrations has been loaded but is disabled or set up incorrectly.');
		}

		// If not set, set it
		$this->_migration_path == '' AND $this->_migration_path = APPPATH . 'migrations/';

		// Add trailing slash if not set
		$this->_migration_path = rtrim($this->_migration_path, '/').'/';

		// Load migration language
		$this->lang->load('migration');

		// They'll probably be using dbforge
		$this->load->dbforge();

		// If the migrations table is missing, make it
		if ( ! $this->db->table_exists(NAILS_DB_PREFIX . 'migrations'))
		{
			$this->dbforge->add_field(array(
				'nails_version'	=> array('type' => 'INT', 'constraint' => 3),
				'app_version'	=> array('type' => 'INT', 'constraint' => 3)
			));

			$this->dbforge->create_table(NAILS_DB_PREFIX . 'migrations', TRUE);

			$this->db->insert(NAILS_DB_PREFIX . 'migrations', array('nails_version' => 0,'app_version' => 0));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Migrate to a schema version
	 *
	 * Calls each migration step required to get to the schema version of
	 * choice
	 *
	 * @param	int	Target schema version
	 * @return	mixed	TRUE if already latest, FALSE if failed, int if upgraded
	 */
	public function version($target_version)
	{
		if ( empty( $this->_migration_column ) ) :

			$this->_error_string = 'You must specify whether you wish to migrate the Nails database or the app database.';
			return false;

		endif;

		// --------------------------------------------------------------------------

		$start = $current_version = $this->_get_version();
		$stop = $target_version;

		if ($target_version > $current_version)
		{
			// Moving Up
			++$start;
			++$stop;
			$step = 1;
		}
		else
		{
			// Moving Down
			$step = -1;
		}

		$method = ($step === 1) ? 'up' : 'down';
		$migrations = array();

		// We now prepare to actually DO the migrations
		// But first let's make sure that everything is the way it should be
		for ($i = $start; $i != $stop; $i += $step)
		{
			$f = glob(sprintf($this->_migration_path . '%03d_*.php', $i));

			// Only one migration per step is permitted
			if (count($f) > 1)
			{
				$this->_error_string = sprintf($this->lang->line('migration_multiple_version'), $i);
				return FALSE;
			}

			// Migration step not found
			if (count($f) == 0)
			{
				// If trying to migrate up to a version greater than the last
				// existing one, migrate to the last one.
				if ($step == 1)
				{
					break;
				}

				// If trying to migrate down but we're missing a step,
				// something must definitely be wrong.
				$this->_error_string = sprintf($this->lang->line('migration_not_found'), $i);
				return FALSE;
			}

			$file = basename($f[0]);
			$name = basename($f[0], '.php');

			// Filename validations
			if (preg_match('/^\d{3}_(\w+)$/', $name, $match))
			{
				$match[1] = strtolower($match[1]);

				// Cannot repeat a migration at different steps
				if (in_array($match[1], $migrations))
				{
					$this->_error_string = sprintf($this->lang->line('migration_multiple_version'), $match[1]);
					return FALSE;
				}

				include $f[0];
				$class = 'Migration_' . ucfirst($match[1]);

				if ( ! class_exists($class))
				{
					$this->_error_string = sprintf($this->lang->line('migration_class_doesnt_exist'), $class);
					return FALSE;
				}

				if ( ! is_callable(array($class, $method)))
				{
					$this->_error_string = sprintf($this->lang->line('migration_missing_'.$method.'_method'), $class);
					return FALSE;
				}

				$migrations[] = $match[1];
			}
			else
			{
				$this->_error_string = sprintf($this->lang->line('migration_invalid_filename'), $file);
				return FALSE;
			}
		}

		log_message('debug', 'Current migration: ' . $current_version);

		$version = $i + ($step == 1 ? -1 : 0);

		// If there is nothing to do so quit
		if ($migrations === array())
		{
			return TRUE;
		}

		log_message('debug', 'Migrating from ' . $method . ' to version ' . $version);

		// Loop through the migrations
		foreach ($migrations AS $migration)
		{
			// Run the migration class
			$class = 'Migration_' . ucfirst(strtolower($migration));
			call_user_func(array(new $class, $method));

			$current_version += $step;
			$this->_update_version($current_version);
		}

		log_message('debug', 'Finished migrating to '.$current_version);

		return $current_version;
	}

	// --------------------------------------------------------------------

	/**
	 * Set's the schema to the latest migration
	 *
	 * @return	mixed	true if already latest, false if failed, int if upgraded
	 */
	public function latest()
	{
		if ( empty( $this->_migration_column ) ) :

			$this->_error_string = 'You must specify whether you wish to migrate the Nails database or the app database.';
			return false;

		endif;

		// --------------------------------------------------------------------------

		if ( ! $migrations = $this->find_migrations())
		{
			$this->_error_string = $this->lang->line('migration_none_found');
			return false;
		}

		$last_migration = basename(end($migrations));

		// Calculate the last migration step from existing migration
		// filenames and procceed to the standard version migration
		return $this->version((int) substr($last_migration, 0, 3));
	}

	// --------------------------------------------------------------------

	/**
	 * Set's the schema to the migration version set in config
	 *
	 * @return	mixed	true if already current, false if failed, int if upgraded
	 */
	public function current()
	{
		if ( empty( $this->_migration_column ) ) :

			$this->_error_string = 'You must specify whether you wish to migrate the Nails database or the app database.';
			return false;

		endif;

		// --------------------------------------------------------------------------

		return $this->version($this->_migration_version);
	}

	// --------------------------------------------------------------------

	/**
	 * Error string
	 *
	 * @return	string	Error message returned as a string
	 */
	public function error_string()
	{
		return $this->_error_string;
	}

	// --------------------------------------------------------------------

	/**
	 * Set's the schema to the latest migration
	 *
	 * @return	mixed	true if already latest, false if failed, int if upgraded
	 */
	protected function find_migrations()
	{
		// Load all *_*.php files in the migrations path
		$files = glob($this->_migration_path . '*_*.php');
		$file_count = count($files);

		for ($i = 0; $i < $file_count; $i++)
		{
			// Mark wrongly formatted files as false for later filtering
			$name = basename($files[$i], '.php');
			if ( ! preg_match('/^\d{3}_(\w+)$/', $name))
			{
				$files[$i] = FALSE;
			}
		}

		sort($files);
		return $files;
	}

	// --------------------------------------------------------------------

	/**
	 * Retrieves current schema version
	 *
	 * @return	int	Current Migration
	 */
	protected function _get_version()
	{
		$row = $this->db->get(NAILS_DB_PREFIX . 'migrations')->row();
		return $row ? $row->{$this->_migration_column} : 0;
	}

	// --------------------------------------------------------------------

	/**
	 * Stores the current schema version
	 *
	 * @param	int	Migration reached
	 * @return	bool
	 */
	protected function _update_version($migrations)
	{
		return $this->db->update(NAILS_DB_PREFIX . 'migrations', array(
			$this->_migration_column => $migrations
		));
	}

	// --------------------------------------------------------------------

	/**
	 * Enable the use of CI super-global
	 *
	 * @param	mixed	$var
	 * @return	mixed
	 */
	public function __get($var)
	{
		return get_instance()->$var;
	}


	// --------------------------------------------------------------------------


	public function set_nails()
	{
		$this->_migration_path		= NAILS_PATH . 'migrations/';
		$this->_migration_path		= str_replace( ' ', '\ ', $this->_migration_path );
		$this->_migration_path		= str_replace( '.', '\.', $this->_migration_path );
		$this->_migration_path		= str_replace( '[', '\[', $this->_migration_path );
		$this->_migration_path		= str_replace( ']', '\]', $this->_migration_path );

		$this->_migration_column	= 'nails_version';
	}


	// --------------------------------------------------------------------------


	public function set_app()
	{
		$this->_migration_path		= FCPATH . APPPATH . 'migrations/';
		$this->_migration_path		= str_replace( ' ', '\ ', $this->_migration_path );
		$this->_migration_path		= str_replace( '.', '\.', $this->_migration_path );
		$this->_migration_path		= str_replace( '[', '\[', $this->_migration_path );
		$this->_migration_path		= str_replace( ']', '\]', $this->_migration_path );

		$this->_migration_column	= 'app_version';
	}
}

/* End of file CORE_NAILS_Migration.php */
/* Location: ./libraries/CORE_NAILS_Migration.php */