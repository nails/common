<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `in_list`
 */
class InList extends AbstractRule
{
    public const NAME            = 'in_list';
    public const DEFAULT_MESSAGE = 'The {field} field must be one of: {param}.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        return in_array($mValue, $oContext->getParams(','), true);
    }
}
