<?php

namespace Nails\Common\Traits\Database\Seeder;

/**
 * Trait Scalar
 *
 * @package Nails\Common\Traits\Database\Seeder
 */
trait Scalar
{
    /**
     * Randomly returns true or false
     *
     * @return bool
     */
    protected function randomBool(): bool
    {
        return (bool) rand(0, 1);
    }

    // --------------------------------------------------------------------------

    /**
     * Return a random integer
     *
     * @param integer $iLow  The lowest possible value to return
     * @param integer $iHigh The highest possible value to return
     *
     * @return int
     */
    protected function randomInteger($iLow = 0, $iHigh = 1000): int
    {
        return rand($iLow, $iHigh);
    }
}
