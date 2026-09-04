<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `is`
 */
class Is extends AbstractRule
{
    public const NAME            = 'is';
    public const DEFAULT_MESSAGE = 'The {field} field must be exactly "{param}"';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        return $mValue === $oContext->getParam();
    }
}
