<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `decimal`
 */
class Decimal extends AbstractRule
{
    public const NAME            = 'decimal';
    public const DEFAULT_MESSAGE = 'The {field} field must contain a decimal number.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        return $sValue !== null && (bool) preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $sValue);
    }
}
