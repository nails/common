<?php

namespace Nails\Common\Validation\Adapter;

use Closure;
use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Wraps a closure as a rule. The closure receives `(mixed $mValue, Context $oContext)`;
 * it fails by throwing a ValidationException (whose message is used verbatim) or by
 * returning false. Any other return value passes. Closures always run, even for
 * empty values, so they should guard themselves.
 */
final class ClosureRule extends AbstractRule
{
    public const NAME            = 'closure';
    public const DEFAULT_MESSAGE = 'Field failed validation.';

    public function __construct(private readonly Closure $cClosure)
    {
    }

    public function runsOnEmpty(): bool
    {
        return true;
    }

    public function apply(mixed $mValue, Context $oContext): bool
    {
        return ($this->cClosure)($mValue, $oContext) !== false;
    }
}
