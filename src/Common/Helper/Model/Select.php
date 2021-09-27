<?php

namespace Nails\Common\Helper\Model;

/**
 * Class Select
 *
 * @package Nails\Common\Helper\Model
 */
class Select
{
    private array $aColumns = [];

    // --------------------------------------------------------------------------

    /**
     * @param string[] $aColumns
     */
    public function __construct(array $aColumns)
    {
        $this->aColumns = $aColumns;
    }

    // --------------------------------------------------------------------------

    /**
     * Compile the statement
     *
     * @return array|string
     */
    public function compile()
    {
        return $this->aColumns;
    }
}
