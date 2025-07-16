<?php

namespace Nails\Common\Resource;

use Nails\Common\Resource;

/**
 * Class ExpandableField
 *
 * @package Nails\Common\Resource
 */
class ExpandableField extends Resource
{
    /**
     * The number of items in the ExpandableField collection
     *
     * @var int
     */
    public int $count = 0;

    /**
     * The resources in the ExpandableFields collection
     *
     * @var Resource\Entity[]
     */
    public array $data = [];
}
