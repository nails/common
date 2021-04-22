<?php

namespace Nails\Common\Traits\Database;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Traits;
use Nails\Factory;

/**
 * Trait Seeder
 *
 * @package Nails\Common\Traits\Database
 */
trait Seeder
{
    use Traits\Database\Seeder\Collection;
    use Traits\Database\Seeder\DateTime;
    use Traits\Database\Seeder\LoremIpsum;
    use Traits\Database\Seeder\Model;
    use Traits\Database\Seeder\Scalar;
    use Traits\Database\Seeder\Strings;

    // --------------------------------------------------------------------------

    /**
     * The database object
     *
     * @var PDODatabase
     */
    protected $oDb;

    // --------------------------------------------------------------------------

    /**
     * Base constructor.
     *
     * @param PDODatabase $oDb
     */
    public function __construct(PDODatabase $oDb)
    {
        $this->oDb = $oDb;
    }

    // --------------------------------------------------------------------------

    /**
     * Execute any pre-seed setup in here
     *
     * @return $this
     */
    public function pre(): self
    {
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Perform any post-seed cleaning up here
     *
     * @return $this
     */
    public function post(): self
    {
        return $this;
    }
}
