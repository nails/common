<?php

namespace Nails\Common\Interfaces\Database;

/**
 * Interface Migration
 *
 * @package Nails\Common\Interfaces\Database
 */
interface Migration
{
    /**
     * Migration constructor.
     *
     * @param \Nails\Common\Service\PDODatabase $oDb
     */
    public function __construct(\Nails\Common\Service\PDODatabase $oDb);

    /**
     * Returns the priority of the migration
     *
     * @return int
     */
    public function getPriority(): int;

    /**
     * Executes the migration
     *
     * @return mixed
     */
    public function execute();

    /**
     * Returns how many queries have been attempted
     *
     * @return int
     */
    public function getQueryCount(): int;

    /**
     * Returns the last query
     *
     * @return string
     */
    public function getLastQuery(): string;
}
