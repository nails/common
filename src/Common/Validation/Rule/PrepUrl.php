<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `prep_url`
 */
class PrepUrl extends AbstractRule
{
    public const NAME            = 'prep_url';
    public const DEFAULT_MESSAGE = 'The {field} field is invalid.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        if ($sValue !== null && $sValue !== '' && stripos($sValue, 'http://') !== 0 && stripos($sValue, 'https://') !== 0) {
            $oContext->setValue('http://' . $sValue);
        }
        return true;
    }
}
