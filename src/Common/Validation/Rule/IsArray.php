<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `is_array`
 */
class IsArray extends AbstractRule
{
    public const NAME            = 'is_array';
    public const DEFAULT_MESSAGE = 'The {field} field must be an array.';

    public function acceptsArrays(): bool
    {
        return true;
    }

    public function apply(mixed $mValue, Context $oContext): bool
    {
        return is_array($mValue);
    }
}
