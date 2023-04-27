<?php

namespace Nails\Common\Helper\Model;

/**
 * Class Join
 *
 * @package Nails\Common\Helper\Model
 */
class Join
{
    const TYPE_LEFT  = 'LEFT';
    const TYPE_RIGHT = 'RIGHT';
    const TYPE_INNER = 'INNER';

    // --------------------------------------------------------------------------

    private string  $sTable;
    private string  $sOn;
    private ?string $sType;

    // --------------------------------------------------------------------------

    /**
     * Join constructor.
     *
     * @param string $sTable
     */
    public function __construct(string $sTable, string $sOn, string $sType = null)
    {
        $this->sTable = $sTable;
        $this->sOn    = $sOn;
        $this->sType  = $sType ?? self::TYPE_INNER;
    }

    // --------------------------------------------------------------------------

    /**
     * Compile the statement
     *
     * @return array|string
     */
    public function compile()
    {
        return [
            $this->sTable,
            $this->sOn,
            $this->sType,
        ];
    }
}
