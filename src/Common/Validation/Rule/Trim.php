<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `trim`
 */
class Trim extends AbstractRule
{
    public const NAME            = 'trim';
    public const DEFAULT_MESSAGE = 'The {field} field is invalid.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        if (is_string($mValue)) {
            $oContext->setValue(trim($mValue));
        }
        return true;
    }
}
