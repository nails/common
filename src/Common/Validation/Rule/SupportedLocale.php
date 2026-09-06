<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Service\Locale;
use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;
use Nails\Factory;

/**
 * Rule: `supportedLocale` — the value must be one of the app's supported locales
 */
class SupportedLocale extends AbstractRule
{
    public const NAME            = 'supportedLocale';
    public const DEFAULT_MESSAGE = 'The {field} field is not a supported locale';

    public function __construct(private ?Locale $oLocale = null)
    {
    }

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $oLocale = $this->oLocale ?? Factory::service('Locale');
        return in_array($mValue, $oLocale->getSupportedLocales());
    }
}
