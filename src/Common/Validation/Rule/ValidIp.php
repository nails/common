<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `valid_ip`
 */
class ValidIp extends AbstractRule
{
    public const NAME            = 'valid_ip';
    public const DEFAULT_MESSAGE = 'The {field} field must contain a valid IP.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        $iFlags = match (strtolower((string) $oContext->getParam())) {
            'ipv4' => FILTER_FLAG_IPV4,
            'ipv6' => FILTER_FLAG_IPV6,
            default => 0,
        };
        return $sValue !== null && filter_var($sValue, FILTER_VALIDATE_IP, $iFlags) !== false;
    }
}
