<?php

namespace Nails\Common\Console\Migrate;

use Nails\Factory;

class Base
{
    /**
     * The database connection
     * @var PDO
     */
    public $oDb;

    // --------------------------------------------------------------------------

    private $sForeignKeyValue;

    // --------------------------------------------------------------------------

    public function __construct()
    {
        $this->oDb = Factory::service('ConsoleDatabase', 'nailsapp/module-console');
    }

    // --------------------------------------------------------------------------

    /**
     * Execute a DB query
     * @return PDOStatement
     */
    public function query($sQuery)
    {
        $sQuery = str_replace('{{NAILS_DB_PREFIX}}', NAILS_DB_PREFIX, $sQuery);
        return $this->oDb->query($sQuery);
    }

    // --------------------------------------------------------------------------

    /**
     * Prepare a DB query
     * @return PDOStatement
     */
    public function prepare($sQuery)
    {
        $sQuery = str_replace('{{NAILS_DB_PREFIX}}', NAILS_DB_PREFIX, $sQuery);
        return $this->oDb->prepare($sQuery);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the ID created by the previous write query
     * @return string
     */
    public function lastInsertId()
    {
        return $this->oDb->lastInsertId();
    }

    // --------------------------------------------------------------------------

    /**
     * Exposes the database API
     * @return PDO
     */
    public function db()
    {
        return $this->oDb;
    }
}
