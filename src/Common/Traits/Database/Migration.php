<?php

namespace Nails\Common\Traits\Database;

use Nails\Common\Exception\FactoryException;
use Nails\Factory;

/**
 * Trait Migration
 *
 * @package Nails\Common\Traits\Database
 */
trait Migration
{
    /**
     * The database connection
     *
     * @var \Nails\Common\Service\PDODatabase
     */
    protected $oDb;

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

    /**
     * Base constructor.
     *
     * @param \Nails\Common\Service\PDODatabase $oDb
     */
    public function __construct(\Nails\Common\Service\PDODatabase $oDb)
    {
        $this->oDb = $oDb;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the priority of the migration
     *
     * @return int
     */
    public function getPriority(): int
    {
        return (int) preg_replace('/^.*?(\d+$)/', '$1', static::class);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns how many queries have been attempted
     *
     * @return int
     */
    public function getQueryCount(): int
    {
        return $this->iQueryCount;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the last query
     *
     * @return string
     */
    public function getLastQuery(): string
    {
        return $this->sLastQuery;
    }

    // --------------------------------------------------------------------------

    /**
     * Execute a DB query
     *
     * @param string $sQuery The query to execute
     *
     * @return \PDOStatement
     */
    public function query(string $sQuery): \PDOStatement
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
     * @param string $sQuery The query to prepare
     *
     * @return \PDOStatement
     */
    public function prepare(string $sQuery): \PDOStatement
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
    protected function replaceConstants(string $sString): string
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
    public function lastInsertId(): ?int
    {
        return (int) $this->oDb->lastInsertId() ?: null;
    }

    // --------------------------------------------------------------------------

    /**
     * Exposes the database API
     *
     * @return \PDO
     */
    public function db(): \PDO
    {
        return $this->oDb;
    }
}
