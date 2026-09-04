<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `prep_for_form` — escapes quotes and angle brackets so the value can be re-rendered in a form
 */
class PrepForForm extends AbstractRule
{
    public const NAME            = 'prep_for_form';
    public const DEFAULT_MESSAGE = 'The {field} field is invalid.';

    public function acceptsArrays(): bool
    {
        return true;
    }

    public function apply(mixed $mValue, Context $oContext): bool
    {
        if (!empty($mValue)) {
            $oContext->setValue($this->prep($mValue));
        }
        return true;
    }

    private function prep(mixed $mValue): mixed
    {
        if (is_array($mValue)) {
            return array_map([$this, 'prep'], $mValue);
        } elseif (is_string($mValue)) {
            return str_replace(["'", '"', '<', '>'], ['&#39;', '&quot;', '&lt;', '&gt;'], stripslashes($mValue));
        }
        return $mValue;
    }
}
