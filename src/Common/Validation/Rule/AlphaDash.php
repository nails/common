<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `alpha_dash`
 */
class AlphaDash extends AbstractRule
{
    public const NAME            = 'alpha_dash';
    public const DEFAULT_MESSAGE = 'The {field} field may only contain alpha-numeric characters, underscores, and dashes.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        return $sValue !== null && (bool) preg_match('/^[a-z0-9_-]+$/i', $sValue);
    }
}
