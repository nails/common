<?php

namespace Nails\Common\Factory\Model;

use Nails\Common\Service\FormValidation;

/**
 * Class Field
 *
 * @package Nails\Common\Factory\Model
 */
class Field
{
    /**
     * The field's key
     *
     * @var string
     */
    public $key = '';

    /**
     * The field's label
     *
     * @var string
     */
    public $label = '';

    /**
     * The field's type
     *
     * @var string
     */
    public $type = 'text';

    /**
     * Whether the field can be null
     *
     * @var bool
     */
    public $allow_null = true;

    /**
     * The field's validation rules
     *
     * @var array
     */
    public $validation = [];

    /**
     * The field's default value
     *
     * @var mixed
     */
    public $default;

    /**
     * The field's options (applicable to dropdowns only)
     *
     * @var string[string|int]
     */
    public $options = [];

    /**
     * The field's maximum length
     *
     * @var int|null
     */
    public $max_length;

    /**
     * The field's class
     *
     * @var string
     */
    public $class = '';

    /**
     * The field's info
     *
     * @var string
     */
    public $info = '';

    /**
     * The field's info classes
     *
     * @var string
     */
    public $info_class = '';

    /**
     * The field's fieldset
     *
     * @var string
     */
    public $fieldset = 'Details';

    /**
     * The field's data
     *
     * @var array
     */
    public $data = [];

    /**
     * Whether the field is requird
     *
     * @var bool
     */
    public $required = false;

    /**
     * Whether the field is read-only
     *
     * @var bool
     */
    public $readonly = false;

    /**
     * The fields's placeholder
     *
     * @var string
     */
    public $placeholder = '';

    /**
     * The field's tip
     *
     * @var string
     */
    public $tip = '';

    // --------------------------------------------------------------------------

    /**
     * Get the key property
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the key property
     *
     * @param string $sKey The value to set
     *
     * @return $this
     */
    public function setKey(string $sKey): self
    {
        $this->key = $sKey;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the label property
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the label property
     *
     * @param string $sLabel The value to set
     *
     * @return $this
     */
    public function setLabel(string $sLabel): self
    {
        $this->label = $sLabel;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the type property
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the type property
     *
     * @param string $sType The value to set
     *
     * @return $this
     */
    public function setType(string $sType): self
    {
        $this->type = $sType;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @return bool
     */
    public function isAllowNull(): bool
    {
        return $this->allow_null;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the allow_null property
     *
     * @param bool $bAllowNull
     *
     * @return $this
     */
    public function setAllowNull(bool $bAllowNull): self
    {
        $this->allow_null = $bAllowNull;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the validation property
     *
     * @return array
     */
    public function getValidation(): array
    {
        return array_unique($this->validation);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the validation property
     *
     * @param array $aValidation The rules to set
     *
     * @return $this
     */
    public function setValidation(array $aValidation): self
    {
        $this->validation = $aValidation;
        $this->checkIfFieldIsRequired();
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Adds a new rule to the stack
     *
     * @param string $sRule The value to set
     *
     * @return $this
     */
    public function addValidation(string $sRule): self
    {
        $this->validation[] = $sRule;
        $this->checkIfFieldIsRequired();
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets field as required if the validation determines it as so
     */
    private function checkIfFieldIsRequired()
    {
        $this->setIsRequired(in_array(FormValidation::RULE_REQUIRED, $this->validation));
    }

    // --------------------------------------------------------------------------

    /**
     * Get the default property
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the default property
     *
     * @param mixed $mDefault The value to set
     *
     * @return $this
     */
    public function setDefault($mDefault): self
    {
        $this->default = $mDefault;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the options property
     *
     * @return string[string|int]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the options property
     *
     * @param string[string|int] $aOptions The options to set
     *
     * @return $this
     */
    public function setOptions(array $aOptions): self
    {
        $this->options = $aOptions;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the max_length property
     *
     * @return int|null
     */
    public function getMaxLength(): ?int
    {
        return $this->max_length;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the max_length property
     *
     * @param int|null $iMaxLength
     *
     * @return $this
     */
    public function setMaxLength(?int $iMaxLength): self
    {
        $this->max_length = $iMaxLength;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the class property
     *
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the class property
     *
     * @param string $sClass The value to set
     *
     * @return $this
     */
    public function setClass(string $sClass): self
    {
        $this->class = $sClass;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the info property
     *
     * @return string
     */
    public function getInfo(): string
    {
        return $this->info;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the info property
     *
     * @param string $sInfo The value to set
     *
     * @return $this
     */
    public function setInfo(string $sInfo): self
    {
        $this->info = $sInfo;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the info_class property
     *
     * @return string
     */
    public function getInfoClass(): string
    {
        return $this->info_class;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the info_class property
     *
     * @param string $sInfoClass The value to set
     *
     * @return $this
     */
    public function setInfoClass(string $sInfoClass): self
    {
        $this->info_class = $sInfoClass;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the fieldset property
     *
     * @return string
     */
    public function getFieldset(): string
    {
        return $this->fieldset;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the fieldset property
     *
     * @param string $Fieldset The value to set
     *
     * @return $this
     */
    public function setFieldset(string $Fieldset): self
    {
        $this->fieldset = $Fieldset;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the data property
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the data property
     *
     * @param array $aData The data to set
     *
     * @return $this
     */
    public function setData(array $aData): self
    {
        $this->data = $aData;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Adds a new data attribute to the stack
     *
     * @param string $sKey   The data key
     * @param mixed  $mValue The value
     *
     * @return $this
     */
    public function addData(string $sKey, $mValue): self
    {
        $this->data[$sKey] = $mValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets whether the field is required
     *
     * @param bool $bValue
     *
     * @return $this
     */
    public function setIsRequired(bool $bValue)
    {
        $this->required = $bValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether the field is requirdd
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets whether the field is read-only
     *
     * @param bool $bValue
     *
     * @return $this
     */
    public function setIsReadOnly(bool $bValue)
    {
        $this->readonly = $bValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether the field is read-only
     *
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return $this->readonly;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the field's placeholder
     *
     * @param string $sValue
     *
     * @return $this
     */
    public function setPlaceholder(string $sValue)
    {
        $this->placeholder = $sValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the field's placeholder
     *
     * @return string
     */
    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the field's tip
     *
     * @param string $sValue
     *
     * @return $this
     */
    public function setTip(string $sValue)
    {
        $this->tip = $sValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the field's tip
     *
     * @return string
     */
    public function getTip(): string
    {
        return $this->tip;
    }
}
