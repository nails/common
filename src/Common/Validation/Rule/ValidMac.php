<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `valid_mac`
 */
class ValidMac extends AbstractRule
{
    public const NAME            = 'valid_mac';
    public const DEFAULT_MESSAGE = 'The {field} field must contain a valid MAC.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        return $sValue !== null && filter_var($sValue, FILTER_VALIDATE_MAC) !== false;
    }
}
