<?php

namespace Nails\Common\Factory\PDODatabase;

use Nails\Common\Service\PDODatabase;

/**
 * Class ForeignKeyCheck
 *
 * @package Nails\Common\Factory\PDODatabase
 */
class ForeignKeyCheck
{
    protected PDODatabase $oDatabase;
    protected bool        $bPrevious = true;

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
     * Returns the status of foreign key checks
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        $oResult = $this->oDatabase
            ->query('SHOW Variables WHERE Variable_name="FOREIGN_KEY_CHECKS"')
            ->fetch(\PDO::FETCH_OBJ);

        return $oResult->Value === 'ON';
    }

    // --------------------------------------------------------------------------

    /**
     * Turns foreign key chcks on
     *
     * @return $this
     */
    public function on(): self
    {
        $this->oDatabase->query('SET FOREIGN_KEY_CHECKS = 1');
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * turns foreign key checks off
     *
     * @return $this
     */
    public function off(): self
    {
        $this->oDatabase->query('SET FOREIGN_KEY_CHECKS = 0');
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Saves the current state of foreign key checks, use with restorePrevious();
     *
     * @return $this
     */
    public function saveCurrent(): self
    {
        $this->bPrevious = $this->isEnabled();
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Restores the previous foreign key check mode
     *
     * @return $this
     */
    public function restorePrevious(): self
    {
        if ($this->bPrevious !== null) {
            if ($this->bPrevious) {
                $this->on();
            } else {
                $this->off();
            }
        }

        return $this;
    }
}
