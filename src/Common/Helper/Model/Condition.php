<?php

namespace Nails\Common\Helper\Model;

/**
 * Class Condition
 *
 * @package Nails\Common\Helper\Model
 */
class Condition
{
    private string $sCondition;

    // --------------------------------------------------------------------------

    /**
     * Condition constructor.
     *
     * @param string $sCondition
     */
    public function __construct(string $sCondition)
    {
        $this->sCondition = $sCondition;
    }

    // --------------------------------------------------------------------------

    /**
     * Compile the statement
     *
     * @return array|string
     */
    public function compile()
    {
        return sprintf('(%s)', $this->sCondition);
    }
}
