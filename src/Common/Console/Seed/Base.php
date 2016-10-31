<?php

namespace Nails\Common\Console\Seed;

class Base
{
    /**
     * The database object
     * @var \Nails\Console\Database;
     */
    protected $oDb;

    // --------------------------------------------------------------------------

    /**
     * Base constructor.
     *
     * @param $oDb
     */
    public function __construct($oDb)
    {
        $this->oDb = $oDb;
    }

    // --------------------------------------------------------------------------

    /**
     * Execute any pre-seed setup in here
     */
    public function pre()
    {
    }

    // --------------------------------------------------------------------------

    /**
     * The main seeding method
     */
    public function execute()
    {
    }

    // --------------------------------------------------------------------------

    /**
     * Perform any post-seed cleaning up here
     */
    public function post()
    {
    }
}
