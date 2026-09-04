<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `min_length`
 */
class MinLength extends AbstractRule
{
    public const NAME            = 'min_length';
    public const DEFAULT_MESSAGE = 'The {field} field is too short, minimum length is {param} characters.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        return $sValue !== null && is_numeric($oContext->getParam()) && (int) $oContext->getParam() <= mb_strlen($sValue);
    }
}
