<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\Context;

/**
 * Rule: `valid_date[format]` — format optional, default `Y-m-d`
 */
class ValidDate extends AbstractDateRule
{
    public const NAME            = 'valid_date';
    public const DEFAULT_MESSAGE = 'The {field} field is not a valid date.';
    public const DEFAULT_FORMAT  = 'Y-m-d';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        //  If blank, then assume the value is not required
        $sValue = $this->asString($mValue);
        if (empty($sValue)) {
            return true;
        }

        $sFormat = $oContext->getParam() ?: static::DEFAULT_FORMAT;
        $oDate = $this->parse($sValue, $sFormat);
        return $oDate !== null && $oDate->format($sFormat) == $sValue;
    }
}
