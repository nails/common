<?php

/**
 * This class abstracts the generic PDO library, for use by console applications
 *
 * Class Database
 *
 * @package Nails\Console
 */

namespace Nails\Common\Service;

use Nails\Common\Exception\Database\ConnectionException;
use Nails\Common\Exception\Database\TransactionException;

class PDODatabase
{
    /**
     * The PDO handler
     *
     * @var \PDO
     */
    protected $oDb;

    /**
     * Whether a transaction is currently running or not
     *
     * @var bool
     */
    protected $transactionRunning = false;

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
     * @param  string $sDbHost The database host
     * @param  string $sDbUser The database user
     * @param  string $sDbPass The database password
     * @param  string $sDbName The database
     *
     * @return void
     * @throws ConnectionException
     */
    public function connect($sDbHost = '', $sDbUser = '', $sDbPass = '', $sDbName = '')
    {
        //  Close the connection if one is open
        if (!is_null($this->oDb)) {
            $this->oDb = null;
        }

        $sDbHost = !empty($sDbHost) ? $sDbHost : (defined('DEPLOY_DB_HOST') ? DEPLOY_DB_HOST : '');
        $sDbUser = !empty($sDbUser) ? $sDbUser : (defined('DEPLOY_DB_USERNAME') ? DEPLOY_DB_USERNAME : '');
        $sDbPass = !empty($sDbPass) ? $sDbPass : (defined('DEPLOY_DB_PASSWORD') ? DEPLOY_DB_PASSWORD : '');
        $sDbName = !empty($sDbName) ? $sDbName : (defined('DEPLOY_DB_DATABASE') ? DEPLOY_DB_DATABASE : '');

        try {

            $this->oDb = new \PDO(
                'mysql:host=' . $sDbHost . ';dbname=' . $sDbName . ';charset=utf8', $sDbUser, $sDbPass
            );

            $this->oDb->exec('set names utf8');
            $this->oDb->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        } catch (\Exception $e) {
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
     * @param  string $sQuery The query to execute
     *
     * @return \PDOStatement
     */
    public function query($sQuery)
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
     * @param  string $sQuery The query to prepare
     *
     * @return \PDOStatement
     */
    public function prepare($sQuery)
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
     */
    public function lastInsertId()
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
     * @return \PDO
     */
    public function db()
    {
        return $this->oDb;
    }

    // --------------------------------------------------------------------------

    /**
     * Escapes a string to make it query safe
     *
     * @param  string $sString The string to escape
     *
     * @return string
     */
    public function escape($sString)
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
     * @return boolean
     * @throws TransactionException
     */
    public function transactionStart()
    {
        if (empty($this->oDb)) {
            $this->connect();
        }

        try {

            $this->oDb->beginTransaction();
            $this->transactionRunning = true;

            return true;

        } catch (\Exception $e) {
            throw new TransactionException($e->getMessage(), $e->getCode());
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Commits a DB transaction
     *
     * @return boolean
     * @throws TransactionException
     */
    public function transactionCommit()
    {
        if (empty($this->oDb)) {
            $this->connect();
        }

        try {

            $this->oDb->commit();
            $this->transactionRunning = false;

            return true;

        } catch (\Exception $e) {
            throw new TransactionException($e->getMessage(), $e->getCode());
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Rollsback a DB transaction
     *
     * @return boolean
     * @throws TransactionException
     */
    public function transactionRollback()
    {
        if (empty($this->oDb)) {
            $this->connect();
        }

        try {

            $this->oDb->rollback();
            $this->transactionRunning = false;

            return true;

        } catch (\Exception $e) {
            throw new TransactionException($e->getMessage(), $e->getCode());
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether a transaction is currently running
     *
     * @return boolean
     */
    public function isTransactionRunning()
    {
        return $this->transactionRunning;
    }
}
