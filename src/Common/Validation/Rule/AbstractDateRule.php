<?php

namespace Nails\Common\Validation\Rule;

use Closure;
use DateTime;
use Nails\Common\Validation\AbstractRule;
use Nails\Factory;
use Throwable;

/**
 * Base for the date/time rules: parses values with a format and knows "now"
 * (injectable for tests; defaults to the app's DateTime factory).
 */
abstract class AbstractDateRule extends AbstractRule
{
    public const DEFAULT_FORMAT = 'Y-m-d';

    /**
     * @param Closure|null $cNow Returns a DateTime representing now
     */
    public function __construct(private readonly ?Closure $cNow = null)
    {
    }

    protected function now(): DateTime
    {
        $oNow = $this->cNow !== null ? ($this->cNow)() : Factory::factory('DateTime');
        return clone $oNow;
    }

    protected function parse(string $sValue, string $sFormat): ?DateTime
    {
        try {
            $oDate = DateTime::createFromFormat($sFormat, $sValue);
            return $oDate instanceof DateTime ? $oDate : null;
        } catch (Throwable) {
            return null;
        }
    }
}
