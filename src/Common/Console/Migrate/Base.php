<?php

namespace Nails\Common\Console\Migrate;

use Nails\Factory;

class Base
{
    /**
     * The database connection
     *
     * @var \PDO
     */
    public $oDb;

    // --------------------------------------------------------------------------

    /**
     * A counter which is incremented for each query to make it easier to trace wen
     *
     * @var int
     */
    protected $iQueryCount = 0;

    // --------------------------------------------------------------------------

    /**
     * The last query which was called via query() or prepare()
     *
     * @var string
     */
    protected $sLastQuery = '';

    // --------------------------------------------------------------------------

    public function __construct()
    {
        $this->oDb = Factory::service('ConsoleDatabase', 'nailsapp/module-console');
    }

    // --------------------------------------------------------------------------

    /**
     * Execute a DB query
     *
     * @param  string $sQuery The query to execute
     * @return \PDOStatement
     */
    public function query($sQuery)
    {
        $sQuery = $this->prepareQuery($sQuery);
        $this->iQueryCount++;
        $this->sLastQuery = $sQuery;

        return $this->oDb->query($sQuery);
    }

    // --------------------------------------------------------------------------

    /**
     * Prepare a DB query
     *
     * @param  string $sQuery The query to prepare
     * @return \PDOStatement
     */
    public function prepare($sQuery)
    {
        $sQuery = $this->prepareQuery($sQuery);
        $this->iQueryCount++;
        $this->sLastQuery = $sQuery;

        return $this->oDb->prepare($sQuery);
    }

    // --------------------------------------------------------------------------

    /**
     * Prepares a query before execution
     *
     * @param string $sQuery The query to prepare
     * @return mixed
     */
    protected function prepareQuery($sQuery)
    {
        $sQuery = str_replace('{{NAILS_DB_PREFIX}}', NAILS_DB_PREFIX, $sQuery);
        if (defined('APP_DB_PREFIX')) {
            $sQuery = str_replace('{{APP_DB_PREFIX}}', APP_DB_PREFIX, $sQuery);
        }

        return $sQuery;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the ID created by the previous write query
     *
     * @return string
     */
    public function lastInsertId()
    {
        return $this->oDb->lastInsertId();
    }

    // --------------------------------------------------------------------------

    /**
     * Exposes the database API
     *
     * @return \PDO
     */
    public function db()
    {
        return $this->oDb;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the value of iQueryCount
     *
     * @return int
     */
    public function getQueryCount()
    {
        return $this->iQueryCount;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the value of iQueryCount
     *
     * @return string
     */
    public function getLastQuery()
    {
        return $this->sLastQuery;
    }
}
