<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `alpha`
 */
class Alpha extends AbstractRule
{
    public const NAME            = 'alpha';
    public const DEFAULT_MESSAGE = 'The {field} field may only contain alphabetical characters.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        return $sValue !== null && ctype_alpha($sValue);
    }
}
