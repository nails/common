<?php

namespace Nails\Common\Helper\Model;

/**
 * Class Sort
 *
 * @package Nails\Common\Helper\Model
 */
class Sort
{
    const ASC    = 'asc';
    const DESC   = 'desc';
    const RANDOM = 'RANDOM';

    // --------------------------------------------------------------------------

    private string $sColumn;
    private string $sDirection;

    // --------------------------------------------------------------------------

    /**
     * Sort constructor.
     *
     * @param string $sColumn
     * @param null   $sDirection
     */
    public function __construct(string $sColumn, $sDirection = null)
    {
        $this->sColumn    = $sColumn;
        $this->sDirection = $sDirection ?? self::ASC;
    }

    // --------------------------------------------------------------------------

    /**
     * Compile the statement
     *
     * @return array|string
     */
    public function compile()
    {
        return [$this->sColumn, $this->sDirection];
    }
}
