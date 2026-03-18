<?php

namespace Nails\Common\Resource;

use Nails\Common\Resource;

/**
 * Class ExpandableFieldCount
 *
 * @package Nails\Common\Resource
 */
class ExpandableFieldCount extends Resource
{
    /**
     * The number of items counted
     *
     * @var int
     */
    public int $count = 0;
}
