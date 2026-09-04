<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `integer`
 */
class Integer extends AbstractRule
{
    public const NAME            = 'integer';
    public const DEFAULT_MESSAGE = 'The {field} field must be an integer.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        return $sValue !== null && (bool) preg_match('/^[\-+]?[0-9]+$/', $sValue);
    }
}
