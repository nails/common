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

use CI_DB_mysqli_driver;
use Nails\Common\Exception\Database\ConnectionException;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Factory\Database\Transaction;
use Nails\Common\Factory\Database\ForeignKeyCheck;
use Nails\Common\Helper\ArrayHelper;
use Nails\Config;
use Nails\Environment;
use Nails\Factory;
use Nails\Testing;

/**
 * Class Database
 *
 * @method \CI_DB_query_builder select($select = '*', $escape = null)
 * @method \CI_DB_query_builder select_max($select = '', $alias = '')
 * @method \CI_DB_query_builder select_min($select = '', $alias = '')
 * @method \CI_DB_query_builder select_avg($select = '', $alias = '')
 * @method \CI_DB_query_builder select_sum($select = '', $alias = '')
 * @method \CI_DB_query_builder distinct($val = true)
 * @method \CI_DB_query_builder from($from)
 * @method \CI_DB_query_builder join($table, $cond, $type = '', $escape = null)
 * @method \CI_DB_query_builder where($key, $value = null, $escape = null)
 * @method \CI_DB_query_builder or_where($key, $value = null, $escape = null)
 * @method \CI_DB_query_builder where_in($key = null, $values = null, $escape = null)
 * @method \CI_DB_query_builder or_where_in($key = null, $values = null, $escape = null)
 * @method \CI_DB_query_builder where_not_in($key = null, $values = null, $escape = null)
 * @method \CI_DB_query_builder or_where_not_in($key = null, $values = null, $escape = null)
 * @method \CI_DB_query_builder like($field, $match = '', $side = 'both', $escape = null)
 * @method \CI_DB_query_builder not_like($field, $match = '', $side = 'both', $escape = null)
 * @method \CI_DB_query_builder or_like($field, $match = '', $side = 'both', $escape = null)
 * @method \CI_DB_query_builder  or_not_like($field, $match = '', $side = 'both', $escape = null)
 * @method \CI_DB_query_builder group_start($not = '', $type = 'AND ')
 * @method \CI_DB_query_builder or_group_start()
 * @method \CI_DB_query_builder not_group_start()
 * @method \CI_DB_query_builder or_not_group_start()
 * @method \CI_DB_query_builder group_end()
 * @method \CI_DB_query_builder group_by($by, $escape = null)
 * @method \CI_DB_query_builder having($key, $value = null, $escape = null)
 * @method \CI_DB_query_builder or_having($key, $value = null, $escape = null)
 * @method \CI_DB_query_builder order_by($orderby, $direction = '', $escape = null)
 * @method \CI_DB_query_builder limit($value, $offset = 0)
 * @method \CI_DB_query_builder offset($offset)
 * @method \CI_DB_query_builder set($key, $value = '', $escape = null)
 * @method string get_compiled_select($table = '', $reset = true)
 * @method \CI_DB_result get($table = '', $limit = null, $offset = null)
 * @method int count_all_results($table = '', $reset = true)
 * @method \CI_DB_result get_where($table = '', $where = null, $limit = null, $offset = null)
 * @method int insert_batch($table, $set = null, $escape = null, $batch_size = 100)
 * @method \CI_DB_query_builder set_insert_batch($key, $value = '', $escape = null)
 * @method string get_compiled_insert($table = '', $reset = true)
 * @method bool insert($table = '', $set = null, $escape = null)
 * @method bool replace($table = '', $set = null)
 * @method string get_compiled_update($table = '', $reset = true)
 * @method bool update($table = '', $set = null, $where = null, $limit = null)
 * @method int update_batch($table, $set = null, $index = null, $batch_size = 100)
 * @method \CI_DB_query_builder set_update_batch($key, $index = '', $escape = null)
 * @method bool empty_table($table = '')
 * @method bool truncate($table = '')
 * @method string get_compiled_delete($table = '', $reset = true)
 * @method mixed delete($table = '', $where = '', $limit = null, $reset_data = true)
 * @method string dbprefix($table = '')
 * @method string set_dbprefix($prefix = '')
 * @method \CI_DB_query_builder start_cache()
 * @method \CI_DB_query_builder stop_cache()
 * @method \CI_DB_query_builder flush_cache()
 * @method \CI_DB_query_builder reset_query()
 * @method mixed db_connect($persistent = false)
 * @method void reconnect()
 * @method bool db_select($database = '')
 * @method string version()
 * @method int affected_rows()
 * @method insert_id()
 * @method array field_data($table)
 * @method array error()
 * @method bool initialize()
 * @method mixed db_pconnect()
 * @method bool db_set_charset($charset)
 * @method string platform()
 * @method mixed query($sql, $binds = false, $return_object = null)
 * @method string load_rdriver()
 * @method mixed simple_query($sql)
 * @method string compile_binds($sql, $binds)
 * @method bool is_write_type($sql)
 * @method string elapsed_time($decimals = 6)
 * @method int total_queries()
 * @method string last_query()
 * @method mixed escape($str)
 * @method string escape_str($str, $like = false)
 * @method mixed escape_like_str($str)
 * @method string primary($table)
 * @method int count_all($table = '')
 * @method array list_tables($constrain_by_prefix = false)
 * @method bool table_exists($table_name)
 * @method array list_fields($table)
 * @method bool field_exists($field_name, $table_name)
 * @method mixed escape_identifiers($item)
 * @method string insert_string($table, $data)
 * @method string update_string($table, $data, $where)
 * @method mixed call_function($function)
 * @method void cache_set_path($path = '')
 * @method bool cache_on()
 * @method bool cache_off()
 * @method bool cache_delete($segment_one = '', $segment_two = '')
 * @method bool cache_delete_all()
 * @method void close()
 * @method string display_error($error = '', $swap = '', $native = false)
 * @method string protect_identifiers($item, $prefix_single = false, $protect_identifiers = null, $field_exists = true)
 */
class Database
{
    /**
     * The database object
     *
     * @var CI_DB_mysqli_driver
     */
    private $oDb;

    /** @var string[] */
    private static $aReservedWords = [];

    // --------------------------------------------------------------------------

    /**
     * Database constructor.
     *
     * @throws ConnectionException
     * @throws FactoryException
     */
    public function __construct()
    {
        /** @var FileCache $oFileCache */
        $oFileCache = Factory::service('FileCache');

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
            'hostname' => Config::get('DB_HOST'),
            'username' => Config::get('DB_USERNAME'),
            'password' => Config::get('DB_PASSWORD'),
            'port'     => Config::get('DB_PORT'),
            'database' => Environment::is([Environment::ENV_TEST, Environment::ENV_HTTP_TEST])
                ? Testing::DB_NAME
                : Config::get('DB_DATABASE'),
            'cachedir' => $oFileCache->getDir(),
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
            $iErrorReporting = error_reporting();
            error_reporting(0);
            $this->oDb->initialize();
            error_reporting($iErrorReporting);
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

        //  Make sure the database is running on UTC
        $this->query('SET time_zone = \'+0:00\'');

        // --------------------------------------------------------------------------

        /**
         * If we're testing then define a global transaction which will be rolled back
         * at the end of the request. This is to ensure that the request does not make
         * any changes to the database so that subsequent tests can work with the
         * database in a known state.
         */
        if (Environment::is(Environment::ENV_HTTP_TEST)) {
            $this->transaction()->start();
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Database destructor.
     */
    public function __destruct()
    {
        if (Environment::is(Environment::ENV_HTTP_TEST)) {
            $this->transaction()->rollback();
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the connection's host
     *
     * @return string
     */
    public function getDbHost(): string
    {
        return (string) Config::get('DB_HOST');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the connection's username
     *
     * @return string
     */
    public function getDbUsername(): string
    {
        return (string) Config::get('DB_USERNAME');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the connection's password
     *
     * @return string
     */
    public function getDbPassword(): string
    {
        return (string) Config::get('DB_HOST');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the connection's port
     *
     * @return string
     */
    public function getDbPort(): string
    {
        return (string) Config::get('DB_PORT');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the connection's database
     *
     * @return string
     */
    public function getDbDatabase(): string
    {
        return Environment::is([Environment::ENV_TEST, Environment::ENV_HTTP_TEST])
            ? Testing::DB_NAME
            : (string) Config::get('DB_DATABASE');
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

        $this->resetProperties($aProperties);
        $this->oDb->reset_query();
        return $this;
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
     * Returns the Transaction object
     *
     * @return Transaction
     * @throws \Nails\Common\Exception\FactoryException
     */
    public function transaction(): Transaction
    {
        return Factory::factory('DatabaseTransaction', null, $this);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the ForeignKeyCheck object
     *
     * @return ForeignKeyCheck
     * @throws \Nails\Common\Exception\FactoryException
     */
    public function foreignKeyCheck(): ForeignKeyCheck
    {
        return Factory::factory('DatabaseForeignKeyCheck', null, $this);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a list of MySQL reserved words
     *
     * @return string[]
     */
    public function getReservedWords(): array
    {
        if (!empty(self::$aReservedWords)) {
            return self::$aReservedWords;
        }

        $aResult = $this
            ->query('SELECT WORD FROM INFORMATION_SCHEMA.KEYWORDS WHERE RESERVED = 1;')
            ->result();

        self::$aReservedWords = ArrayHelper::extract($aResult, 'WORD');

        return self::$aReservedWords;
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
