<?php

namespace Nails\Common\Validation;

use Nails\Common\Service\Translation;

/**
 * Class State
 *
 * The mutable working state of a single Engine run: the rule set, the data
 * (with mutations applied as they happen) and the per-field values.
 *
 * @package Nails\Common\Validation
 * @internal
 */
final class State
{
    /** @var array<string, mixed> field name => current value */
    private array $aValues = [];

    // --------------------------------------------------------------------------

    public function __construct(
        private readonly RuleSet $oRuleSet,
        private array $aData,
        private readonly MessageStyle $eStyle,
        private readonly Translation $oTranslation,
    ) {
        foreach ($oRuleSet as $oField) {
            $this->aValues[$oField->name] = $oField->extract($aData);
        }
    }

    // --------------------------------------------------------------------------

    public function getRuleSet(): RuleSet
    {
        return $this->oRuleSet;
    }

    public function getMessageStyle(): MessageStyle
    {
        return $this->eStyle;
    }

    public function getTranslation(): Translation
    {
        return $this->oTranslation;
    }

    public function getData(): array
    {
        return $this->aData;
    }

    /**
     * @return array<string, mixed>
     */
    public function getValues(): array
    {
        return $this->aValues;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the current value of a field, by declared name or bracketed path
     */
    public function getValue(string $sField): mixed
    {
        if (array_key_exists($sField, $this->aValues)) {
            return $this->aValues[$sField];
        }

        return (new Field($sField, '', []))->extract($this->aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Records a (possibly mutated) value for a field, or for one element of an
     * array value, and writes it through to the data set
     */
    public function setValue(Field $oField, mixed $mValue, int|string|null $mIndex = null): void
    {
        if ($mIndex === null) {
            $this->aValues[$oField->name] = $mValue;
        } else {
            if (!is_array($this->aValues[$oField->name] ?? null)) {
                $this->aValues[$oField->name] = [];
            }
            $this->aValues[$oField->name][$mIndex] = $mValue;
        }

        $oField->inject($this->aData, $this->aValues[$oField->name]);
    }
}
