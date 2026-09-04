<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\Context;

/**
 * Rule: `datetime_after[other_field.format]` — the datetime must be after the datetime in `other_field` (format optional, default `Y-m-d H:i:s`)
 */
class DatetimeAfter extends AbstractDateRule
{
    public const NAME            = 'datetime_after';
    public const DEFAULT_MESSAGE = 'The {field} field must be after the {param} field.';
    public const DEFAULT_FORMAT  = 'Y-m-d H:i:s';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        //  If blank, then assume the value is not required
        $sValue = $this->asString($mValue);
        if (empty($sValue)) {
            return true;
        }

        $aParams = $oContext->getParams('.');
        $sField  = $aParams[0] ?? '';
        $sFormat = !empty($aParams[1]) ? $aParams[1] : static::DEFAULT_FORMAT;

        if ($sField === '') {
            return false;
        }

        //  If the other field is blank then bail out
        $sOther = $oContext->getValue($sField);
        if (empty($sOther) || !is_scalar($sOther)) {
            return false;
        }

        $oDate  = $this->parse($sValue, $sFormat);
        $oOther = $this->parse((string) $sOther, $sFormat);

        if ($oDate === null || $oOther === null) {
            return false;
        }

        return $oDate > $oOther;
    }
}
