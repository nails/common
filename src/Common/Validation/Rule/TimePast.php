<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\Context;

/**
 * Rule: `time_past[format]` — format optional, default `H:i:s`
 */
class TimePast extends AbstractDateRule
{
    public const NAME            = 'time_past';
    public const DEFAULT_MESSAGE = 'The {field} field must be in the past.';
    public const DEFAULT_FORMAT  = 'H:i:s';

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
        return $oDate < $oNow;
    }
}
