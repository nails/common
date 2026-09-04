<?php

namespace Nails\Common\Validation\Adapter;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;
use Throwable;

/**
 * Wraps a native PHP function as a rule (e.g. `strtolower`, `htmlspecialchars`),
 * with CodeIgniter's semantics: false fails, a non-boolean return replaces the value.
 * Errors raised by the function (e.g. a TypeError) are treated as a failure.
 */
final class NativeFunctionRule extends AbstractRule
{
    public function __construct(private readonly string $sFunction)
    {
    }

    public function getName(): string
    {
        return $this->sFunction;
    }

    public function apply(mixed $mValue, Context $oContext): bool
    {
        try {
            $mResult = $oContext->getParam() !== null
                ? ($this->sFunction)($mValue, $oContext->getParam())
                : ($this->sFunction)($mValue);
        } catch (Throwable) {
            return false;
        }

        if ($mResult === false) {
            return false;
        } elseif (!is_bool($mResult)) {
            $oContext->setValue($mResult);
        }

        return true;
    }
}
