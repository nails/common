<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `valid_postcode`
 */
class ValidPostcode extends AbstractRule
{
    public const NAME            = 'valid_postcode';
    public const DEFAULT_MESSAGE = 'The {field} field is not a valid UK postcode.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        $sPattern = '/^([Gg][Ii][Rr] 0[Aa]{2})|((([A-Za-z][0-9]{1,2})|(([A-Za-z][A-Ha-hJ-Yj-y][0-9]{1,2})|(([A-Za-z][0-9][A-Za-z])|([A-Za-z][A-Ha-hJ-Yj-y][0-9]?[A-Za-z])))) {0,1}[0-9][A-Za-z]{2})$/';
        return $sValue !== null && (bool) preg_match($sPattern, strtoupper($sValue));
    }
}
