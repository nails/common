<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `numeric`
 */
class Numeric extends AbstractRule
{
    public const NAME            = 'numeric';
    public const DEFAULT_MESSAGE = 'The {field} field must be numeric.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        return $sValue !== null && (bool) preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $sValue);
    }
}
