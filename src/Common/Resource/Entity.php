<?php

namespace Nails\Common\Resource;

use Nails\Common\Model\Base;
use Nails\Common\Resource;

/**
 * Class Entity
 *
 * @package Nails\Common\Resource
 */
class Entity extends Resource
{
    public function __construct($mObj = [], protected ?Base $model = null)
    {
        parent::__construct($mObj);
    }

    /**
     * The entity's ID
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
     * The entity's creator's ID
     *
     * @var int|Resource|null
     */
    public $created_by;

    /**
     * The entity's modification date
     *
     * @var Resource\DateTime
     */
    public $modified;

    /**
     * The entity's modifier's ID
     *
     * @var int|Resource|null
     */
    public $modified_by;
}
