<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `valid_email`
 */
class ValidEmail extends AbstractRule
{
    public const NAME            = 'valid_email';
    public const DEFAULT_MESSAGE = 'The {field} field must be a valid email.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        return $sValue !== null && (bool) filter_var($sValue, FILTER_VALIDATE_EMAIL);
    }
}
