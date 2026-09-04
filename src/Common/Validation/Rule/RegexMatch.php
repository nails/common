<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `regex_match`
 */
class RegexMatch extends AbstractRule
{
    public const NAME            = 'regex_match';
    public const DEFAULT_MESSAGE = 'The {field} field is not in the correct format.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        return $sValue !== null && (bool) preg_match((string) $oContext->getParam(), $sValue);
    }
}
