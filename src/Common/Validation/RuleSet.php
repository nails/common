<?php

namespace Nails\Common\Validation;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Class RuleSet
 *
 * An ordered collection of Fields, keyed by field name
 *
 * @package Nails\Common\Validation
 *
 * @implements IteratorAggregate<string, Field>
 */
final class RuleSet implements IteratorAggregate, Countable
{
    /** @var array<string, Field> */
    private array $aFields = [];

    // --------------------------------------------------------------------------

    /**
     * Adds a field; a field with the same name is replaced
     */
    public function add(Field $oField): self
    {
        $this->aFields[$oField->name] = $oField;
        return $this;
    }

    public function has(string $sName): bool
    {
        return array_key_exists($sName, $this->aFields);
    }

    public function get(string $sName): ?Field
    {
        return $this->aFields[$sName] ?? null;
    }

    /**
     * @return array<string, Field>
     */
    public function all(): array
    {
        return $this->aFields;
    }

    public function count(): int
    {
        return count($this->aFields);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->aFields);
    }
}
