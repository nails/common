<?php

namespace Nails\Common\Resource;

use Nails\Common\Resource;

class ExpandableField extends Resource
{
    /**
     * The number of items in the ExpandableField collection
     *
     * @var int
     */
    public $count = 0;

    /**
     * The resources in the ExpandableFields collection
     *
     * @var Resource[]
     */
    public $data = [];
}
