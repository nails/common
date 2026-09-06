<?php

namespace Nails\Common\Validation;

/**
 * Class Result
 *
 * The outcome of an Engine run
 *
 * @package Nails\Common\Validation
 */
final class Result
{
    /**
     * @param RuleSet              $oRuleSet The rule set which was run
     * @param array<string,string> $aErrors  field => first error message
     * @param array<string,mixed>  $aValues  field => processed value
     * @param array                $aData    The full data set with mutations applied
     */
    public function __construct(
        private readonly RuleSet $oRuleSet,
        private readonly array $aErrors,
        private readonly array $aValues,
        private readonly array $aData,
    ) {
    }

    // --------------------------------------------------------------------------

    public function passed(): bool
    {
        return empty($this->aErrors);
    }

    public function failed(): bool
    {
        return !$this->passed();
    }

    /**
     * @return array<string,string>
     */
    public function getErrors(): array
    {
        return $this->aErrors;
    }

    public function getError(string $sField): ?string
    {
        return $this->aErrors[$sField] ?? null;
    }

    /**
     * Whether the field was declared in the rule set (regardless of outcome)
     */
    public function hasField(string $sField): bool
    {
        return $this->oRuleSet->has($sField);
    }

    public function getValue(string $sField, mixed $mDefault = null): mixed
    {
        return array_key_exists($sField, $this->aValues) ? $this->aValues[$sField] : $mDefault;
    }

    /**
     * @return array<string,mixed>
     */
    public function getValues(): array
    {
        return $this->aValues;
    }

    public function getData(): array
    {
        return $this->aData;
    }

    public function getRuleSet(): RuleSet
    {
        return $this->oRuleSet;
    }
}
