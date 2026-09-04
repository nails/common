<?php

namespace Nails\Common\Validation;

/**
 * Class Context
 *
 * What a Rule (or closure) sees while validating one value: the field, the
 * rule parameter, and the rest of the data set (so cross-field rules never
 * need to reach for $_POST).
 *
 * @package Nails\Common\Validation
 */
final class Context
{
    private bool  $bMutated = false;
    private mixed $mMutated = null;

    // --------------------------------------------------------------------------

    public function __construct(
        private readonly State $oState,
        private readonly Field $oField,
        private readonly ?string $sParam,
        private readonly int|string|null $mIndex = null,
    ) {
    }

    // --------------------------------------------------------------------------

    /**
     * The declared field name, e.g. `email` or `question[0][answer]`
     */
    public function getField(): string
    {
        return $this->oField->name;
    }

    /**
     * The field's label (falls back to the field name)
     */
    public function getLabel(): string
    {
        return $this->oField->getLabel();
    }

    /**
     * The raw, unsplit rule parameter (the text inside `[...]`), or null
     */
    public function getParam(): ?string
    {
        return $this->sParam;
    }

    /**
     * The rule parameter split on a delimiter; an empty array when there is no parameter
     *
     * @return string[]
     */
    public function getParams(string $sDelimiter = '.'): array
    {
        return $this->sParam === null || $this->sParam === '' ? [] : explode($sDelimiter, $this->sParam);
    }

    /**
     * When validating one element of an array value, that element's key
     */
    public function getIndex(): int|string|null
    {
        return $this->mIndex;
    }

    /**
     * The whole data set, with any mutations applied so far
     */
    public function getData(): array
    {
        return $this->oState->getData();
    }

    /**
     * The current value of another field (declared name or bracketed path); null if absent
     */
    public function getValue(string $sField): mixed
    {
        return $this->oState->getValue($sField);
    }

    /**
     * Whether another field is declared in the rule set
     */
    public function hasField(string $sField): bool
    {
        return $this->oState->getRuleSet()->has($sField);
    }

    /**
     * The label of another declared field, or null
     */
    public function getLabelFor(string $sField): ?string
    {
        return $this->oState->getRuleSet()->get($sField)?->getLabel();
    }

    public function getMessageStyle(): MessageStyle
    {
        return $this->oState->getMessageStyle();
    }

    /**
     * Looks up a language line; false if it doesn't exist
     */
    public function translate(string $sKey, array|string|null $mParams = null): string|false
    {
        return $this->oState->getTranslation()->line($sKey, $mParams);
    }

    // --------------------------------------------------------------------------

    /**
     * Replaces the value being validated (e.g. after trimming)
     */
    public function setValue(mixed $mValue): void
    {
        $this->bMutated = true;
        $this->mMutated = $mValue;
    }

    public function hasMutation(): bool
    {
        return $this->bMutated;
    }

    public function getMutatedValue(): mixed
    {
        return $this->mMutated;
    }
}
