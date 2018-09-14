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
        $this->oDb = Factory::service('PDODatabase');
    }

    // --------------------------------------------------------------------------

    /**
     * Execute a DB query
     *
     * @param  string $sQuery The query to execute
     *
     * @return \PDOStatement
     */
    public function query($sQuery)
    {
        $sQuery = $this->replaceConstants($sQuery);
        $this->iQueryCount++;
        $this->sLastQuery = $sQuery;

        return $this->oDb->query($sQuery);
    }

    // --------------------------------------------------------------------------

    /**
     * Prepare a DB query
     *
     * @param  string $sQuery The query to prepare
     *
     * @return \PDOStatement
     */
    public function prepare($sQuery)
    {
        $sQuery = $this->replaceConstants($sQuery);
        $this->iQueryCount++;
        $this->sLastQuery = $sQuery;

        return $this->oDb->prepare($sQuery);
    }

    // --------------------------------------------------------------------------

    /**
     * Replaces {{CONSTANT}} with the value of constant, CONSTANT
     *
     * @param string $sString The string to search on
     *
     * @return string
     */
    protected function replaceConstants($sString)
    {
        return preg_replace_callback(
            '/{{(.+?)}}/',
            function ($aMatches) {
                if (defined($aMatches[1])) {
                    return constant($aMatches[1]);
                }

                return $aMatches[0];
            },
            $sString
        );
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
