<?php

namespace Nails\Common\Helper\Model;

/**
 * Class Where
 *
 * @package Nails\Common\Helper\Model
 */
class Where
{
    private string $sColumn;
    private $mValue;
    private ?bool  $bEscape;

    // --------------------------------------------------------------------------

    /**
     * Where constructor.
     *
     * @param string    $sColumn
     * @param null      $mValue
     * @param bool|null $bEscape
     */
    public function __construct(string $sColumn, $mValue = null, bool $bEscape = null)
    {
        $this->sColumn = $sColumn;
        $this->mValue  = $mValue;
        $this->bEscape = $bEscape;
    }

    // --------------------------------------------------------------------------

    /**
     * Compile the statement
     *
     * @return array|string
     */
    public function compile()
    {
        return [$this->sColumn, $this->mValue, $this->bEscape];
    }
}
