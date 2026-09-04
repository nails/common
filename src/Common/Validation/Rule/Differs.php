<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `differs`
 */
class Differs extends AbstractRule
{
    public const NAME            = 'differs';
    public const DEFAULT_MESSAGE = 'The {field} field must differ from the {param} field.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sOther = (string) $oContext->getParam();
        return !($oContext->hasField($sOther) && $oContext->getValue($sOther) === $mValue);
    }
}
