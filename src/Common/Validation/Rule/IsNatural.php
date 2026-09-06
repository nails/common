<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `is_natural`
 */
class IsNatural extends AbstractRule
{
    public const NAME            = 'is_natural';
    public const DEFAULT_MESSAGE = 'The {field} field must be a natural number (0, 1, 2, 3, etc).';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        return $sValue !== null && ctype_digit($sValue);
    }
}
