<?php

namespace Nails\Common\Traits\Database\Seeder;

/**
 * Trait Strings
 *
 * @package Nails\Common\Traits\Database\Seeder
 */
trait Strings
{
    abstract protected function loremWord($iNumWords = 5);

    // --------------------------------------------------------------------------

    /**
     * Generates a random email address
     *
     * @return string
     */
    protected function email($sDomain = 'example.com'): string
    {
        return sprintf(
            '%s@%s',
            str_replace(' ', '-', $this->loremWord(3)),
            $sDomain
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a random URL
     *
     * @param string $sProtocol The protocol to use
     * @param string $sTld      The TLD to use
     *
     * @return string
     */
    protected function url($sProtocol = 'https', $sTld = '.com'): string
    {
        return sprintf(
            '%s://%s%s',
            $sProtocol,
            url_title($this->loremWord(2)),
            $sTld
        );
    }
}
