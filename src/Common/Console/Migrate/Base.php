<?php

namespace Nails\Common\Console\Migrate;

class Base {

    /**
     * Prepare and execute a DB query
     * @return PDOStatement
     */
    public function query($sQuery)
    {
        die($sQuery);
        //  @todo: replace the {{NAILS_DB_PREFIX}} string
    }

    // --------------------------------------------------------------------------

    /**
     * Execute the migration
     * @return Void
     */
    public function execute()
    {
        return true;
    }
}
