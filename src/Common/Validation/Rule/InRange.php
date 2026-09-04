<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `in_range`
 */
class InRange extends AbstractRule
{
    public const NAME            = 'in_range';
    public const DEFAULT_MESSAGE = 'The {field} field must be within the range {param}.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $aRange = $oContext->getParams('-');
        if (!isset($aRange[0], $aRange[1])) {
            return true;
        }
        return (float) $mValue >= (float) $aRange[0] && (float) $mValue <= (float) $aRange[1];
    }
}
