<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\Context;

/**
 * Rule: `date_before[other_field.format]` — the date must be before the date in `other_field` (format optional, default `Y-m-d`)
 */
class DateBefore extends AbstractDateRule
{
    public const NAME            = 'date_before';
    public const DEFAULT_MESSAGE = 'The {field} field must be before the {param} field.';
    public const DEFAULT_FORMAT  = 'Y-m-d';

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

        $oDate->setTime(0, 0, 0);
        $oOther->setTime(0, 0, 0);

        return $oDate < $oOther;
    }
}
