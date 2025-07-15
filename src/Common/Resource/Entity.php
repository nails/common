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
    /**
     * The entity's ID
     */
    public ?int $id = null;

    /**
     * The source's creation date
     */
    public ?Resource\DateTime $created;

    /**
     * The entity's creator's ID (or User object, if expanded)
     */
    public int|Resource|null $created_by;

    /**
     * The entity's modification date
     */
    public ?Resource\DateTime $modified;

    /**
     * The entity's modifier's ID (or User object, if expanded)
     */
    public int|Resource|null $modified_by;

    // --------------------------------------------------------------------------

    public function __construct($mObj = [], protected ?Base $model = null)
    {
        parent::__construct($mObj);
    }

    // --------------------------------------------------------------------------

    public function getModel(): ?Base
    {
        return $this->model;
    }
}
