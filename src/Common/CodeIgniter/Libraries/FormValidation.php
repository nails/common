<?php

/**
 * Adds additional validation rules to the Form_validation library
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\CodeIgniter\Libraries;

use CI_Form_validation;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ValidationException;
use Nails\Common\Helper\ArrayHelper;
use Nails\Common\Helper\Strings;
use Nails\Common\Service\Database;
use Nails\Common\Service\Input;
use Nails\Common\Service\Locale;
use Nails\Factory;

/**
 * Class FormValidation
 *
 * @package Nails\Common\CodeIgniter\Libraries
 */
class FormValidation extends CI_Form_validation
{
    /**
     * Quick mod of run() to allow for HMVC.
     *
     * @param string $module
     * @param string $group
     *
     * @return bool
     */
    public function run($module = '', $group = '')
    {
        if (is_object($module)) {
            $this->CI        = &$module;
            $this->CI->input = get_instance()->input;
            $this->CI->lang  = get_instance()->lang;
        }
        return parent::run($group);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the current ruleset
     *
     * @return array
     */
    public function getRules(): array
    {
        return array_combine(
            array_keys($this->_field_data),
            arrayExtractProperty($this->_field_data, 'rules')
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a certain value is unique in a specified table if different
     * from current value.
     *
     * @param string $new    The form value
     * @param string $params Parameters passed from set_rules() method
     *
     * @return boolean
     */
    public function unique_if_diff($new, $params)
    {
        [$table, $column, $old] = explode(".", $params, 3);

        if ($new == $old) {
            return true;
        }

        if (!array_key_exists('unique_if_diff', $this->_error_messages)) {
            $this->set_message('unique_if_diff', lang('fv_unique_if_diff_field'));
        }

        /** @var Database $oDb */
        $oDb = Factory::service('Database');
        $oDb->where($column . ' !=', $old);
        $oDb->where($column, $new);
        $oDb->limit(1);
        $q = $oDb->get($table);

        if ($q->row()) {
            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a string is in a valid UK post code format.
     *
     * @param string $str The form value
     *
     * @return boolean
     */
    public function valid_postcode($str)
    {
        if (!array_key_exists('valid_postcode', $this->_error_messages)) {
            $this->set_message('valid_postcode', lang('fv_valid_postcode'));
        }

        $pattern = '/^([Gg][Ii][Rr] 0[Aa]{2})|((([A-Za-z][0-9]{1,2})|(([A-Za-z][A-Ha-hJ-Yj-y][0-9]{1,2})|(([A-Za-z][0-9][A-Za-z])|([A-Za-z][A-Ha-hJ-Yj-y][0-9]?[A-Za-z])))) {0,1}[0-9][A-Za-z]{2})$/';
        return preg_match($pattern, strtoupper($str)) ? true : false;
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if an array satisfies specified count restrictions.
     *
     * @param array  $aArray The value to check
     * @param string $sParam The parameter to check against
     *
     * @return boolean
     */
    public function item_count(array $aArray, $sParam)
    {
        $aParams  = preg_replace('/[^0-9]/', '', explode(',', $sParam));
        $mFloor   = ArrayHelper::get(0, $aParams, 0);
        $mCeiling = ArrayHelper::get(1, $aParams, INF);

        if (substr($sParam, 0, 1) === '(' && substr($sParam, -1, 1) === ')') {
            $mFloor++;
            $mCeiling--;
        }

        if (($bAboveFloor = $mFloor <= count($aArray)) === false) {
            $this->set_message('item_count', lang('fv_count_floor'));
        }

        if (($bBeneathCeiling = $mCeiling >= count($aArray)) === false) {
            $this->set_message('item_count', lang('fv_count_ceiling'));
        }

        return $bAboveFloor && $bBeneathCeiling;
    }

    // --------------------------------------------------------------------------

    /**
     * Check if a date is valid
     *
     * @param string $sDate   The date string to check
     * @param string $sFormat The format the string is in
     *
     * @return boolean
     */
    public function valid_date($sDate, $sFormat)
    {
        //  If blank, then assume the date is not required
        if (empty($sDate)) {
            return true;
        }

        if (empty($sFormat)) {
            $sFormat = 'Y-m-d';
        }

        if (!array_key_exists('valid_date', $this->_error_messages)) {
            $this->set_message('valid_date', lang('fv_valid_date_field'));
        }

        try {

            $oDate = \DateTime::createFromFormat($sFormat, $sDate);

            if (empty($oDate)) {
                return false;
            }

            return $oDate->format($sFormat) == $sDate;

        } catch (\Exception $e) {

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a date is in the future
     *
     * @param string $sDate   The date string to check
     * @param string $sFormat The format the string is in
     *
     * @return boolean
     */
    public function date_future($sDate, $sFormat)
    {
        //  If blank, then assume the date is not required
        if (empty($sDate)) {
            return true;
        }

        if (empty($sFormat)) {
            $sFormat = 'Y-m-d';
        }

        if (!array_key_exists('date_future', $this->_error_messages)) {
            $this->set_message('date_future', lang('fv_valid_date_future_field'));
        }

        try {

            /** @var \DateTime $oNow */
            $oNow  = Factory::factory('DateTime');
            $oDate = \DateTime::createFromFormat($sFormat, $sDate);

            if (empty($oDate)) {
                return false;
            }

            $oNow->setTime(0, 0, 0);
            $oDate->setTime(0, 0, 0);

            return $oDate > $oNow;

        } catch (\Exception $e) {

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a date is in the past
     *
     * @param string $sDate   The date string to check
     * @param string $sFormat The format the string is in
     *
     * @return boolean
     */
    public function date_past($sDate, $sFormat)
    {
        //  If blank, then assume the date is not required
        if (empty($sDate)) {
            return true;
        }

        if (empty($sFormat)) {
            $sFormat = 'Y-m-d';
        }

        if (!array_key_exists('date_past', $this->_error_messages)) {
            $this->set_message('date_past', lang('fv_valid_date_past_field'));
        }

        try {

            /** @var \DateTime $oNow */
            $oNow  = Factory::factory('DateTime');
            $oDate = \DateTime::createFromFormat($sFormat, $sDate);

            if (empty($oDate)) {
                return false;
            }

            $oNow->setTime(0, 0, 0);
            $oDate->setTime(0, 0, 0);

            return $oDate < $oNow;

        } catch (\Exception $e) {

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a date is today
     *
     * @param string $sDate   The date string to check
     * @param string $sFormat The format the string is in
     *
     * @return boolean
     */
    public function date_today($sDate, $sFormat)
    {
        //  If blank, then assume the date is not required
        if (empty($sDate)) {
            return true;
        }

        if (empty($sFormat)) {
            $sFormat = 'Y-m-d';
        }

        if (!array_key_exists('date_today', $this->_error_messages)) {
            $this->set_message('date_today', lang('fv_valid_date_today_field'));
        }

        try {

            /** @var \DateTime $oNow */
            $oNow  = Factory::factory('DateTime');
            $oDate = \DateTime::createFromFormat($sFormat, $sDate);

            if (empty($oDate)) {
                return false;
            }

            $oNow->setTime(0, 0, 0);
            $oDate->setTime(0, 0, 0);

            return $oDate === $oNow;

        } catch (\Exception $e) {

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a date is before another date field
     *
     * @param string $sDate   The date string to check
     * @param string $sParams The other field name, and the date format (optional), separated with a period.
     *
     * @return boolean
     */
    public function date_before($sDate, $sParams)
    {
        //  If blank, then assume the date is not required
        if (empty($sDate)) {
            return true;
        }

        if (empty($sParams)) {
            return false;
        }

        $aParams = explode('.', $sParams);
        $sField  = !empty($aParams[0]) ? $aParams[0] : null;
        $sFormat = !empty($aParams[1]) ? $aParams[1] : 'Y-m-d';

        if (empty($sField)) {
            return false;
        }

        if (!array_key_exists('date_before', $this->_error_messages)) {
            $this->set_message('date_before', lang('fv_valid_date_before_field'));
        }

        //  If the other field is blank then bail out
        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        $sOther = $oInput->post($sField);
        if (empty($sOther)) {
            return false;
        }

        try {

            $oDate  = \DateTime::createFromFormat($sFormat, $sDate);
            $oOther = \DateTime::createFromFormat($sFormat, $sOther);

            if (empty($oDate) || $oOther) {
                return false;
            }

            $oDate->setTime(0, 0, 0);
            $oOther->setTime(0, 0, 0);

            return $oDate < $oOther;

        } catch (\Exception $e) {

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a date is after another date field
     *
     * @param string $sDate   The date string to check
     * @param string $sParams The other field name, and the date format (optional), separated with a period.
     *
     * @return boolean
     */
    public function date_after($sDate, $sParams)
    {
        //  If blank, then assume the date is not required
        if (empty($sDate)) {
            return true;
        }

        if (empty($sParams)) {
            return false;
        }

        $aParams = explode('.', $sParams);
        $sField  = !empty($aParams[0]) ? $aParams[0] : null;
        $sFormat = !empty($aParams[1]) ? $aParams[1] : 'Y-m-d';

        if (empty($sField)) {
            return false;
        }

        if (!array_key_exists('date_after', $this->_error_messages)) {
            $this->set_message('date_after', lang('fv_valid_date_after_field'));
        }

        //  If the other field is blank then bail out
        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        $sOther = $oInput->post($sField);
        if (empty($sOther)) {
            return false;
        }

        try {

            $oDate  = \DateTime::createFromFormat($sFormat, $sDate);
            $oOther = \DateTime::createFromFormat($sFormat, $sOther);

            if (empty($oDate) || empty($oOther)) {
                return false;
            }

            $oDate->setTime(0, 0, 0);
            $oOther->setTime(0, 0, 0);

            return $oDate > $oOther;

        } catch (\Exception $e) {

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a datetime string is valid
     *
     * @param string $sDateTime The datetime string to check
     * @param string $sFormat   The format the string is in
     *
     * @return boolean
     */
    public function valid_datetime($sDateTime, $sFormat)
    {
        //  If blank, then assume the date is not required
        if (empty($sDateTime)) {
            return true;
        }

        if (empty($sFormat)) {
            $sFormat = 'Y-m-d H:i:s';
        }

        if (!array_key_exists('valid_datetime', $this->_error_messages)) {
            $this->set_message('valid_datetime', lang('fv_valid_datetime_field'));
        }

        try {

            $oDate = \DateTime::createFromFormat($sFormat, $sDateTime);

            if (empty($oDate)) {
                return false;
            }

            return $oDate->format($sFormat) == $sDateTime;

        } catch (\Exception $e) {

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a datetime string is in the future
     *
     * @param string $sDateTime The datetime string to check
     * @param string $sFormat   The format the string is in
     *
     * @return boolean
     */
    public function datetime_future($sDateTime, $sFormat)
    {
        //  If blank, then assume the date is not required
        if (empty($sDateTime)) {
            return true;
        }

        if (empty($sFormat)) {
            $sFormat = 'Y-m-d H:i:s';
        }

        if (!array_key_exists('datetime_future', $this->_error_messages)) {
            $this->set_message('datetime_future', lang('fv_valid_datetime_future_field'));
        }

        try {

            /** @var \DateTime $oNow */
            $oNow  = Factory::factory('DateTime');
            $oDate = \DateTime::createFromFormat($sFormat, $sDateTime);

            if (empty($oDate)) {
                return false;
            }

            return $oDate > $oNow;

        } catch (\Exception $e) {

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a datetime string is in the past
     *
     * @param string $sDateTime The datetime string to check
     * @param string $sFormat   The format the string is in
     *
     * @return boolean
     */
    public function datetime_past($sDateTime, $sFormat)
    {
        //  If blank, then assume the date is not required
        if (empty($sDateTime)) {
            return true;
        }

        if (empty($sFormat)) {
            $sFormat = 'Y-m-d H:i:s';
        }

        if (!array_key_exists('datetime_past', $this->_error_messages)) {
            $this->set_message('datetime_past', lang('fv_valid_datetime_past_field'));
        }

        try {

            /** @var \DateTime $oNow */
            $oNow  = Factory::factory('DateTime');
            $oDate = \DateTime::createFromFormat($sFormat, $sDateTime);

            if (empty($oDate)) {
                return false;
            }

            return $oDate < $oNow;

        } catch (\Exception $e) {

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a datetime is before another date field
     *
     * @param string $sDateTime The datetime string to check
     * @param string $sParams   The other field name, and the datetime format (optional), separated with a period.
     *
     * @return boolean
     */
    public function datetime_before($sDateTime, $sParams)
    {
        //  If blank, then assume the date is not required
        if (empty($sDateTime)) {
            return true;
        }

        if (empty($sParams)) {
            return false;
        }

        $aParams = explode('.', $sParams);
        $sField  = !empty($aParams[0]) ? $aParams[0] : null;
        $sFormat = !empty($aParams[1]) ? $aParams[1] : 'Y-m-d H:i:s';

        if (empty($sField)) {
            return false;
        }

        if (!array_key_exists('datetime_before', $this->_error_messages)) {
            $this->set_message('datetime_before', lang('fv_valid_datetime_before_field'));
        }

        //  If the other field is blank then bail out
        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        $sOther = $oInput->post($sField);
        if (empty($sOther)) {
            return false;
        }

        try {

            $oDate  = \DateTime::createFromFormat($sFormat, $sDateTime);
            $oOther = \DateTime::createFromFormat($sFormat, $sOther);

            if (empty($oDate) || empty($oOther)) {
                return false;
            }

            return $oDate < $oOther;

        } catch (\Exception $e) {

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a datetime is after another date field
     *
     * @param string $sDateTime The datetime string to check
     * @param string $sParams   The other field name, and the datetime format (optional), separated with a period.
     *
     * @return boolean
     */
    public function datetime_after($sDateTime, $sParams)
    {
        //  If blank, then assume the date is not required
        if (empty($sDateTime)) {
            return true;
        }

        if (empty($sParams)) {
            return false;
        }

        $aParams = explode('.', $sParams);
        $sField  = !empty($aParams[0]) ? $aParams[0] : null;
        $sFormat = !empty($aParams[1]) ? $aParams[1] : 'Y-m-d H:i:s';

        if (empty($sField)) {
            return false;
        }

        if (!array_key_exists('datetime_after', $this->_error_messages)) {
            $this->set_message('datetime_after', lang('fv_valid_datetime_after_field'));
        }

        //  If the other field is blank then bail out
        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        $sOther = $oInput->post($sField);
        if (empty($sOther)) {
            return false;
        }

        try {

            $oDate  = \DateTime::createFromFormat($sFormat, $sDateTime);
            $oOther = \DateTime::createFromFormat($sFormat, $sOther);

            if (empty($oDate) || empty($oOther)) {
                return false;
            }

            return $oDate > $oOther;

        } catch (\Exception $e) {

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a time string is valid
     *
     * @param string $sTime   The time string to check
     * @param string $sFormat The format the string is in
     *
     * @return boolean
     */
    public function valid_time($sTime, $sFormat)
    {
        //  If blank, then assume the date is not required
        if (empty($sTime)) {
            return true;
        }

        if (empty($sFormat)) {
            $sFormat = 'H:i:s';
        }

        if (!array_key_exists('valid_time', $this->_error_messages)) {
            $this->set_message('valid_time', lang('fv_valid_time_field'));
        }

        try {

            $oDate = \DateTime::createFromFormat($sFormat, $sTime);

            if (empty($oDate)) {
                return false;
            }

            return $oDate->format($sFormat) == $sTime;

        } catch (\Exception $e) {

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a time string is in the future relative to right now (assumes date is today)
     *
     * @param string $sTime   The time string to check
     * @param string $sFormat The format the string is in
     *
     * @return boolean
     */
    public function time_future($sTime, $sFormat)
    {
        //  If blank, then assume the date is not required
        if (empty($sTime)) {
            return true;
        }

        if (empty($sFormat)) {
            $sFormat = 'H:i:s';
        }

        if (!array_key_exists('time_future', $this->_error_messages)) {
            $this->set_message('time_future', lang('fv_valid_time_future_field'));
        }

        try {

            /** @var \DateTime $oNow */
            $oNow  = Factory::factory('DateTime');
            $oDate = \DateTime::createFromFormat($sFormat, $sTime);

            if (empty($oDate)) {
                return false;
            }

            return $oDate > $oNow;

        } catch (\Exception $e) {

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a time string is in the past relative to right now (assumes date is today)
     *
     * @param string $sTime   The time string to check
     * @param string $sFormat The format the string is in
     *
     * @return boolean
     */
    public function time_past($sTime, $sFormat)
    {
        //  If blank, then assume the date is not required
        if (empty($sTime)) {
            return true;
        }

        if (empty($sFormat)) {
            $sFormat = 'H:i:s';
        }

        if (!array_key_exists('time_past', $this->_error_messages)) {
            $this->set_message('time_past', lang('fv_valid_time_past_field'));
        }

        try {

            /** @var \DateTime $oNow */
            $oNow  = Factory::factory('DateTime');
            $oDate = \DateTime::createFromFormat($sFormat, $sTime);

            if (empty($oDate)) {
                return false;
            }

            return $oDate < $oNow;

        } catch (\Exception $e) {

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a time string is before another time field
     *
     * @param string $sTime   The time string to check
     * @param string $sParams The other field name, and the time format (optional), separated with a period.
     *
     * @return boolean
     */
    public function time_before($sTime, $sParams)
    {
        //  If blank, then assume the date is not required
        if (empty($sTime)) {
            return true;
        }

        if (empty($sParams)) {
            return false;
        }

        $aParams = explode('.', $sParams);
        $sField  = !empty($aParams[0]) ? $aParams[0] : null;
        $sFormat = !empty($aParams[1]) ? $aParams[1] : 'H:i:s';

        if (empty($sField)) {
            return false;
        }

        if (!array_key_exists('time_before', $this->_error_messages)) {
            $this->set_message('time_before', lang('fv_valid_time_before_field'));
        }

        //  If the other field is blank then bail out
        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        $sOther = $oInput->post($sField);
        if (empty($sOther)) {
            return false;
        }

        try {

            $oDate  = \DateTime::createFromFormat($sFormat, $sTime);
            $oOther = \DateTime::createFromFormat($sFormat, $sOther);

            if (empty($oDate) || empty($oOther)) {
                return false;
            }

            return $oDate < $oOther;

        } catch (\Exception $e) {

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a time string is after another time field
     *
     * @param string $sTime   The time string to check
     * @param string $sParams The other field name, and the time format (optional), separated with a period.
     *
     * @return boolean
     */
    public function time_after($sTime, $sParams)
    {
        //  If blank, then assume the date is not required
        if (empty($sTime)) {
            return true;
        }

        if (empty($sParams)) {
            return false;
        }

        $aParams = explode('.', $sParams);
        $sField  = !empty($aParams[0]) ? $aParams[0] : null;
        $sFormat = !empty($aParams[1]) ? $aParams[1] : 'H:i:s';

        if (empty($sField)) {
            return false;
        }

        if (!array_key_exists('time_after', $this->_error_messages)) {
            $this->set_message('time_after', lang('fv_valid_time_after_field'));
        }

        //  If the other field is blank then bail out
        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        $sOther = $oInput->post($sField);
        if (empty($sOther)) {
            return false;
        }

        try {

            $oDate  = \DateTime::createFromFormat($sFormat, $sTime);
            $oOther = \DateTime::createFromFormat($sFormat, $sOther);

            if (empty($oDate) || empty($oOther)) {
                return false;
            }

            return $oDate > $oOther;

        } catch (\Exception $e) {

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a value is within a range as defined in $field
     *
     * @param string $str   The form value
     * @param string $field The range, e.g., 0-10
     *
     * @return boolean
     */
    public function in_range($str, $field)
    {
        $_range = explode('-', $field);
        $_low   = isset($_range[0]) ? (float) $_range[0] : null;
        $_high  = isset($_range[1]) ? (float) $_range[1] : null;

        if (is_null($_low) || is_null($_high)) {
            return true;
        }

        if ((float) $str >= $_low && (float) $str <= $_high) {

            return true;

        } else {

            if (!array_key_exists('in_range', $this->_error_messages)) {
                $this->set_message('in_range', lang('fv_in_range_field'));
            }

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Valid Email, using filter_var if possible falling back to CI's regex
     *
     * @access  public
     *
     * @param string
     *
     * @return  bool
     */
    public function valid_email($str)
    {
        if (function_exists('filter_var')) {

            return (bool) filter_var($str, FILTER_VALIDATE_EMAIL);

        } else {

            return parent::valid_email($str);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Same as alpha_dash, but includes periods
     *
     * @param string $str The string to test
     *
     * @return boolean
     */
    public function alpha_dash_period($str)
    {
        if (!array_key_exists('alpha_dash_period', $this->_error_messages)) {
            $this->set_message('alpha_dash_period', lang('fv_alpha_dash_period_field'));
        }

        return (!preg_match("/^([\.-a-z0-9_])+$/i", $str)) ? false : true;
    }

    // --------------------------------------------------------------------------

    /**
     * Validates that all items within a CDN Object Multi Picker have a label set
     *
     * @param array $aValues The values from the picker
     *
     * @return boolean
     * @todo  provide this from within the CDN module
     *
     */
    public function cdnObjectPickerMultiObjectRequired($aValues)
    {
        if (!array_key_exists('cdnObjectPickerMultiObjectRequired', $this->_error_messages)) {
            $this->set_message('cdnObjectPickerMultiObjectRequired', lang('fv_cdnObjectPickerMultiObjectRequired_field'));
        }

        foreach ($aValues as $aValue) {
            if (empty($aValue['object_id'])) {
                return false;
            }
        }

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Validates that all items within a CDN Object Multi Picker have an object set
     *
     * @param array $aValues The values from the picker
     *
     * @return boolean
     * @todo  provide this from within the CDN module
     *
     */
    public function cdnObjectPickerMultiLabelRequired($aValues)
    {
        if (!array_key_exists('cdnObjectPickerMultiLabelRequired', $this->_error_messages)) {
            $this->set_message('cdnObjectPickerMultiLabelRequired', lang('fv_cdnObjectPickerMultiLabelRequired_field'));
        }

        foreach ($aValues as $aValue) {
            if (empty($aValue['label'])) {
                return false;
            }
        }

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Validates that all items within a CDN Object Multi Picker have both an object and a label set
     *
     * @param array $aValues The values from the picker
     *
     * @return boolean
     * @todo  provide this from within the CDN module
     *
     */
    public function cdnObjectPickerMultiAllRequired($aValues)
    {
        if (!array_key_exists('cdnObjectPickerMultiAllRequired', $this->_error_messages)) {
            $this->set_message('cdnObjectPickerMultiAllRequired', lang('fv_cdnObjectPickerMultiAllRequired_field'));
        }

        foreach ($aValues as $aValue) {
            if (empty($aValue['object_id']) || empty($aValue['label'])) {
                return false;
            }
        }

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Checks a value is uniqe in a given table
     *
     * @param string $sString     The string to check
     * @param string $sParameters Period separated parameters; table.column.ignore_id.ignore_id_column
     *
     * @return bool
     */
    public function is_unique($sString, $sParameters)
    {
        $aParameters   = explode('.', $sParameters);
        $sTable        = ArrayHelper::get(0, $aParameters);
        $sColumn       = ArrayHelper::get(1, $aParameters);
        $sIgnoreId     = ArrayHelper::get(2, $aParameters);
        $sIgnoreColumn = ArrayHelper::get(3, $aParameters, 'id');

        /** @var Database $oDb */
        $oDb = Factory::service('Database');
        $oDb->where($sColumn, $sString);
        if ($sIgnoreId) {
            $oDb->where($sIgnoreColumn . ' !=', $sIgnoreId);
        }
        return $oDb->count_all_results($sTable) === 0;
    }

    // --------------------------------------------------------------------------

    /**
     * Checks whether a value is a boolean value
     *
     * @param string $bValue The value to check
     *
     * @return bool
     */
    public function is_bool($bValue)
    {
        if (!array_key_exists('is_bool', $this->_error_messages)) {
            $this->set_message('is_bool', lang('fv_is_bool_field'));
        }

        return is_bool($bValue) || $bValue === '1' || $bValue === '0' || $bValue === 1 || $bValue === 0;
    }

    // --------------------------------------------------------------------------

    /**
     * Checks whether a value is a valid ID
     *
     * @param string $bValue The value to check
     *
     * @return bool
     */
    public function is_id($sId, $sParams)
    {
        //  If blank, then assume the ID is not required
        if (empty($sId)) {
            return true;
        }

        if (!array_key_exists('is_id', $this->_error_messages)) {
            $this->set_message('is_id', lang('fv_is_id_field'));
        }

        try {

            [$sModel, $sProvider] = array_pad(explode('.', $sParams), 2, 'app');
            $oModel = Factory::model($sModel, $sProvider);
            return $oModel->getById($sId) !== null;

        } catch (FactoryException $e) {
            throw new ValidationException(
                sprintf(
                    'Failed to load model %s::%s when processing rule is_id; %s',
                    $sModel,
                    $sProvider,
                    $e->getMessage()
                )
            );
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether the vlaue is a supported locale
     *
     * @param string $sValue The vaue to check
     *
     * @return bool
     * @throws \Nails\Common\Exception\FactoryException
     */
    public function supportedLocale($sValue)
    {
        /** @var Locale $oLocale */
        $oLocale           = Factory::service('Locale');
        $aSupportedLocales = $oLocale->getSupportedLocales();
        if (!in_array($sValue, $aSupportedLocales)) {

            if (!array_key_exists('supportedLocale', $this->_error_messages)) {
                $this->set_message('supportedLocale', lang('fv_supportedLocale_field'));
            }

            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether a value is specifically something
     *
     * @param string $sValue    The value to check
     * @param string $sExpected The expected value
     *
     * @return bool
     */
    public function is($sValue, $sExpected)
    {
        if ($sValue !== $sExpected) {
            if (!array_key_exists('is', $this->_error_messages)) {
                $this->set_message('is', lang('fv_is_field'));
            }
            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether a field is a valid timecode or not
     *
     * @param $sTimecode The timecode to test
     *
     * @return bool
     */
    public function validTimecode($sTimecode)
    {
        if (!preg_match('/^\d+\d:[0-5]\d:[0-5]\d$/', $sTimecode)) {
            if (!array_key_exists('validTimecode', $this->_error_messages)) {
                $this->set_message('validTimecode', lang('fv_validTimecode_field'));
            }
            return false;
        }
        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether a value exceeds the maximum number of words
     *
     * @param $sValue    The value to test
     * @param $iMaxWords The maximum number of words allowed
     *
     * @return bool
     */
    public function maxWords($sValue, $iMaxWords)
    {
        if (str_word_count($sValue) > (int) $iMaxWords) {
            if (!array_key_exists('max_words', $this->_error_messages)) {
                $this->set_message('max_words', lang('fv_maxWords_field'));
            }
            return false;
        }
        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Validates a string contains only valid emails
     *
     * @param $str
     *
     * @return bool
     */
    public function valid_emails($str)
    {
        return parent::valid_emails(
            implode(',', Strings::toArray($str))
        );
    }
}
