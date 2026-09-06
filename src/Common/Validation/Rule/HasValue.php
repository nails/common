<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `isset`
 */
class HasValue extends AbstractRule
{
    public const NAME            = 'isset';
    public const DEFAULT_MESSAGE = 'The {field} field must have a value.';

    public function runsOnEmpty(): bool
    {
        return true;
    }

    public function apply(mixed $mValue, Context $oContext): bool
    {
        return $mValue !== null;
    }
}
