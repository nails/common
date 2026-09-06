<?php

namespace Nails\Common\Validation;

/**
 * Class Field
 *
 * Declares a field to be validated: its name (which may be a bracketed path
 * such as `question[0][answer]` or `tags[]`), label, rules and any per-rule
 * message overrides.
 *
 * @package Nails\Common\Validation
 */
final class Field
{
    /** @var string[] */
    private array $aKeys   = [];
    private bool  $bIsArray = false;

    // --------------------------------------------------------------------------

    /**
     * @param string                $name     The field name, e.g. `email` or `question[0][answer]`
     * @param string                $label    The human friendly label ('' = use the name)
     * @param array<string|\Closure|\Nails\Common\Interfaces\Validation\Rule> $rules
     * @param array<string, string> $messages Per-rule message overrides for this field
     */
    public function __construct(
        public readonly string $name,
        public readonly string $label,
        public readonly array $rules,
        public readonly array $messages = [],
    ) {
        if (preg_match_all('/\[(.*?)\]/', $name, $aMatches)) {
            $this->bIsArray = true;
            sscanf($name, '%[^[][', $sBase);
            $this->aKeys = [(string) $sBase];
            foreach ($aMatches[1] as $sKey) {
                if ($sKey !== '') {
                    $this->aKeys[] = $sKey;
                }
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Whether the field name is a bracketed path
     */
    public function isArray(): bool
    {
        return $this->bIsArray;
    }

    // --------------------------------------------------------------------------

    /**
     * The path segments of a bracketed name; empty for plain names
     *
     * @return string[]
     */
    public function getKeys(): array
    {
        return $this->aKeys;
    }

    // --------------------------------------------------------------------------

    /**
     * The label, defaulting to the field name
     */
    public function getLabel(): string
    {
        return $this->label !== '' ? $this->label : $this->name;
    }

    // --------------------------------------------------------------------------

    /**
     * Reads this field's value out of a data set (ports CI's _reduce_array)
     *
     * @param array $aData The data set
     *
     * @return mixed
     */
    public function extract(array $aData): mixed
    {
        if (!$this->bIsArray) {
            return $aData[$this->name] ?? null;
        }

        $mCursor = $aData;
        foreach ($this->aKeys as $sKey) {
            if (!is_array($mCursor) || !isset($mCursor[$sKey])) {
                return null;
            }
            $mCursor = $mCursor[$sKey];
        }

        return $mCursor === '' ? null : $mCursor;
    }

    // --------------------------------------------------------------------------

    /**
     * Writes a value for this field into a data set (ports CI's _reset_data_array)
     *
     * @param array $aData  The data set (modified in place)
     * @param mixed $mValue The value to write
     */
    public function inject(array &$aData, mixed $mValue): void
    {
        if (!$this->bIsArray) {
            if (array_key_exists($this->name, $aData)) {
                $aData[$this->name] = $mValue;
            }
            return;
        }

        $aCursor = &$aData;
        foreach ($this->aKeys as $sKey) {
            if (!is_array($aCursor)) {
                $aCursor = [];
            }
            if (!array_key_exists($sKey, $aCursor)) {
                $aCursor[$sKey] = null;
            }
            $aCursor = &$aCursor[$sKey];
        }
        $aCursor = $mValue;
    }
}
