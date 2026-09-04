<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `alpha_numeric_spaces`
 */
class AlphaNumericSpaces extends AbstractRule
{
    public const NAME            = 'alpha_numeric_spaces';
    public const DEFAULT_MESSAGE = 'The {field} field may only contain alpha-numeric characters and spaces.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        return $sValue !== null && (bool) preg_match('/^[A-Z0-9 ]+$/i', $sValue);
    }
}
