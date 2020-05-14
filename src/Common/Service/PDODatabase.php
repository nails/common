<?php

/**
 * This class abstracts the generic PDO library, for use by console applications
 *
 * Class Database
 *
 * @package Nails\Console
 */

namespace Nails\Common\Service;

use Exception;
use Nails\Common\Exception\Database\ConnectionException;
use Nails\Common\Exception\Database\TransactionException;
use PDO;
use PDOStatement;

/**
 * Class PDODatabase
 *
 * @package Nails\Common\Service
 */
class PDODatabase
{
    /**
     * The PDO handler
     *
     * @var PDO
     */
    protected $oDb;

    /**
     * Whether a transaction is currently running or not
     *
     * @var bool
     */
    protected $bIsTransactionRunning = false;

    // --------------------------------------------------------------------------

    /**
     * Exception messages and numbers
     */
    const ERR_MSG_CONNECTION_FAILED = 'Connection failed (%s: %s)';
    const ERR_NUM_CONNECTION_FAILED = 1;

    // --------------------------------------------------------------------------

    /**
     * Connect to the database
     *
     * @param string     $sDbHost The database host
     * @param string     $sDbUser The database user
     * @param string     $sDbPass The database password
     * @param string     $sDbName The database
     * @param string|int $sDbPort The database port
     *
     * @return void
     * @throws ConnectionException
     */
    public function connect($sDbHost = '', $sDbUser = '', $sDbPass = '', $sDbName = '', $sDbPort = '')
    {
        //  Close the connection if one is open
        if (!is_null($this->oDb)) {
            $this->oDb = null;
        }

        $sDbHost = !empty($sDbHost) ? $sDbHost : \Nails\Config::get('DB_HOST');
        $sDbUser = !empty($sDbUser) ? $sDbUser : \Nails\Config::get('DB_USERNAME');
        $sDbPass = !empty($sDbPass) ? $sDbPass : \Nails\Config::get('DB_PASSWORD');
        $sDbName = !empty($sDbName) ? $sDbName : \Nails\Config::get('DB_DATABASE');
        $sDbPort = !empty($sDbPort) ? $sDbPort : \Nails\Config::get('DB_PORT');

        try {

            $this->oDb = new PDO(
                sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=utf8',
                    $sDbHost,
                    $sDbPort,
                    $sDbName
                ),
                $sDbUser,
                $sDbPass
            );

            $this->oDb->exec('set names utf8');
            $this->oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (Exception $e) {
            throw new ConnectionException(
                sprintf(self::ERR_MSG_CONNECTION_FAILED, $e->getCode(), $e->getMessage()),
                self::ERR_NUM_CONNECTION_FAILED
            );
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Execute a query
     *
     * @param string $sQuery The query to execute
     *
     * @return PDOStatement
     * @throws ConnectionException
     */
    public function query($sQuery): PDOStatement
    {
        if (empty($this->oDb)) {
            $this->connect();
        }

        return $this->oDb->query($sQuery);
    }

    // --------------------------------------------------------------------------

    /**
     * Prepares an SQL query
     *
     * @param string $sQuery The query to prepare
     *
     * @return PDOStatement
     * @throws ConnectionException
     */
    public function prepare($sQuery): PDOStatement
    {
        if (empty($this->oDb)) {
            $this->connect();
        }

        return $this->oDb->prepare($sQuery);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the ID created by the previous write query
     *
     * @return string
     * @throws ConnectionException
     */
    public function lastInsertId(): string
    {
        if (empty($this->oDb)) {
            $this->connect();
        }

        return $this->oDb->lastInsertId();
    }

    // --------------------------------------------------------------------------

    /**
     * Exposes the database API
     *
     * @return PDO
     */
    public function db(): PDO
    {
        return $this->oDb;
    }

    // --------------------------------------------------------------------------

    /**
     * Escapes a string to make it query safe
     *
     * @param string $sString The string to escape
     *
     * @return string
     * @throws ConnectionException
     */
    public function escape($sString): string
    {
        if (empty($this->oDb)) {
            $this->connect();
        }

        return $this->oDb->quote($sString);
    }

    // --------------------------------------------------------------------------

    /**
     * Starts a DB transaction
     *
     * @return $this
     * @throws TransactionException
     * @throws ConnectionException
     */
    public function transactionStart(): self
    {
        if (empty($this->oDb)) {
            $this->connect();
        }

        try {

            $this->oDb->beginTransaction();
            $this->bIsTransactionRunning = true;

        } catch (Exception $e) {
            throw new TransactionException($e->getMessage(), $e->getCode());
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Commits a DB transaction
     *
     * @return $this
     * @throws TransactionException
     * @throws ConnectionException
     */
    public function transactionCommit(): self
    {
        if (empty($this->oDb)) {
            $this->connect();
        }

        try {

            $this->oDb->commit();
            $this->bIsTransactionRunning = false;

        } catch (Exception $e) {
            throw new TransactionException($e->getMessage(), $e->getCode());
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Rolls back a DB transaction
     *
     * @return $this
     * @throws TransactionException
     * @throws ConnectionException
     */
    public function transactionRollback(): self
    {
        if (empty($this->oDb)) {
            $this->connect();
        }

        try {

            $this->oDb->rollback();
            $this->bIsTransactionRunning = false;

        } catch (Exception $e) {
            throw new TransactionException($e->getMessage(), $e->getCode());
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether a transaction is currently running
     *
     * @return bool
     */
    public function isTransactionRunning(): bool
    {
        return $this->bIsTransactionRunning;
    }
}
