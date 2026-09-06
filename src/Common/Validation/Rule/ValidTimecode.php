<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `validTimecode`
 */
class ValidTimecode extends AbstractRule
{
    public const NAME            = 'validTimecode';
    public const DEFAULT_MESSAGE = 'The {field} field must be a valid timecode (expected format: hh:mm:ss)';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        return $sValue !== null && (bool) preg_match('/^\d+\d:[0-5]\d:[0-5]\d$/', $sValue);
    }
}
