<?php

/**
 * This class contains a collection of metadata items
 *
 * @package     Nails
 * @subpackage  common
 * @category    core
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Service\MetaData;

/**
 * Class Collection
 *
 * @package Nails\Common\Service\MetaData
 */
final class Collection extends \Nails\Common\Factory\Collection
{
    /** @var string */
    protected $sImplodeString;

    // --------------------------------------------------------------------------

    /**
     * Collection constructor.
     *
     * @param array  $aCollection
     * @param string $sImplodeString
     */
    public function __construct(array $aCollection = [], string $sImplodeString = null)
    {
        parent::__construct($aCollection);
        $this->sImplodeString = $sImplodeString;
    }

    // --------------------------------------------------------------------------

    /**
     * Implodes the collection, joined by $sImplodeString
     *
     * @param string|null $sImplodeString The string to implode with
     *
     * @return string
     */
    public function implode(string $sImplodeString = null): string
    {
        return implode($sImplodeString ?? $this->sImplodeString, $this->aCollection);
    }
}
