<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `is_natural_no_zero`
 */
class IsNaturalNoZero extends AbstractRule
{
    public const NAME            = 'is_natural_no_zero';
    public const DEFAULT_MESSAGE = 'The {field} field must be a natural number greater than zero (1, 2, 3, etc).';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        return $sValue !== null && ctype_digit($sValue) && (int) $sValue !== 0;
    }
}
