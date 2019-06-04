<?php

/**
 * The class abstracts the database. The whole of this class is a little hacky - an attempt to disconnect
 * the CI DB library from CI so it can be used in non-CI environments (e.g. the console). As such, we need to
 * simulate some classes and wave some magic wands.
 *
 * @todo        - Remove dependency on CodeIgniter's Database abstraction
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

use Nails\Common\Exception\Database\ConnectionException;
use Nails\Environment;
use Nails\Factory;

/**
 * Class Database
 *
 * @method select($select = '*', $escape = null)
 * @method select_max($select = '', $alias = '')
 * @method select_min($select = '', $alias = '')
 * @method select_avg($select = '', $alias = '')
 * @method select_sum($select = '', $alias = '')
 * @method distinct($val = true)
 * @method from($from)
 * @method join($table, $cond, $type = '', $escape = null)
 * @method where($key, $value = null, $escape = null)
 * @method or_where($key, $value = null, $escape = null)
 * @method where_in($key = null, $values = null, $escape = null)
 * @method or_where_in($key = null, $values = null, $escape = null)
 * @method where_not_in($key = null, $values = null, $escape = null)
 * @method or_where_not_in($key = null, $values = null, $escape = null)
 * @method like($field, $match = '', $side = 'both', $escape = null)
 * @method not_like($field, $match = '', $side = 'both', $escape = null)
 * @method or_like($field, $match = '', $side = 'both', $escape = null)
 * @method or_not_like($field, $match = '', $side = 'both', $escape = null)
 * @method group_start($not = '', $type = 'AND ')
 * @method or_group_start()
 * @method not_group_start()
 * @method or_not_group_start()
 * @method group_end()
 * @method group_by($by, $escape = null)
 * @method having($key, $value = null, $escape = null)
 * @method or_having($key, $value = null, $escape = null)
 * @method order_by($orderby, $direction = '', $escape = null)
 * @method limit($value, $offset = 0)
 * @method offset($offset)
 * @method set($key, $value = '', $escape = null)
 * @method get_compiled_select($table = '', $reset = true)
 * @method get($table = '', $limit = null, $offset = null)
 * @method count_all_results($table = '', $reset = true)
 * @method get_where($table = '', $where = null, $limit = null, $offset = null)
 * @method insert_batch($table, $set = null, $escape = null, $batch_size = 100)
 * @method set_insert_batch($key, $value = '', $escape = null)
 * @method get_compiled_insert($table = '', $reset = true)
 * @method insert($table = '', $set = null, $escape = null)
 * @method replace($table = '', $set = null)
 * @method get_compiled_update($table = '', $reset = true)
 * @method update($table = '', $set = null, $where = null, $limit = null)
 * @method update_batch($table, $set = null, $index = null, $batch_size = 100)
 * @method set_update_batch($key, $index = '', $escape = null)
 * @method empty_table($table = '')
 * @method truncate($table = '')
 * @method get_compiled_delete($table = '', $reset = true)
 * @method delete($table = '', $where = '', $limit = null, $reset_data = true)
 * @method dbprefix($table = '')
 * @method set_dbprefix($prefix = '')
 * @method start_cache()
 * @method stop_cache()
 * @method reset_query()
 * @method db_connect($persistent = false)
 * @method reconnect()
 * @method db_select($database = '')
 * @method version()
 * @method affected_rows()
 * @method insert_id()
 * @method field_data($table)
 * @method error()
 * @method initialize()
 * @method db_pconnect()
 * @method db_set_charset($charset)
 * @method platform()
 * @method query($sql, $binds = false, $return_object = null)
 * @method load_rdriver()
 * @method simple_query($sql)
 * @method trans_off()
 * @method trans_strict($mode = true)
 * @method trans_start($test_mode = false)
 * @method trans_complete()
 * @method trans_status()
 * @method trans_begin($test_mode = false)
 * @method trans_commit()
 * @method trans_rollback()
 * @method compile_binds($sql, $binds)
 * @method is_write_type($sql)
 * @method elapsed_time($decimals = 6)
 * @method total_queries()
 * @method last_query()
 * @method escape($str)
 * @method escape_str($str, $like = false)
 * @method escape_like_str($str)
 * @method primary($table)
 * @method count_all($table = '')
 * @method list_tables($constrain_by_prefix = false)
 * @method table_exists($table_name)
 * @method list_fields($table)
 * @method field_exists($field_name, $table_name)
 * @method escape_identifiers($item)
 * @method insert_string($table, $data)
 * @method update_string($table, $data, $where)
 * @method call_function($function)
 * @method cache_set_path($path = '')
 * @method cache_on()
 * @method cache_off()
 * @method cache_delete($segment_one = '', $segment_two = '')
 * @method cache_delete_all()
 * @method close()
 * @method display_error($error = '', $swap = '', $native = false)
 * @method protect_identifiers($item, $prefix_single = false, $protect_identifiers = null, $field_exists = true)
 */
class Database
{
    /**
     * The database object
     *
     * @var \CI_DB_mysqli_driver
     */
    private $oDb;

    // --------------------------------------------------------------------------

    /**
     * Construct the class, connect to the database
     */
    public function __construct()
    {
        $aParams = [

            //  Consistent between deployments
            'dbdriver' => APP_DB_DRIVER,
            'dbprefix' => APP_DB_GLOBAL_PREFIX,
            'pconnect' => APP_DB_PCONNECT,
            'cache_on' => APP_DB_CACHE,
            'char_set' => APP_DB_CHARSET,
            'dbcollat' => APP_DB_DBCOLLAT,
            'stricton' => APP_DB_STRICT,
            'swap_pre' => false,
            'autoinit' => true,

            /**
             * We always have the database in debug mode; errors will throw exceptions,
             * which are handled by the ErrorHandler. If this is set to false, errors
             * cause the query to return `false` which results in cascading errors for
             * things which expect an object.
             */
            'db_debug' => true,

            //  Potentially vary between deployments
            'hostname' => Factory::property('DB_HOST'),
            'username' => Factory::property('DB_USERNAME'),
            'password' => Factory::property('DB_PASSWORD'),
            'database' => Factory::property('DB_DATABASE'),
            'cachedir' => CACHE_PATH,
        ];

        $sDbPath = BASEPATH . 'database/';
        require_once $sDbPath . 'DB_driver.php';
        require_once $sDbPath . 'DB_query_builder.php';

        if (!class_exists('CI_DB')) {
            require_once __DIR__ . '/Database/CI_DB.php';
        }

        require_once $sDbPath . 'drivers/' . $aParams['dbdriver'] . '/' . $aParams['dbdriver'] . '_driver.php';

        $sDriver   = 'CI_DB_' . $aParams['dbdriver'] . '_driver';
        $this->oDb = new $sDriver($aParams);

        if (!empty($aParams['autoinit'])) {
            $this->oDb->initialize();
        }

        if (!empty($aParams['stricton'])) {
            $this->oDb->query('SET SESSION sql_mode="STRICT_ALL_TABLES"');
        }

        if (empty($this->oDb->conn_id)) {
            throw new ConnectionException(
                'Failed to connect to database',
                0
            );
        }

        $this->oDb->trans_strict(false);

        // --------------------------------------------------------------------------

        /**
         * If we're testing then define a global transaction which will be rolled back
         * at the end of the request. This is to ensure that the request does not make
         * any changes to the database so that subsequent tests can work with the
         * database in a known state.
         */
        if (Environment::is(Environment::ENV_HTTP_TEST)) {
            $this->oDb->trans_begin();
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Database destructor.
     */
    public function __destruct()
    {
        if (Environment::is(Environment::ENV_HTTP_TEST)) {
            $this->oDb->trans_rollback();
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Clears the query history and other memory hogs
     *
     * @return Database
     */
    public function flushCache()
    {
        $aProperties = [
            ['queries', []],
            ['query_times', []],
            ['data_cache', []],
        ];

        return $this->resetProperties($aProperties);
    }

    // --------------------------------------------------------------------------

    /**
     * Resets Active Record/Query Builder
     *
     * @return Database
     */
    public function reset()
    {
        $aProperties = [
            ['ar_select', []],
            ['ar_from', []],
            ['ar_join', []],
            ['ar_where', []],
            ['ar_like', []],
            ['ar_groupby', []],
            ['ar_having', []],
            ['ar_orderby', []],
            ['ar_wherein', []],
            ['ar_aliased_tables', []],
            ['ar_no_escape', []],
            ['ar_distinct', false],
            ['ar_limit', false],
            ['ar_offset', false],
            ['ar_order', false],
        ];

        return $this->resetProperties($aProperties);
    }

    // --------------------------------------------------------------------------

    /**
     * Safely resets properties
     *
     * @param array $aProperties The properties to reset; a multi-dimensional array where index 0 is the property and
     *                           index 1 is the value.
     *
     * @return $this
     */
    protected function resetProperties($aProperties)
    {
        foreach ($aProperties as $aProperty) {
            if (property_exists($this->oDb, $aProperty[0])) {
                $this->oDb->{$aProperty[0]} = $aProperty[1];
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Route calls to the CodeIgniter Database class
     *
     * @param string $sMethod    The method being called
     * @param array  $aArguments Any arguments being passed
     *
     * @return mixed
     */
    public function __call($sMethod, $aArguments)
    {
        if (method_exists($this, $sMethod)) {

            return call_user_func_array([$this, $sMethod], $aArguments);

        } else {

            return call_user_func_array([$this->oDb, $sMethod], $aArguments);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Pass any property "gets" to the CodeIgniter Database class
     *
     * @param string $sProperty The property to get
     *
     * @return mixed
     */
    public function __get($sProperty)
    {
        return $this->oDb->{$sProperty};
    }

    // --------------------------------------------------------------------------

    /**
     * Pass any property "sets" to the CodeIgniter Database class
     *
     * @param string $sProperty The property to set
     * @param mixed  $mValue    The value to set
     *
     * @return void
     */
    public function __set($sProperty, $mValue)
    {
        $this->oDb->{$sProperty} = $mValue;
    }
}
