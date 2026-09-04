<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\Context;

/**
 * Rule: `date_today[format]` — format optional, default `Y-m-d`
 */
class DateToday extends AbstractDateRule
{
    public const NAME            = 'date_today';
    public const DEFAULT_MESSAGE = 'The {field} field must be today\'s date.';
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
        return $oDate->format('Y-m-d') === $this->now()->format('Y-m-d');
    }
}
