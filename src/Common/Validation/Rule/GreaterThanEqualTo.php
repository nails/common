<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `greater_than_equal_to`
 */
class GreaterThanEqualTo extends AbstractRule
{
    public const NAME            = 'greater_than_equal_to';
    public const DEFAULT_MESSAGE = 'The {field} field must be greater than or equal to {param}';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        return is_numeric($mValue) && is_numeric($oContext->getParam()) && $mValue >= $oContext->getParam();
    }
}
