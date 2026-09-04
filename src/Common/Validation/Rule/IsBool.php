<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `is_bool`
 */
class IsBool extends AbstractRule
{
    public const NAME            = 'is_bool';
    public const DEFAULT_MESSAGE = 'The {field} field must be a boolean';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        return is_bool($mValue) || $mValue === '1' || $mValue === '0' || $mValue === 1 || $mValue === 0;
    }
}
