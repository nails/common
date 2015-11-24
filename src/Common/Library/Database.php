<?php

/**
 * The class abstracts the database
 * @todo remove dependency on CodeIgniter's Database abstraction
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Library;

class Database
{
    /**
     * The database object
     * @var \CI_DB_mysqli_driver
     */
    private $oDb;

    // --------------------------------------------------------------------------

    /**
     * Construct the class, connect to the database
     */
    public function __construct()
    {
        $oCi       = get_instance();
        $this->oDb = $oCi->load->database();

        if (empty($this->oDb->conn_id)) {

            throw new \Nails\Common\Exception\Database\ConnectionException(
                'Failed to connect to database',
                0
            );
        }

        /**
         * Don't run transactions in strict mode. In my opinion it's odd behaviour:
         * When a transaction is committed it should be the end of the story. If it's
         * not then a failure elsewhere can cause a rollback unexpectedly. Silly CI.
         */

        $this->oDb->trans_strict(false);
    }

    // --------------------------------------------------------------------------

    /**
     * Clears the query history and other memory hogs
     * @return Object
     */
    public function flushCache()
    {
        $this->oDb->queries     = array();
        $this->oDb->query_times = array();
        $this->oDb->data_cache  = array();
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Resets Active Record/Query Builder
     * @return Object
     */
    public function reset()
    {
        $this->oDb->ar_select         = array();
        $this->oDb->ar_from           = array();
        $this->oDb->ar_join           = array();
        $this->oDb->ar_where          = array();
        $this->oDb->ar_like           = array();
        $this->oDb->ar_groupby        = array();
        $this->oDb->ar_having         = array();
        $this->oDb->ar_orderby        = array();
        $this->oDb->ar_wherein        = array();
        $this->oDb->ar_aliased_tables = array();
        $this->oDb->ar_no_escape      = array();
        $this->oDb->ar_distinct       = false;
        $this->oDb->ar_limit          = false;
        $this->oDb->ar_offset         = false;
        $this->oDb->ar_order          = false;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Route calls to the CodeIgniter Database class
     * @param  string $sMethod    The method being called
     * @param  array  $aArguments Any arguments being passed
     * @return mixed
     */
    public function __call($sMethod, $aArguments)
    {
        if (method_exists($this, $sMethod)) {

            return call_user_func_array(array($this, $sMethod), $aArguments);

        } else {

            return call_user_func_array(array($this->oDb, $sMethod), $aArguments);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Pass any property "gets" to the CodeIgniter Database class
     * @param  string $sProperty The property to get
     * @return mixed
     */
    public function __get($sProperty)
    {
        return $this->oDb->{$sProperty};
    }

    // --------------------------------------------------------------------------

    /**
     * Pass any property "sets" to the CodeIgniter Database class
     * @param  string $sProperty The property to set
     * @param  mixed  $mValue    The value to set
     * @return mixed
     */
    public function __set($sProperty, $mValue)
    {
        $this->oDb->{$sProperty} = $mValue;
    }
}
