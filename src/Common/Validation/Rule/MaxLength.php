<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `max_length`
 */
class MaxLength extends AbstractRule
{
    public const NAME            = 'max_length';
    public const DEFAULT_MESSAGE = 'The {field} field is too long, maximum length is {param} characters.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        return $sValue !== null && is_numeric($oContext->getParam()) && (int) $oContext->getParam() >= mb_strlen($sValue);
    }
}
