<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `valid_base64`
 */
class ValidBase64 extends AbstractRule
{
    public const NAME            = 'valid_base64';
    public const DEFAULT_MESSAGE = 'The {field} field must contain a valid Base64 string.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        return $sValue !== null && base64_encode((string) base64_decode($sValue, true)) === $sValue;
    }
}
