<?php

/**
 * The FormValidation service: builds validators, knows the available rules, and
 * offers a (deprecated) CodeIgniter-compatible surface for legacy call sites.
 *
 * @package                   Nails
 * @subpackage                common
 * @category                  Service
 * @author                    Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

use BadMethodCallException;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ValidationException;
use Nails\Common\Factory\Service\FormValidation\Validator;
use Nails\Common\Interfaces\Validation\Rule as RuleInterface;
use Nails\Common\Model\Base;
use Nails\Common\Validation\Engine;
use Nails\Common\Validation\Field;
use Nails\Common\Validation\MessageResolver;
use Nails\Common\Validation\MessageStyle;
use Nails\Common\Validation\Registry;
use Nails\Common\Validation\Result;
use Nails\Common\Validation\Rule;
use Nails\Common\Validation\RuleSet;
use Nails\Factory;

/**
 * Class FormValidation
 *
 * @package Nails\Common\Service
 *
 * @property array $validation_data The data set with set_data() (deprecated)
 */
class FormValidation
{
    /**
     * The following constants represent the rules bundled with Nails. Additional
     * rules are discovered from every component's `Validation\Rule` namespace.
     */
    const RULE_ALPHA                    = Rule\Alpha::NAME;
    const RULE_ALPHA_DASH               = Rule\AlphaDash::NAME;
    const RULE_ALPHA_DASH_PERIOD        = Rule\AlphaDashPeriod::NAME;
    const RULE_ALPHA_NUMERIC            = Rule\AlphaNumeric::NAME;
    const RULE_ALPHA_NUMERIC_SPACES     = Rule\AlphaNumericSpaces::NAME;
    const RULE_DATETIME_AFTER           = Rule\DatetimeAfter::NAME;
    const RULE_DATETIME_BEFORE          = Rule\DatetimeBefore::NAME;
    const RULE_DATETIME_FUTURE          = Rule\DatetimeFuture::NAME;
    const RULE_DATETIME_PAST            = Rule\DatetimePast::NAME;
    const RULE_DATE_AFTER               = Rule\DateAfter::NAME;
    const RULE_DATE_BEFORE              = Rule\DateBefore::NAME;
    const RULE_DATE_FUTURE              = Rule\DateFuture::NAME;
    const RULE_DATE_PAST                = Rule\DatePast::NAME;
    const RULE_DATE_TODAY               = Rule\DateToday::NAME;
    const RULE_DECIMAL                  = Rule\Decimal::NAME;
    const RULE_DIFFERS                  = Rule\Differs::NAME;
    const RULE_ENCODE_PHP_TAGS          = Rule\EncodePhpTags::NAME;
    const RULE_EXACT_LENGTH             = Rule\ExactLength::NAME;
    const RULE_GREATER_THAN             = Rule\GreaterThan::NAME;
    const RULE_GREATER_THAN_EQUAL_TO    = Rule\GreaterThanEqualTo::NAME;
    const RULE_INTEGER                  = Rule\Integer::NAME;
    const RULE_IN_LIST                  = Rule\InList::NAME;
    const RULE_IN_RANGE                 = Rule\InRange::NAME;
    const RULE_IS                       = Rule\Is::NAME;
    const RULE_IS_BOOL                  = Rule\IsBool::NAME;
    const RULE_IS_ID                    = Rule\IsId::NAME;
    const RULE_IS_NATURAL               = Rule\IsNatural::NAME;
    const RULE_IS_NATURAL_NO_ZERO       = Rule\IsNaturalNoZero::NAME;
    const RULE_IS_UNIQUE                = Rule\IsUnique::NAME;
    const RULE_ITEM_COUNT               = Rule\ItemCount::NAME;
    const RULE_LESS_THAN                = Rule\LessThan::NAME;
    const RULE_LESS_THAN_EQUAL_TO       = Rule\LessThanEqualTo::NAME;
    const RULE_MATCHES                  = Rule\Matches::NAME;
    const RULE_MAX_LENGTH               = Rule\MaxLength::NAME;
    const RULE_MAX_WORDS                = Rule\MaxWords::NAME;
    const RULE_MIN_LENGTH               = Rule\MinLength::NAME;
    const RULE_NUMERIC                  = Rule\Numeric::NAME;
    const RULE_PREP_FOR_FORM            = Rule\PrepForForm::NAME;
    const RULE_PREP_URL                 = Rule\PrepUrl::NAME;
    const RULE_REGEX_MATCH              = Rule\RegexMatch::NAME;
    const RULE_REQUIRED                 = Rule\Required::NAME;
    const RULE_STRIP_IMAGE_TAGS         = Rule\StripImageTags::NAME;
    const RULE_SUPPORTED_LOCALE         = Rule\SupportedLocale::NAME;
    const RULE_TIME_AFTER               = Rule\TimeAfter::NAME;
    const RULE_TIME_BEFORE              = Rule\TimeBefore::NAME;
    const RULE_TIME_FUTURE              = Rule\TimeFuture::NAME;
    const RULE_TIME_PAST                = Rule\TimePast::NAME;
    const RULE_UNIQUE_IF_DIFF           = Rule\UniqueIfDiff::NAME;
    const RULE_VALID_DATE               = Rule\ValidDate::NAME;
    const RULE_VALID_DATETIME           = Rule\ValidDatetime::NAME;
    const RULE_VALID_EMAIL              = Rule\ValidEmail::NAME;
    const RULE_VALID_EMAILS             = Rule\ValidEmails::NAME;
    const RULE_VALID_IP                 = Rule\ValidIp::NAME;
    const RULE_VALID_POSTCODE           = Rule\ValidPostcode::NAME;
    const RULE_VALID_TIME               = Rule\ValidTime::NAME;
    const RULE_VALID_TIMECODE           = Rule\ValidTimecode::NAME;
    const RULE_VALID_URL                = Rule\ValidUrl::NAME;

    /**
     * @deprecated These rules are provided by the CDN module; use \Nails\Cdn\Constants::RULE_OBJECT_PICKER_MULTI_*
     */
    const RULE_CDNOBJECTPICKERMULTIALLREQUIRED    = 'cdnObjectPickerMultiAllRequired';
    const RULE_CDNOBJECTPICKERMULTILABELREQUIRED  = 'cdnObjectPickerMultiLabelRequired';
    const RULE_CDNOBJECTPICKERMULTIOBJECTREQUIRED = 'cdnObjectPickerMultiObjectRequired';

    /**
     * Splits a pipe-separated rule string, ignoring pipes inside `[...]`
     */
    const RULE_SPLIT_REGEX = '/\|(?![^\[]*\])/';

    // --------------------------------------------------------------------------

    protected RuleSet  $oPendingRules;
    protected array    $aMessages       = [];
    protected array    $aValidationData = [];
    protected string   $sErrorPrefix    = '<p>';
    protected string   $sErrorSuffix    = '</p>';
    protected ?Result  $oLastResult     = null;
    protected array    $aValueCursors   = [];
    protected ?Registry $oRegistry      = null;
    protected ?Engine  $oEngine         = null;

    // --------------------------------------------------------------------------

    public function __construct()
    {
        $this->oPendingRules = new RuleSet();
    }

    // --------------------------------------------------------------------------

    /**
     * The registry of available rules (discovered lazily, once per process)
     */
    public function getRegistry(): Registry
    {
        return $this->oRegistry ??= Registry::discover();
    }

    /**
     * The service used to look up error messages
     *
     * @throws FactoryException
     */
    public function getTranslation(): Translation
    {
        /** @var Translation $oTranslation */
        $oTranslation = Factory::service('Translation');
        return $oTranslation;
    }

    /**
     * The validation engine
     *
     * @throws FactoryException
     */
    public function getEngine(): Engine
    {
        return $this->oEngine ??= new Engine(
            $this->getRegistry(),
            new MessageResolver($this->getTranslation()),
            $this->getTranslation()
        );
    }

    /**
     * The result of the most recent run (legacy run() or a Validator); this is
     * what the set_value()/form_error() view helpers read.
     */
    public function getLastResult(): ?Result
    {
        return $this->oLastResult;
    }

    /**
     * Records a run's result as the most recent one
     */
    public function publish(Result $oResult): static
    {
        $this->oLastResult   = $oResult;
        $this->aValueCursors = [];
        return $this;
    }

    /**
     * Global per-rule message overrides (rule => message)
     *
     * @return array<string, string>
     */
    public function getMessages(): array
    {
        return $this->aMessages;
    }

    // --------------------------------------------------------------------------

    /**
     * Normalises a rule definition (pipe-separated string or array) into an array of rules
     *
     * @param string|array|null $mRules
     *
     * @return array<string|\Closure|RuleInterface>
     */
    public static function splitRules(string|array|null $mRules): array
    {
        if ($mRules === null || $mRules === '') {
            return [];
        }

        $aRules = is_array($mRules) ? $mRules : preg_split(static::RULE_SPLIT_REGEX, $mRules);

        return array_values(array_filter(
            $aRules,
            fn($mRule) => $mRule !== null && $mRule !== ''
        ));
    }

    // --------------------------------------------------------------------------

    /**
     * Builds a validator
     *
     * @param array      $aRules    The validation rules in a key => value format, with
     *                              value being the rules either as an array or pipe separated string
     * @param array      $aMessages An array of error message overrides
     * @param array|null $aData     The data to validate, defaults to $_POST
     *
     * @return Validator
     * @throws FactoryException
     */
    public function buildValidator(array $aRules = [], array $aMessages = [], ?array $aData = null): Validator
    {
        if ($aData === null) {
            /** @var Input $oInput */
            $oInput = Factory::service('Input');
            $aData  = $oInput->post();
        }

        return Factory::factory(
            'FormValidationValidator',
            null,
            $aRules,
            $aMessages,
            $aData
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Builds a validator from a model
     *
     * @param Base       $oModel    The model to use for rule generation
     * @param array      $aMessages An array of error message overrides
     * @param array|null $aData     The data to validate, defaults to $_POST
     *
     * @return Validator
     * @throws FactoryException
     * @throws ValidationException
     */
    public function buildValidatorFromModel(Base $oModel, array $aMessages = [], ?array $aData = null): Validator
    {
        $oValidator = $this->buildValidator([], $aMessages, $aData);
        return $oValidator->setRulesFromModel($oModel);
    }

    // --------------------------------------------------------------------------

    /**
     * Programatically compiles a validation rule
     *
     * @param string $sRule    The rule to compile
     * @param mixed  ...$aArgs Any arguments to pass to the validation rule
     *
     * @return string
     */
    public static function rule(string $sRule, ...$aArgs): string
    {
        if (empty($aArgs)) {
            return $sRule;
        }

        return sprintf(
            '%s[%s]',
            $sRule,
            implode('.', $aArgs)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Sets a rule
     *
     * @param string|array $mKey   The key to set, or an array of key/value pairs
     * @param string       $sRule  The rule to set
     * @param string|null  $sLabel The field being validated, human friendly
     *
     * @return $this;
     */
    public function setRule($mKey, string $sRule, ?string $sLabel = null): self
    {
        if (is_array($mKey)) {
            foreach ($mKey as $sKey => $mRule) {
                if (is_array($mRule)) {
                    foreach ($mRule as $sRule) {
                        $this->set_rules($sKey, null, $sRule);
                    }
                } else {
                    $this->set_rules($sKey, null, $mRule);
                }
            }

        } else {
            $this->set_rules($mKey, $sLabel, $sRule);
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets a rule message
     *
     * @param string $sRule    The rule to set the message for
     * @param string $sMessage The message to set
     *
     * @return $this
     */
    public function setMessage(string $sRule, string $sMessage): self
    {
        $this->set_message($sRule, $sMessage);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns validation errors
     *
     * @return array
     */
    public function errors(): array
    {
        return $this->error_array();
    }

    // --------------------------------------------------------------------------
    //  CodeIgniter-compatible surface
    // --------------------------------------------------------------------------

    /**
     * Declares a field's rules
     *
     * @param string|array $field  The field name, or an array of ['field', 'label', 'rules', 'errors'] rows
     * @param string|null  $label  The field's label
     * @param string|array $rules  Pipe-separated string or array of rules
     * @param array        $errors Per-rule message overrides for this field
     *
     * @deprecated Use buildValidator()
     */
    public function set_rules($field, $label = null, $rules = null, $errors = []): static
    {
        if (is_array($field)) {
            foreach ($field as $aRow) {
                if (!isset($aRow['field'], $aRow['rules'])) {
                    continue;
                }
                $this->set_rules(
                    $aRow['field'],
                    $aRow['label'] ?? $aRow['field'],
                    $aRow['rules'],
                    is_array($aRow['errors'] ?? null) ? $aRow['errors'] : []
                );
            }
            return $this;
        }

        //  Nothing to do
        if (empty($field) || empty($rules)) {
            return $this;
        }

        //  No reason to set rules if we have no POST data and no validation data (CodeIgniter parity)
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' && empty($this->aValidationData)) {
            return $this;
        }

        $this->oPendingRules->add(new Field(
            (string) $field,
            (string) ($label ?? ''),
            static::splitRules($rules),
            (array) $errors
        ));

        return $this;
    }

    /**
     * Sets the data to validate (instead of $_POST)
     *
     * @deprecated Use buildValidator()
     */
    public function set_data(array $data): static
    {
        if (!empty($data)) {
            $this->aValidationData = $data;
        }
        return $this;
    }

    /**
     * Sets a global per-rule error message
     *
     * @param string|array $lang The rule name, or an array of rule => message
     * @param string       $val  The message
     *
     * @deprecated Use buildValidator()
     */
    public function set_message($lang, $val = ''): static
    {
        $this->aMessages = array_merge($this->aMessages, is_array($lang) ? $lang : [$lang => $val]);
        return $this;
    }

    /**
     * @deprecated
     */
    public function set_error_delimiters($prefix = '<p>', $suffix = '</p>'): static
    {
        $this->sErrorPrefix = (string) $prefix;
        $this->sErrorSuffix = (string) $suffix;
        return $this;
    }

    /**
     * Runs the rules declared with set_rules() against the data set with set_data()
     * (or $_POST). On success, when $_POST was validated, the processed values are
     * written back into $_POST.
     *
     * @param mixed      $config Unused (an object here is used as the callback_* target, for parity)
     * @param array|null $data   If passed, receives the processed data
     * @param mixed      $module The object `callback_*` rules are methods of
     *
     * @deprecated Use buildValidator()
     */
    public function run($config = null, &$data = null, $module = ''): bool
    {
        $oCallbackTarget = match (true) {
            is_object($module) => $module,
            is_object($config) => $config,
            function_exists('get_instance') => get_instance(),
            default => null,
        };

        if (count($this->oPendingRules) === 0) {
            return false;
        }

        $bUsesPost = empty($this->aValidationData);
        $oResult   = $this->getEngine()->run(
            $this->oPendingRules,
            $bUsesPost ? $_POST : $this->aValidationData,
            MessageStyle::WITH_FIELD,
            $this->aMessages,
            $oCallbackTarget
        );

        $this->publish($oResult);

        if ($oResult->failed()) {
            return false;
        }

        if (func_num_args() >= 2) {
            $data = $oResult->getData();
        } elseif ($bUsesPost) {
            $_POST = $oResult->getData();
        }

        return true;
    }

    /**
     * @deprecated Use Validator::getErrors()
     */
    public function error_array(): array
    {
        return $this->oLastResult?->getErrors() ?? [];
    }

    /**
     * @deprecated
     */
    public function error($field, $prefix = '', $suffix = ''): string
    {
        $sError = $this->oLastResult?->getError((string) $field);
        if (empty($sError)) {
            return '';
        }

        return ($prefix === '' ? $this->sErrorPrefix : $prefix)
            . $sError
            . ($suffix === '' ? $this->sErrorSuffix : $suffix);
    }

    /**
     * @deprecated
     */
    public function error_string($prefix = '', $suffix = ''): string
    {
        $sOut = '';
        foreach ($this->error_array() as $sError) {
            if ($sError !== '') {
                $sOut .= ($prefix === '' ? $this->sErrorPrefix : $prefix)
                    . $sError
                    . ($suffix === '' ? $this->sErrorSuffix : $suffix)
                    . "\n";
            }
        }
        return $sOut;
    }

    /**
     * Whether a field was declared (in the last run, or pending)
     *
     * @deprecated
     */
    public function has_rule($field): bool
    {
        return ($this->oLastResult?->hasField((string) $field) ?? false)
            || $this->oPendingRules->has((string) $field);
    }

    /**
     * The submitted (processed) value of a field, for repopulating forms;
     * array values are returned one element at a time.
     *
     * @deprecated
     */
    public function set_value($field = '', $default = '')
    {
        $field = (string) $field;

        if ($this->oLastResult === null || !$this->oLastResult->hasField($field)) {
            return $default;
        }

        $mValue = $this->oLastResult->getValue($field);
        if ($mValue === null) {
            return $default;
        }

        if (is_array($mValue)) {
            $aValues = array_values($mValue);
            $iCursor = $this->aValueCursors[$field] ?? 0;
            $this->aValueCursors[$field] = $iCursor + 1;
            return $aValues[$iCursor] ?? null;
        }

        return $mValue;
    }

    /**
     * @deprecated
     */
    public function set_select($field = '', $value = '', $default = false): string
    {
        return $this->setChoice((string) $field, $value, $default, ' selected="selected"');
    }

    /**
     * @deprecated
     */
    public function set_radio($field = '', $value = '', $default = false): string
    {
        return $this->setChoice((string) $field, $value, $default, ' checked="checked"');
    }

    /**
     * @deprecated
     */
    public function set_checkbox($field = '', $value = '', $default = false): string
    {
        return $this->set_radio($field, $value, $default);
    }

    /**
     * Ports CodeIgniter's set_select/set_radio
     */
    protected function setChoice(string $sField, mixed $mOption, mixed $mDefault, string $sAttribute): string
    {
        $mValue = $this->oLastResult?->hasField($sField)
            ? $this->oLastResult->getValue($sField)
            : null;

        if ($mValue === null) {
            $iDeclared = $this->oLastResult !== null
                ? count($this->oLastResult->getRuleSet())
                : count($this->oPendingRules);
            return ($mDefault === true && $iDeclared === 0) ? $sAttribute : '';
        }

        $sOption = (string) $mOption;

        if (is_array($mValue)) {
            foreach ($mValue as $mItem) {
                if (is_scalar($mItem) && $sOption === (string) $mItem) {
                    return $sAttribute;
                }
            }
            return '';
        }

        $sValue = is_scalar($mValue) ? (string) $mValue : '';

        return ($sValue === '' || $sOption === '' || $sValue !== $sOption) ? '' : $sAttribute;
    }

    /**
     * Clears declared rules, messages, data and the last result
     *
     * @deprecated
     */
    public function reset_validation(): static
    {
        $this->oPendingRules   = new RuleSet();
        $this->aMessages       = [];
        $this->aValidationData = [];
        $this->oLastResult     = null;
        $this->aValueCursors   = [];
        return $this;
    }

    /**
     * Returns the declared rules, field => rules[]
     *
     * @deprecated
     */
    public function getRules(): array
    {
        $oRuleSet = count($this->oPendingRules) > 0
            ? $this->oPendingRules
            : ($this->oLastResult?->getRuleSet() ?? new RuleSet());

        $aOut = [];
        foreach ($oRuleSet as $oField) {
            $aOut[$oField->name] = $oField->rules;
        }
        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * @deprecated Closures receive a Context; use $oContext->getValue('other_field')
     */
    public function __get($sProperty)
    {
        if ($sProperty === 'validation_data') {
            return $this->aValidationData;
        }

        trigger_error(
            sprintf('Undefined property %s::$%s', static::class, $sProperty),
            E_USER_DEPRECATED
        );
        return null;
    }

    /**
     * @deprecated
     */
    public function __set($sProperty, $mValue)
    {
        if ($sProperty === 'validation_data' && is_array($mValue)) {
            $this->aValidationData = $mValue;
            return;
        }

        trigger_error(
            sprintf('Undefined property %s::$%s', static::class, $sProperty),
            E_USER_DEPRECATED
        );
    }

    /**
     * Allows a rule to be invoked directly, e.g. $oFormValidation->valid_email($sEmail)
     *
     * @deprecated Use the rule classes directly
     */
    public function __call($sMethod, $aArguments): bool
    {
        if (!$this->getRegistry()->has($sMethod)) {
            throw new BadMethodCallException(
                sprintf('Call to undefined method %s::%s()', static::class, $sMethod)
            );
        }

        $sParam   = isset($aArguments[1]) && $aArguments[1] !== false ? (string) $aArguments[1] : null;
        $sRule    = $sParam !== null ? $sMethod . '[' . $sParam . ']' : $sMethod;
        $oRuleSet = (new RuleSet())->add(new Field('value', '', [$sRule]));

        return $this->getEngine()->run($oRuleSet, ['value' => $aArguments[0] ?? null])->passed();
    }
}
