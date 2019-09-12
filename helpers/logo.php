<?php

/**
 * This file provides logo related helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 */

use Nails\Common\Helper\Logo;

if (!function_exists('logoDiscover')) {
    function logoDiscover(): ?string
    {
        return Logo::discover();
    }
}
