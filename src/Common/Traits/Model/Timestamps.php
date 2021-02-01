<?php

namespace Nails\Common\Traits\Model;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Model\Base;
use Nails\Factory;

/**
 * Trait Timestamps
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
     * Returns whether this model automatically generates timestamps or not
     *
     * @return bool
     */
    public function isAutoSetTimestamps()
    {
        //  @todo (Pablo - 2019-04-15) - Phase out support for $this->tableAutoSetTimestamps
        return $this->tableAutoSetTimestamps ?? (defined('static::AUTO_SET_TIMESTAMP') ? static::AUTO_SET_TIMESTAMP : false);
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

            if ($bSetCreated && empty($aData[$this->getColumn('created')])) {
                $aData[$this->getColumn('created')] = $oDate->format('Y-m-d H:i:s');
            }
            if (empty($aData[$this->getColumn('modified')])) {
                $aData[$this->getColumn('modified')] = $oDate->format('Y-m-d H:i:s');
            }
        }

        $this->bSkipUpdateTimestamp = false;

        return $this;
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
