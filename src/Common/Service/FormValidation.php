<?php

/**
 * The class abstracts CI's FormValidation class.
 *
 * @todo (Pablo - 2018-04-18) - Remove dependency on CI
 *
 * @package                   Nails
 * @subpackage                common
 * @category                  Service
 * @author                    Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ValidationException;
use Nails\Common\Factory\Model\Field;
use Nails\Common\Factory\Service\FormValidation\Validator;
use Nails\Common\Model\Base;
use Nails\Factory;

/**
 * Class FormValidation
 *
 * @package Nails\Common\Service
 *
 * @property $validation_data = []
 *
 * The following are provied natively by CodeIgniter's FormValidation library
 *
 * @method alpha($str)
 * @method alpha_dash($str)
 * @method alpha_numeric($str)
 * @method alpha_numeric_spaces($str)
 * @method decimal($str)
 * @method differs($str, $field)
 * @method encode_php_tags($str)
 * @method error($field, $prefix = '', $suffix = '')
 * @method error_array()
 * @method error_string($prefix = '', $suffix = '')
 * @method exact_length($str, $val)
 * @method greater_than($str, $min)
 * @method greater_than_equal_to($str, $min)
 * @method has_rule($field)
 * @method in_list($value, $list)
 * @method integer($str)
 * @method is_natural($str)
 * @method is_natural_no_zero($str)
 * @method less_than($str, $max)
 * @method less_than_equal_to($str, $max)
 * @method matches($str, $field)
 * @method max_length($str, $val)
 * @method min_length($str, $val)
 * @method numeric($str)
 * @method prep_for_form($data)
 * @method prep_url($str = '')
 * @method regex_match($str, $regex)
 * @method required($str)
 * @method reset_validation()
 * @method run($group = '')
 * @method set_checkbox($field = '', $value = '', $default = false)
 * @method set_data(array $data)
 * @method set_error_delimiters($prefix = '<p>', $suffix = '</p>')
 * @method set_message($lang, $val = '')
 * @method set_radio($field = '', $value = '', $default = false)
 * @method set_rules($field, $label = '', $rules = [], $errors = [])
 * @method set_select($field = '', $value = '', $default = false)
 * @method set_value($field = '', $default = '')
 * @method strip_image_tags($str)
 * @method valid_base64($str)
 * @method valid_emails($str)
 * @method valid_ip($ip, $which = '')
 * @method valid_url($str)
 *
 *
 * The following are provied by the Nails FormValidation extension
 *
 * @method alpha_dash_period($str)
 * @method cdnObjectPickerMultiAllRequired($aValues)
 * @method cdnObjectPickerMultiLabelRequired($aValues)
 * @method cdnObjectPickerMultiObjectRequired($aValues)
 * @method date_after($sDate, $sParams)
 * @method date_before($sDate, $sParams)
 * @method date_future($sDate, $sFormat)
 * @method date_past($sDate, $sFormat)
 * @method date_today($sDate, $sFormat)
 * @method datetime_after($sDateTime, $sParams)
 * @method datetime_before($sDateTime, $sParams)
 * @method datetime_future($sDateTime, $sFormat)
 * @method datetime_past($sDateTime, $sFormat)
 * @method getRules(): array
 * @method in_range($str, $field)
 * @method is($sValue, $sExpected)
 * @method is_bool($bValue)
 * @method is_id($bValue, $sParams)
 * @method is_unique($sString, $sParameters)
 * @method item_count(array $aArray, $sParam)
 * @method supportedLocale($sValue)
 * @method time_after($sTime, $sParams)
 * @method time_before($sTime, $sParams)
 * @method time_future($sTime, $sFormat)
 * @method time_past($sTime, $sFormat)
 * @method unique_if_diff($new, $params)
 * @method valid_date($sDate, $sFormat)
 * @method valid_datetime($sDateTime, $sFormat)
 * @method valid_email($str)
 * @method valid_postcode($str)
 * @method valid_time($sTime, $sFormat)
 * @method valid_timecode($sTimecode)
 */
class FormValidation
{
    /**
     * The following constants represent the various rules available.
     */
    const RULE_ALPHA                 = 'alpha';
    const RULE_ALPHA_DASH            = 'alpha_dash';
    const RULE_ALPHA_DASH_PERIOD     = 'alpha_dash_period';
    const RULE_ALPHA_NUMERIC         = 'alpha_numeric';
    const RULE_ALPHA_NUMERIC_SPACES  = 'alpha_numeric_spaces';
    const RULE_DATETIME_AFTER        = 'datetime_after';
    const RULE_DATETIME_BEFORE       = 'datetime_before';
    const RULE_DATETIME_FUTURE       = 'datetime_future';
    const RULE_DATETIME_PAST         = 'datetime_past';
    const RULE_DATE_AFTER            = 'date_after';
    const RULE_DATE_BEFORE           = 'date_before';
    const RULE_DATE_FUTURE           = 'date_future';
    const RULE_DATE_PAST             = 'date_past';
    const RULE_DATE_TODAY            = 'date_today';
    const RULE_DECIMAL               = 'decimal';
    const RULE_DIFFERS               = 'differs';
    const RULE_ENCODE_PHP_TAGS       = 'encode_php_tags';
    const RULE_EXACT_LENGTH          = 'exact_length';
    const RULE_GREATER_THAN          = 'greater_than';
    const RULE_GREATER_THAN_EQUAL_TO = 'greater_than_equal_to';
    const RULE_INTEGER               = 'integer';
    const RULE_IN_LIST               = 'in_list';
    const RULE_IN_RANGE              = 'in_range';
    const RULE_IS                    = 'is';
    const RULE_IS_BOOL               = 'is_bool';
    const RULE_IS_ID                 = 'is_id';
    const RULE_IS_NATURAL            = 'is_natural';
    const RULE_IS_NATURAL_NO_ZERO    = 'is_natural_no_zero';
    const RULE_IS_UNIQUE             = 'is_unique';
    const RULE_ITEM_COUNT            = 'item_count';
    const RULE_LESS_THAN             = 'less_than';
    const RULE_LESS_THAN_EQUAL_TO    = 'less_than_equal_to';
    const RULE_MATCHES               = 'matches';
    const RULE_MAX_LENGTH            = 'max_length';
    const RULE_MIN_LENGTH            = 'min_length';
    const RULE_NUMERIC               = 'numeric';
    const RULE_PREP_FOR_FORM         = 'prep_for_form';
    const RULE_PREP_URL              = 'prep_url';
    const RULE_REGEX_MATCH           = 'regex_match';
    const RULE_REQUIRED              = 'required';
    const RULE_STRIP_IMAGE_TAGS      = 'strip_image_tags';
    const RULE_SUPPORTED_LOCALE      = 'supportedLocale';
    const RULE_TIME_AFTER            = 'time_after';
    const RULE_TIME_BEFORE           = 'time_before';
    const RULE_TIME_FUTURE           = 'time_future';
    const RULE_TIME_PAST             = 'time_past';
    const RULE_UNIQUE_IF_DIFF        = 'unique_if_diff';
    const RULE_VALID_BASE64          = 'valid_base64';
    const RULE_VALID_DATE            = 'valid_date';
    const RULE_VALID_DATETIME        = 'valid_datetime';
    const RULE_VALID_EMAIL           = 'valid_email';
    const RULE_VALID_EMAILS          = 'valid_emails';
    const RULE_VALID_IP              = 'valid_ip';
    const RULE_VALID_POSTCODE        = 'valid_postcode';
    const RULE_VALID_TIME            = 'valid_time';
    const RULE_VALID_TIMECODE        = 'valid_timecode';
    const RULE_VALID_URL             = 'valid_url';

    //  @todo (Pablo - 2019-12-16) - Deprecate/remove/move these rules
    const RULE_CDNOBJECTPICKERMULTIALLREQUIRED    = 'cdnObjectPickerMultiAllRequired';
    const RULE_CDNOBJECTPICKERMULTILABELREQUIRED  = 'cdnObjectPickerMultiLabelRequired';
    const RULE_CDNOBJECTPICKERMULTIOBJECTREQUIRED = 'cdnObjectPickerMultiObjectRequired';

    // --------------------------------------------------------------------------

    /**
     * The CI_Form_validation object
     *
     * @var \CI_Form_validation
     */
    private $oFormValidation;

    // --------------------------------------------------------------------------

    /**
     * FormValidation constructor.
     */
    public function __construct()
    {
        $oCi = get_instance();
        $oCi->load->library('form_validation');
        $this->oFormValidation = $oCi->form_validation;
    }

    // --------------------------------------------------------------------------

    /**
     * Route calls to the CodeIgniter FormValidation class
     *
     * @param string $sMethod    The method being called
     * @param array  $aArguments Any arguments being passed
     *
     * @return mixed
     */
    public function __call($sMethod, $aArguments)
    {
        if (method_exists($this, $sMethod)) {
            return call_user_func_array([$this, $sMethod], $aArguments);
        } else {
            return call_user_func_array([$this->oFormValidation, $sMethod], $aArguments);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Pass any property "gets" to the CodeIgniter FormValidation class
     *
     * @param string $sProperty The property to get
     *
     * @return mixed
     */
    public function __get($sProperty)
    {
        return $this->oFormValidation->{$sProperty};
    }

    // --------------------------------------------------------------------------

    /**
     * Sets a rule
     *
     * @param string|array $mKey   The key to set, or an array of key/value pairs
     * @param string|array $mRule  The rule to set
     * @param string|null  $sLabel The field being validated, human friendly
     *
     * @return $this;
     */
    public function setRule($mKey, string $mRule, string $sLabel = null): self
    {
        if (is_array($mKey)) {
            foreach ($mKey as $sKey => $mRule) {
                if (is_array($mRule)) {
                    foreach ($mRule as $sRule) {
                        $this->oFormValidation->set_rules($sKey, null, $sRule);
                    }
                } else {
                    $this->oFormValidation->set_rules($sKey, null, $mRule);
                }
            }
        } else {
            if (is_array($mRule)) {
                foreach ($mRule as $sRule) {
                    $this->oFormValidation->set_rules($mKey, null, $sRule);
                }
            } else {
                $this->oFormValidation->set_rules($mKey, $sLabel, $sRule);
            }
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
        $this->oFormValidation->set_message($sRule, $sMessage);
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
        return $this->oFormValidation->error_array();
    }

    // --------------------------------------------------------------------------

    /**
     * Pass any property "sets" to the CodeIgniter FormValidation class
     *
     * @param string $sProperty The property to set
     * @param mixed  $mValue    The value to set
     *
     * @return void
     */
    public function __set($sProperty, $mValue)
    {
        $this->oFormValidation->{$sProperty} = $mValue;
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
    public function buildValidator(array $aRules = [], array $aMessages = [], array $aData = null): Validator
    {
        $oInput = Factory::service('Input');
        return Factory::factory(
            'FormValidationValidator',
            null,
            $aRules,
            $aMessages,
            $aData ?? $oInput->post()
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
    public function buildValidatorFromModel(Base $oModel, array $aMessages = [], array $aData = null): Validator
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
     * @throws FactoryException
     * @throws ValidationException
     */
    public static function rule(string $sRule, ...$aArgs): string
    {
        /**
         * Ensure that the instance has been loaded at least once, this will ensure
         * all the right classes have ben loaded.
         */
        Factory::service('FormValidation');

        if (
            !method_exists(\Nails\Common\CodeIgniter\Libraries\FormValidation::class, $sRule)
            && !method_exists(\CI_Form_validation::class, $sRule)
        ) {
            throw new ValidationException(
                sprintf('%s is not a valid validation rule', $sRule),
                HttpCodes::STATUS_INTERNAL_SERVER_ERROR
            );
        }

        if (empty($aArgs)) {
            return $sRule;
        }

        return sprintf(
            '%s[%s]',
            $sRule,
            implode('.', $aArgs)
        );
    }
}
