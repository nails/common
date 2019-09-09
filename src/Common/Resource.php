<?php

namespace Nails\Common;

/**
 * Class Resource
 *
 * @package Nails\Common
 */
class Resource
{
    /**
     * The resource's ID
     *
     * @var int|null
     */
    public $id = null;

    /**
     * The source's creation date
     *
     * @var Resource\DateTime
     */
    public $created;

    /**
     * The resource's creator's ID
     *
     * @var int|Resource|null
     */
    public $created_by;

    /**
     * The resource's modification date
     *
     * @var Resource\DateTime
     */
    public $modified;

    /**
     * The resource's modifier's ID
     *
     * @var int|Resource|null
     */
    public $modified_by;

    // --------------------------------------------------------------------------

    /**
     * Resource constructor.
     *
     * @param self|\stdClass|array $mObj The data to populate the resource with
     */
    public function __construct($mObj = [])
    {
        foreach ($mObj as $sProperty => $mValue) {
            $this->{$sProperty} = $mValue;
        }
    }
}
