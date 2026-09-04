<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `encode_php_tags`
 */
class EncodePhpTags extends AbstractRule
{
    public const NAME            = 'encode_php_tags';
    public const DEFAULT_MESSAGE = 'The {field} field is invalid.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        if (is_string($mValue)) {
            $oContext->setValue(str_replace(['<?', '?>'], ['&lt;?', '?&gt;'], $mValue));
        }
        return true;
    }
}
