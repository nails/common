<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `greater_than`
 */
class GreaterThan extends AbstractRule
{
    public const NAME            = 'greater_than';
    public const DEFAULT_MESSAGE = 'The {field} field must be greater than {param}';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        return is_numeric($mValue) && is_numeric($oContext->getParam()) && $mValue > $oContext->getParam();
    }
}
