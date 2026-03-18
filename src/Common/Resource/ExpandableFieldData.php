<?php

namespace Nails\Common\Resource;

use Nails\Common\Resource;

/**
 * Class ExpandableFieldData
 *
 * @package Nails\Common\Resource
 */
class ExpandableFieldData extends ExpandableFieldCount
{
    /**
     * The resources in the ExpandableFields collection
     *
     * @var Resource\Entity[]
     */
    public array $data = [];
}
