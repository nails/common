<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\Context;

/**
 * Rule: `valid_datetime[format]` — format optional, default `Y-m-d H:i:s`
 */
class ValidDatetime extends AbstractDateRule
{
    public const NAME            = 'valid_datetime';
    public const DEFAULT_MESSAGE = 'The {field} field is not a valid datetime.';
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
        return $oDate !== null && $oDate->format($sFormat) == $sValue;
    }
}
