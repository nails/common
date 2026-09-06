<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `valid_url`
 */
class ValidUrl extends AbstractRule
{
    public const NAME            = 'valid_url';
    public const DEFAULT_MESSAGE = 'The {field} field must contain a valid URL.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);

        if ($sValue === null || $sValue === '') {
            return false;

        } elseif (preg_match('/^(?:([^:]*)\:)?\/\/(.+)$/', $sValue, $aMatches)) {

            if (empty($aMatches[2])) {
                return false;
            } elseif (!in_array(strtolower($aMatches[1]), ['http', 'https'], true)) {
                return false;
            }

            $sValue = $aMatches[2];
        }

        //  FILTER_VALIDATE_URL doesn't reject digit-only names
        if (ctype_digit($sValue)) {
            return false;
        }

        return filter_var('http://' . $sValue, FILTER_VALIDATE_URL) !== false;
    }
}
