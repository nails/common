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
 * @method set_rules($field, $label = '', $rules = [], $errors = [])
 * @method set_data(array $data)
 * @method set_message($lang, $val = '')
 * @method set_error_delimiters($prefix = '<p>', $suffix = '</p>')
 * @method error($field, $prefix = '', $suffix = '')
 * @method error_array()
 * @method error_string($prefix = '', $suffix = '')
 * @method run($group = '')
 * @method has_rule($field)
 * @method set_value($field = '', $default = '')
 * @method set_select($field = '', $value = '', $default = false)
 * @method set_radio($field = '', $value = '', $default = false)
 * @method set_checkbox($field = '', $value = '', $default = false)
 * @method required($str)
 * @method regex_match($str, $regex)
 * @method matches($str, $field)
 * @method differs($str, $field)
 * @method min_length($str, $val)
 * @method max_length($str, $val)
 * @method exact_length($str, $val)
 * @method valid_url($str)
 * @method valid_emails($str)
 * @method valid_ip($ip, $which = '')
 * @method alpha($str)
 * @method alpha_numeric($str)
 * @method alpha_numeric_spaces($str)
 * @method alpha_dash($str)
 * @method numeric($str)
 * @method integer($str)
 * @method decimal($str)
 * @method greater_than($str, $min)
 * @method greater_than_equal_to($str, $min)
 * @method less_than($str, $max)
 * @method less_than_equal_to($str, $max)
 * @method in_list($value, $list)
 * @method is_natural($str)
 * @method is_natural_no_zero($str)
 * @method valid_base64($str)
 * @method prep_for_form($data)
 * @method prep_url($str = '')
 * @method strip_image_tags($str)
 * @method encode_php_tags($str)
 * @method reset_validation()
 *
 *
 * The following are provied by the Nails FormValidation extension
 *
 * @method getRules(): array
 * @method unique_if_diff($new, $params)
 * @method valid_postcode($str)
 * @method item_count(array $aArray, $sParam)
 * @method valid_date($sDate, $sFormat)
 * @method date_future($sDate, $sFormat)
 * @method date_past($sDate, $sFormat)
 * @method date_today($sDate, $sFormat)
 * @method date_before($sDate, $sParams)
 * @method date_after($sDate, $sParams)
 * @method valid_datetime($sDateTime, $sFormat)
 * @method datetime_future($sDateTime, $sFormat)
 * @method datetime_past($sDateTime, $sFormat)
 * @method datetime_before($sDateTime, $sParams)
 * @method datetime_after($sDateTime, $sParams)
 * @method valid_time($sTime, $sFormat)
 * @method time_future($sTime, $sFormat)
 * @method time_past($sTime, $sFormat)
 * @method time_before($sTime, $sParams)
 * @method time_after($sTime, $sParams)
 * @method in_range($str, $field)
 * @method valid_email($str)
 * @method alpha_dash_period($str)
 * @method cdnObjectPickerMultiObjectRequired($aValues)
 * @method cdnObjectPickerMultiLabelRequired($aValues)
 * @method cdnObjectPickerMultiAllRequired($aValues)
 * @method is_unique($sString, $sParameters)
 * @method is_bool($bValue)
 * @method supportedLocale($sValue)
 * @method is($sValue, $sExpected)
 */
class FormValidation
{
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
     */
    public function buildValidatorFromModel(Base $oModel, array $aMessages = [], array $aData = null): Validator
    {
        $aRules = [];
        /** @var Field $oField */
        foreach ($oModel->describeFields() as $oField) {
            $aRules[$oField->key] = $oField->validation;
        }

        return $this->buildValidator($aRules, $aMessages, $aData);
    }
}
