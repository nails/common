<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `exact_length`
 */
class ExactLength extends AbstractRule
{
    public const NAME            = 'exact_length';
    public const DEFAULT_MESSAGE = 'The {field} field must be exactly {param} characters in length.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        return $sValue !== null && is_numeric($oContext->getParam()) && (int) $oContext->getParam() === mb_strlen($sValue);
    }
}
