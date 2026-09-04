<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `maxWords`
 */
class MaxWords extends AbstractRule
{
    public const NAME            = 'maxWords';
    public const DEFAULT_MESSAGE = 'The {field} field is too long, maximum {param} words.';
    public const ALIASES         = ['max_words'];

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        return $sValue !== null && str_word_count($sValue) <= (int) $oContext->getParam();
    }
}
