<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `required`
 */
class Required extends AbstractRule
{
    public const NAME            = 'required';
    public const DEFAULT_MESSAGE = 'The {field} field is required.';

    public function runsOnEmpty(): bool
    {
        return true;
    }

    public function apply(mixed $mValue, Context $oContext): bool
    {
        return is_array($mValue) ? !empty($mValue) : trim((string) $mValue) !== '';
    }
}
