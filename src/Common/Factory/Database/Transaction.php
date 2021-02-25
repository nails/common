<?php

namespace Nails\Common\Factory\Database;

use Nails\Common\Service\Database;

/**
 * Class Transaction
 *
 * @package Nails\Common\Factory\Database
 */
class Transaction
{
    /** @var Database */
    protected $oDatabase;

    // --------------------------------------------------------------------------

    /**
     * CriticalCss constructor.
     *
     * @param Database $oDatabase The database service
     */
    public function __construct(Database $oDatabase)
    {
        $this->oDatabase = $oDatabase;
    }

    // --------------------------------------------------------------------------

    /**
     * starts a transaction
     *
     * @return $this
     */
    public function start(): self
    {
        $this->oDatabase->trans_begin();
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Gets the status of a transaction
     *
     * @return bool
     */
    public function status(): bool
    {
        return $this->oDatabase->trans_status();
    }

    // --------------------------------------------------------------------------

    /**
     * Commits a transaction
     *
     * @return $this
     */
    public function commit(): self
    {
        $this->oDatabase->trans_commit();
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Rollsback a transaction
     *
     * @return $this
     */
    public function rollback(): self
    {
        $this->oDatabase->trans_rollback();
        return $this;
    }
}
