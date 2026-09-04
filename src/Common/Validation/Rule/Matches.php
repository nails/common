<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `matches`
 */
class Matches extends AbstractRule
{
    public const NAME            = 'matches';
    public const DEFAULT_MESSAGE = 'The {field} field does not match the {param} field.';

    public function runsOnEmpty(): bool
    {
        return true;
    }

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sOther = (string) $oContext->getParam();
        return $oContext->hasField($sOther) && $oContext->getValue($sOther) !== null
            ? $mValue === $oContext->getValue($sOther)
            : false;
    }
}
