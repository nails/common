<?php

namespace Nails\Common\Traits\Model;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Model\Base;
use Nails\Factory;

/**
 * Trait Timestamps
 *
 * @package Nails\Common\Traits\Model
 */
trait Timestamps
{
    /**
     * Whether to automatically set created/modified timestamps
     *
     * @var bool|null
     * @deprecated Use constant AUTO_SET_TIMESTAMP instead
     */
    protected $tableAutoSetTimestamps;

    /**
     * Whether to skip the updating of the timestamp during an update
     */
    protected $bSkipUpdateTimestamp = false;

    // --------------------------------------------------------------------------

    /**
     * Returns the column name for specific columns of interest
     *
     * @param string      $sColumn  The column to query
     * @param string|null $sDefault The default value if not defined
     *
     * @return string|null
     */
    abstract public function getColumn(string $sColumn, string $sDefault = null): ?string;

    // --------------------------------------------------------------------------

    /**
     * Returns whether this model automatically generates timestamps or not
     *
     * @return bool
     */
    public function isAutoSetTimestamps(): bool
    {
        //  @todo (Pablo - 2019-04-15) - Phase out support for $this->tableAutoSetTimestamps
        return $this->tableAutoSetTimestamps ?? (defined('static::AUTO_SET_TIMESTAMP') ? static::AUTO_SET_TIMESTAMP : true);
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the timestamps on the $aData array
     *
     * @param array $aData       The data being passed to the model
     * @param bool  $bSetCreated Whether to set the created timestamp or not
     *
     * @return $this
     * @throws FactoryException
     */
    protected function setDataTimestamps(array &$aData, bool $bSetCreated = true): Base
    {
        if ($this->isAutoSetTimestamps() && ($bSetCreated || !$this->bSkipUpdateTimestamp)) {

            $oDate = Factory::factory('DateTime');

            if ($bSetCreated && empty($aData[$this->getColumnCreated()])) {
                $aData[$this->getColumnCreated()] = $oDate->format('Y-m-d H:i:s');
            }
            if (empty($aData[$this->getColumnModified()])) {
                $aData[$this->getColumnModified()] = $oDate->format('Y-m-d H:i:s');
            }
        }

        $this->bSkipUpdateTimestamp = false;

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the column to use for `created` timestamp`
     *
     * @return string
     */
    public function getColumnCreated(): string
    {
        return $this->getColumn('created', 'created');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the column to use for `created` timestamp`
     *
     * @return string
     */
    public function getColumnModified(): string
    {
        return $this->getColumn('modified', 'modified');
    }

    // --------------------------------------------------------------------------

    /**
     * Skips the next timestamp update
     *
     * @return $this
     */
    public function skipUpdateTimestamp(): Base
    {
        $this->bSkipUpdateTimestamp = true;
        return $this;
    }
}
