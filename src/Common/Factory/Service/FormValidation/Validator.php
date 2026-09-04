<?php

namespace Nails\Common\Factory\Service\FormValidation;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ValidationException;
use Nails\Common\Factory\Model\Field as ModelField;
use Nails\Common\Interfaces\Validation\Rule;
use Nails\Common\Model\Base;
use Nails\Common\Service\FormValidation;
use Nails\Common\Validation\Context;
use Nails\Common\Validation\Exception\UnknownRuleException;
use Nails\Common\Validation\Field;
use Nails\Common\Validation\MessageStyle;
use Nails\Common\Validation\Result;
use Nails\Common\Validation\RuleSet;
use Nails\Factory;

/**
 * Class Validator
 *
 * Validates a data set against a set of rules. Rules are given per field, as a
 * pipe-separated string or an array of: rule names (`required`, `max_length[5]`),
 * Rule class names, Rule instances, or closures. A closure receives
 * `(mixed $mValue, Context $oContext)` and fails by throwing a ValidationException.
 *
 * @package Nails\Common\Factory\Service\FormValidation
 */
class Validator
{
    /**
     * The default message when validation fails and no language line exists
     */
    const ERRORS_MESSAGE = 'Please check highlighted fields.';

    // --------------------------------------------------------------------------

    /**
     * The rules array, key => value format, where value is an array or pipe separated strings
     *
     * @var array<string, string|array>
     */
    protected $aRules = [];

    /**
     * Messages to override the default error messages, rule => message
     *
     * @var string[]
     */
    protected $aMessages = [];

    /**
     * The data to validate
     *
     * @var array
     */
    protected $aData = [];

    /**
     * Field labels, field => label
     *
     * @var string[]
     */
    protected $aLabels = [];

    /**
     * Per-field message overrides, field => [rule => message]
     *
     * @var array<string, array<string, string>>
     */
    protected $aFieldMessages = [];

    /**
     * The result of the last run
     *
     * @var Result|null
     */
    protected $oResult = null;

    // --------------------------------------------------------------------------

    /**
     * Validator constructor.
     *
     * @param array    $aRules    The rules array, key => value format, where value is an array or pipe separated strings
     * @param string[] $aMessages Messages to override the default error messages
     * @param array    $aData     The data to validate
     */
    public function __construct(array $aRules = [], array $aMessages = [], array $aData = [])
    {
        $this
            ->setRules($aRules)
            ->setMessages($aMessages)
            ->setData($aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the rules array
     *
     * @param array $aRules The rules array, key => value format, where value is an array or pipe separated strings
     *
     * @return $this
     */
    public function setRules(array $aRules): static
    {
        $this->aRules = $aRules;
        return $this;
    }

    /**
     * Get the rules array
     *
     * @return array
     */
    public function getRules(): array
    {
        return $this->aRules;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the messages array
     *
     * @param string[] $aMessages Messages to override the default error messages, rule => message
     *
     * @return $this
     */
    public function setMessages(array $aMessages): static
    {
        $this->aMessages = $aMessages;
        return $this;
    }

    /**
     * Get the messages array
     *
     * @return string[]
     */
    public function getMessages(): array
    {
        return $this->aMessages;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the data array
     *
     * @param array $aData The data to validate
     *
     * @return $this
     */
    public function setData(array $aData): static
    {
        $this->aData = $aData;
        return $this;
    }

    /**
     * Get the data array (as supplied; see getValidatedData() for the processed values)
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->aData;
    }

    // --------------------------------------------------------------------------

    /**
     * Set field labels (used in `{field}` message placeholders)
     *
     * @param string[] $aLabels field => label
     *
     * @return $this
     */
    public function setLabels(array $aLabels): static
    {
        $this->aLabels = $aLabels;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getLabels(): array
    {
        return $this->aLabels;
    }

    // --------------------------------------------------------------------------

    /**
     * Set per-field message overrides
     *
     * @param array<string, array<string, string>> $aFieldMessages field => [rule => message]
     *
     * @return $this
     */
    public function setFieldMessages(array $aFieldMessages): static
    {
        $this->aFieldMessages = $aFieldMessages;
        return $this;
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function getFieldMessages(): array
    {
        return $this->aFieldMessages;
    }

    // --------------------------------------------------------------------------

    /**
     * The result of the last run, if any
     */
    public function getResult(): ?Result
    {
        return $this->oResult;
    }

    /**
     * The data after validation, with any rule mutations (e.g. `trim`) applied
     *
     * @return array
     */
    public function getValidatedData(): array
    {
        return $this->oResult?->getData() ?? $this->aData;
    }

    /**
     * Return any errors from the last run, field => message
     *
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->oResult?->getErrors() ?? [];
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles the rules into a RuleSet
     *
     * @return RuleSet
     */
    public function buildRuleSet(): RuleSet
    {
        $oRuleSet = new RuleSet();

        foreach ($this->getRules() as $sField => $mRules) {
            $oRuleSet->add(new Field(
                (string) $sField,
                (string) ($this->aLabels[$sField] ?? ''),
                FormValidation::splitRules($mRules),
                $this->aFieldMessages[$sField] ?? []
            ));
        }

        return $oRuleSet;
    }

    // --------------------------------------------------------------------------

    /**
     * Perform the validation
     *
     * @param array|null $aData The data to validate (overrides any data already set)
     *
     * @return $this
     * @throws ValidationException
     * @throws FactoryException
     * @throws UnknownRuleException
     */
    public function run(?array $aData = null): static
    {
        if (empty($this->getRules())) {
            return $this;
        } elseif ($aData !== null) {
            $this->setData($aData);
        }

        /** @var FormValidation $oFormValidation */
        $oFormValidation = Factory::service('FormValidation');

        $this->oResult = $oFormValidation
            ->getEngine()
            ->run(
                $this->buildRuleSet(),
                $this->getData(),
                MessageStyle::PLAIN,
                $this->getMessages()
            );

        //  Make the result available to the set_value()/form_error() view helpers
        $oFormValidation->publish($this->oResult);

        if ($this->oResult->failed()) {
            $sMessage   = $oFormValidation->getTranslation()->line('fv_there_were_errors');
            $oException = new ValidationException($sMessage ?: static::ERRORS_MESSAGE);
            $oException->setData($this->oResult->getErrors());
            throw $oException;
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the validation rules from a model's describeFields method
     *
     * @param string|Base $mModel    The model's name or instance
     * @param string      $sProvider The model's provider (if $mModel is a string)
     *
     * @return $this
     * @throws FactoryException
     * @throws ValidationException
     */
    public function setRulesFromModel($mModel, $sProvider = 'app'): static
    {
        if (is_string($mModel)) {
            $oModel = Factory::model($mModel, $sProvider);
        } elseif ($mModel instanceof Base) {
            $oModel = $mModel;
        } else {
            throw new ValidationException(
                sprintf(
                    'Expected string or %s, got %s',
                    Base::class,
                    is_object($mModel) ? get_class($mModel) : gettype($mModel)
                )
            );
        }

        $aRules = [];

        /** @var ModelField $oField */
        foreach ($oModel->describeFields() as $oField) {
            $aRules[$oField->key] = $oField->validation;
        }

        $this->aRules = array_merge(
            $this->aRules,
            $aRules
        );

        return $this;
    }
}
