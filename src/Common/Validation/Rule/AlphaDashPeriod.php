<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `alpha_dash_period`
 */
class AlphaDashPeriod extends AbstractRule
{
    public const NAME            = 'alpha_dash_period';
    public const DEFAULT_MESSAGE = 'The {field} field may only contain alpha-numeric characters, underscores, periods, and dashes.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        return $sValue !== null && (bool) preg_match('/^[a-z0-9_.-]+$/i', $sValue);
    }
}
