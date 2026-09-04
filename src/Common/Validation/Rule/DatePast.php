<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\Context;

/**
 * Rule: `date_past[format]` — format optional, default `Y-m-d`
 */
class DatePast extends AbstractDateRule
{
    public const NAME            = 'date_past';
    public const DEFAULT_MESSAGE = 'The {field} field must be in the past.';
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
        if ($oDate === null) {
            return false;
        }
        $oNow = $this->now();
        $oNow->setTime(0, 0, 0);
        $oDate->setTime(0, 0, 0);
        return $oDate < $oNow;
    }
}
