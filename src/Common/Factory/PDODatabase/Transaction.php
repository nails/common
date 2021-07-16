<?php

namespace Nails\Common\Factory\PDODatabase;

use Nails\Common\Service\PDODatabase;
use Nails\Common\Exception\PDODatabase\TransactionException;

/**
 * Class Transaction
 *
 * @package Nails\Common\Factory\PDODatabase
 */
class Transaction
{
    /** @var PDODatabase */
    protected $oDatabase;

    /**
     * Whether a transaction is currently running or not
     *
     * @var bool
     */
    protected $bIsTransactionRunning = false;

    // --------------------------------------------------------------------------

    /**
     * CriticalCss constructor.
     *
     * @param PDODatabase $oDatabase The database service
     */
    public function __construct(PDODatabase $oDatabase)
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
        try {

            $this->oDatabase->db()->beginTransaction();
            $this->bIsTransactionRunning = true;

        } catch (Exception $e) {
            throw new TransactionException($e->getMessage(), $e->getCode(), $e);
        }

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
        return $this->bIsTransactionRunning;
    }

    // --------------------------------------------------------------------------

    /**
     * Commits a transaction
     *
     * @return $this
     */
    public function commit(): self
    {
        try {

            $this->oDatabase->db()->commit();
            $this->bIsTransactionRunning = false;

        } catch (Exception $e) {
            throw new TransactionException($e->getMessage(), $e->getCode(), $e);
        }

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
        try {

            $this->oDatabase->db()->rollBack();
            $this->bIsTransactionRunning = false;

        } catch (Exception $e) {
            throw new TransactionException($e->getMessage(), $e->getCode(), $e);
        }

        return $this;
    }
}
