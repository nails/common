<?php

namespace Nails\Common\Traits\Model;

/**
 * Trait Mergeable
 *
 * @package Nails\Common\Traits\Model
 */
trait Mergeable
{
    /**
     * Merges items into an original
     *
     * @param int   $iKeepId   The ID of the item to keep
     * @param int[] $aMergeIds An array of IDs to merge into $iKeepId
     */
    abstract public function merge(int $iKeepId, array $aMergeIds): self;
}
