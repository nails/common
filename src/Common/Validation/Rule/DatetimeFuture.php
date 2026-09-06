<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\Context;

/**
 * Rule: `datetime_future[format]` — format optional, default `Y-m-d H:i:s`
 */
class DatetimeFuture extends AbstractDateRule
{
    public const NAME            = 'datetime_future';
    public const DEFAULT_MESSAGE = 'The {field} field must be in the future.';
    public const DEFAULT_FORMAT  = 'Y-m-d H:i:s';

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
        return $oDate > $oNow;
    }
}
