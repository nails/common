<?php

namespace Nails\Common\Interfaces\Database;

use Nails\Common\Service\PDODatabase;

/**
 * Interface Seeder
 *
 * @package Nails\Common\Interfaces\Database
 */
interface Seeder
{
    /**
     * Seed constructor.
     *
     * @param PDODatabase $oDb
     */
    public function __construct(PDODatabase $oDb);

    // --------------------------------------------------------------------------

    /**
     * Returns the seeders priority
     *
     * @return int
     */
    public static function getPriority(): int;

    // --------------------------------------------------------------------------

    /**
     * Execute any pre-seed setup in here
     */
    public function pre(): self;

    // --------------------------------------------------------------------------

    /**
     * The main seeding method
     */
    public function execute(): self;

    // --------------------------------------------------------------------------

    /**
     * Perform any post-seed cleaning up here
     */
    public function post(): self;
}
