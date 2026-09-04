<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `alpha_numeric`
 */
class AlphaNumeric extends AbstractRule
{
    public const NAME            = 'alpha_numeric';
    public const DEFAULT_MESSAGE = 'The {field} field may only contain alpha-numeric characters.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        return $sValue !== null && ctype_alnum($sValue);
    }
}
