<?php

namespace Nails\Common\Validation\Adapter;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Wraps a `callback_<method>` rule (a method on the calling object) with
 * CodeIgniter's semantics: false fails, a non-boolean return replaces the value.
 *
 * @deprecated Use a closure or a Rule class instead
 */
final class CallbackRule extends AbstractRule
{
    public const DEFAULT_MESSAGE = 'Field failed validation.';

    public function __construct(
        private readonly object $oTarget,
        private readonly string $sMethod,
    ) {
    }

    public function getName(): string
    {
        return $this->sMethod;
    }

    public function runsOnEmpty(): bool
    {
        return true;
    }

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $mResult = $this->oTarget->{$this->sMethod}($mValue, $oContext->getParam());

        if ($mResult === false) {
            return false;
        } elseif (!is_bool($mResult)) {
            $oContext->setValue($mResult);
        }

        return true;
    }
}
