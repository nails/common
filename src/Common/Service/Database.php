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
use Nails\Testing;

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
        if (Environment::is(Environment::ENV_TEST)) {
            $this->oDb->trans_begin();
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Database destructor.
     */
    public function __destruct()
    {
        if (Environment::is(Environment::ENV_TEST)) {
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
     * @param  string $sMethod    The method being called
     * @param  array  $aArguments Any arguments being passed
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
     * @param  string $sProperty The property to get
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
     * @param  string $sProperty The property to set
     * @param  mixed  $mValue    The value to set
     *
     * @return void
     */
    public function __set($sProperty, $mValue)
    {
        $this->oDb->{$sProperty} = $mValue;
    }
}
