<?php

namespace Nails\Common\Validation;

use Nails\Common\Interfaces\Validation\Rule;

/**
 * Class AbstractRule
 *
 * Convenience base for rules: declare NAME, DEFAULT_MESSAGE (and optionally
 * ALIASES) then implement apply().
 *
 * @package Nails\Common\Validation
 */
abstract class AbstractRule implements Rule
{
    public const NAME            = '';
    public const ALIASES         = [];
    public const DEFAULT_MESSAGE = 'The {field} field is invalid.';

    // --------------------------------------------------------------------------

    public function getName(): string
    {
        return static::NAME;
    }

    public function getAliases(): array
    {
        return static::ALIASES;
    }

    public function runsOnEmpty(): bool
    {
        return false;
    }

    public function acceptsArrays(): bool
    {
        return false;
    }

    public function getDefaultMessage(): string
    {
        return static::DEFAULT_MESSAGE;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the value as a string, or null when it cannot be represented as one
     * (arrays, objects); string rules fail on null.
     */
    protected function asString(mixed $mValue): ?string
    {
        if (is_string($mValue)) {
            return $mValue;
        } elseif (is_int($mValue) || is_float($mValue)) {
            return (string) $mValue;
        } elseif (is_bool($mValue)) {
            return $mValue ? '1' : '0';
        } elseif ($mValue instanceof \Stringable) {
            return (string) $mValue;
        }
        return null;
    }
}
