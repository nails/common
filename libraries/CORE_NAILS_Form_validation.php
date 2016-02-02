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

use Nails\Factory;

class CORE_NAILS_Form_validation extends CI_Form_validation
{
    /**
     * Quick mod of run() to allow for HMVC.
     */
    public function run($module = '', $group = '')
    {
        (is_object($module)) && $this->CI = &$module;
            return parent::run($group);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the form validation error array.
     * @return array
     */
    public function get_error_array()
    {
        return $this->_error_array;
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a certain value is unique in a specified table if different
     * from current value.
     * @param  string $new    The form value
     * @param  string $params Parameters passed from set_rules() method
     * @return boolean
     */
    public function unique_if_diff($new, $params)
    {
        $CI =& get_instance();

        list($table, $column, $old) = explode(".", $params, 3);

        if ($new == $old) {
            return true;
        }

        if (!array_key_exists('unique_if_diff', $CI->form_validation->_error_messages)) {
            $CI->form_validation->set_message('unique_if_diff', lang('fv_unique_if_diff_field'));
        }

        $CI->db->where($column . ' !=', $old);
        $CI->db->where($column, $new);
        $CI->db->limit(1);
        $q = $CI->db->get($table);

        if ($q->row()) {
            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a string is in a valid UK post code format.
     * @param  string $str The form value
     * @return boolean
     */
    public function valid_postcode($str)
    {
        $CI =& get_instance();

        if (!array_key_exists('valid_postcode', $CI->form_validation->_error_messages)) {
            $CI->form_validation->set_message('valid_postcode', lang('fv_valid_postcode'));
        }

        $pattern = '/^([Gg][Ii][Rr] 0[Aa]{2})|((([A-Za-z][0-9]{1,2})|(([A-Za-z][A-Ha-hJ-Yj-y][0-9]{1,2})|(([A-Za-z][0-9][A-Za-z])|([A-Za-z][A-Ha-hJ-Yj-y][0-9]?[A-Za-z])))) {0,1}[0-9][A-Za-z]{2})$/';
        return preg_match($pattern, strtoupper($str)) ? true : false;
    }

    // --------------------------------------------------------------------------


    /**
     * Check if a date is valid
     * @param  string  $sDate   The date string to check
     * @param  string  $sFormat The format the string is in
     * @return boolean
     */
    public function valid_date($sDate, $sFormat = 'Y-m-d')
    {
        //  If blank, then assume the date is not required
        if (empty($sDate)) {
            return true;
        }

        $oCi =& get_instance();
        if (!array_key_exists('valid_date', $oCi->form_validation->_error_messages)) {
            $oCi->form_validation->set_message('valid_date', lang('fv_valid_date_field'));
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
     * @param  string  $sDate   The date string to check
     * @param  string  $sFormat The format the string is in
     * @return boolean
     */
    public function date_future($sDate, $sFormat = 'Y-m-d')
    {
        //  If blank, then assume the date is not required
        if (empty($sDate)) {
            return true;
        }

        $oCi =& get_instance();
        if (!array_key_exists('date_future', $oCi->form_validation->_error_messages)) {
            $oCi->form_validation->set_message('date_future', lang('fv_valid_date_future_field'));
        }

        try {

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
     * @param  string  $sDate   The date string to check
     * @param  string  $sFormat The format the string is in
     * @return boolean
     */
    public function date_past($sDate, $sFormat = 'Y-m-d')
    {
        //  If blank, then assume the date is not required
        if (empty($sDate)) {
            return true;
        }

        $oCi =& get_instance();
        if (!array_key_exists('date_past', $oCi->form_validation->_error_messages)) {
            $oCi->form_validation->set_message('date_past', lang('fv_valid_date_past_field'));
        }

        try {

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
     * @param  string  $sDate   The date string to check
     * @param  string  $sFormat The format the string is in
     * @return boolean
     */
    public function date_today($sDate, $sFormat = 'Y-m-d')
    {
        //  If blank, then assume the date is not required
        if (empty($sDate)) {
            return true;
        }

        $oCi =& get_instance();
        if (!array_key_exists('date_today', $oCi->form_validation->_error_messages)) {
            $oCi->form_validation->set_message('date_today', lang('fv_valid_date_today_field'));
        }

        try {

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
     * @param  string  $sDate   The date string to check
     * @param  string  $sParams The other field name, and the date format (optional), seperated with a period.
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

        $oCi =& get_instance();
        if (!array_key_exists('date_before', $oCi->form_validation->_error_messages)) {
            $oCi->form_validation->set_message('date_before', lang('fv_valid_date_before_field'));
        }

        //  If the other field is blank then bail out
        $sOther = $oCi->input->post($sField);
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
     * @param  string  $sDate   The date string to check
     * @param  string  $sParams The other field name, and the date format (optional), seperated with a period.
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

        $oCi =& get_instance();
        if (!array_key_exists('date_after', $oCi->form_validation->_error_messages)) {
            $oCi->form_validation->set_message('date_after', lang('fv_valid_date_after_field'));
        }

        //  If the other field is blank then bail out
        $sOther = $oCi->input->post($sField);
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
     * Checks if a datetime string is vali
     * @param  string  $sDateTime The datetime string to check
     * @param  string  $sFormat   The format the string is in
     * @return boolean
     */
    public function valid_datetime($sDateTime, $sFormat = 'Y-m-d H:i:s')
    {
        //  If blank, then assume the date is not required
        if (empty($sDateTime)) {
            return true;
        }

        $oCi =& get_instance();
        if (!array_key_exists('valid_datetime', $oCi->form_validation->_error_messages)) {
            $oCi->form_validation->set_message('valid_datetime', lang('fv_valid_datetime_field'));
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
     * @param  string  $sDateTime The datetime string to check
     * @param  string  $sFormat   The format the string is in
     * @return boolean
     */
    public function datetime_future($sDateTime, $sFormat = 'Y-m-d H:i:s')
    {
        //  If blank, then assume the date is not required
        if (empty($sDateTime)) {
            return true;
        }

        $oCi =& get_instance();
        if (!array_key_exists('datetime_future', $oCi->form_validation->_error_messages)) {
            $oCi->form_validation->set_message('datetime_future', lang('fv_valid_datetime_future_field'));
        }

        try {

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
     * @param  string  $sDateTime The datetime string to check
     * @param  string  $sFormat   The format the string is in
     * @return boolean
     */
    public function datetime_past($sDateTime, $sFormat = 'Y-m-d H:i:s')
    {
        //  If blank, then assume the date is not required
        if (empty($sDateTime)) {
            return true;
        }

        $oCi =& get_instance();
        if (!array_key_exists('datetime_past', $oCi->form_validation->_error_messages)) {
            $oCi->form_validation->set_message('datetime_past', lang('fv_valid_datetime_past_field'));
        }

        try {

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
     * @param  string  $sDateTime The datetime string to check
     * @param  string  $sParams   The other field name, and the datetime format (optional), seperated with a period.
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

        $oCi =& get_instance();
        if (!array_key_exists('datetime_before', $oCi->form_validation->_error_messages)) {
            $oCi->form_validation->set_message('datetime_before', lang('fv_valid_datetime_before_field'));
        }

        //  If the other field is blank then bail out
        $sOther = $oCi->input->post($sField);
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
     * @param  string  $sDateTime The datetime string to check
     * @param  string  $sParams   The other field name, and the datetime format (optional), seperated with a period.
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

        $oCi =& get_instance();
        if (!array_key_exists('datetime_after', $oCi->form_validation->_error_messages)) {
            $oCi->form_validation->set_message('datetime_after', lang('fv_valid_datetime_after_field'));
        }

        //  If the other field is blank then bail out
        $sOther = $oCi->input->post($sField);
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
     * Checks if a value is within a range as defined in $field
     * @param  string $str   The form value
     * @param  string $field The range, e.g., 0-10
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

            $CI =& get_instance();

            if (!array_key_exists('in_range', $CI->form_validation->_error_messages)) {
                $CI->form_validation->set_message('in_range', lang('fv_in_range_field'));
            }

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Valid Email, using filter_var if possible falling back to CI's regex
     *
     * @access  public
     * @param   string
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
     * @param  string $str The string to test
     * @return boolean
     */
    public function alpha_dash_period($str)
    {
        return (!preg_match("/^([\.-a-z0-9_-])+$/i", $str)) ? false : true;
    }

    // --------------------------------------------------------------------------

    /**
     * Validates that all items within a CDN Object Multi Picker have a label set
     * @todo  provide this from within the CDN module
     * @param  array   $aValues The values from the picker
     * @return boolean
     */
    public function cdnObjectPickerMultiObjectRequired($aValues)
    {
        $CI =& get_instance();
        $CI->form_validation->set_message(
            'cdnObjectPickerMultiObjectRequired',
            'All items must have a file set.'
        );

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
     * @todo  provide this from within the CDN module
     * @param  array   $aValues The values from the picker
     * @return boolean
     */
    public function cdnObjectPickerMultiLabelRequired($aValues)
    {
        $CI =& get_instance();
        $CI->form_validation->set_message(
            'cdnObjectPickerMultiLabelRequired',
            'All items must have a label set.'
        );

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
     * @todo  provide this from within the CDN module
     * @param  array   $aValues The values from the picker
     * @return boolean
     */
    public function cdnObjectPickerMultiAllRequired($aValues)
    {
        $CI =& get_instance();
        $CI->form_validation->set_message(
            'cdnObjectPickerMultiAllRequired',
            'All items must have a file and a label set.'
        );

        foreach ($aValues as $aValue) {
            if (empty($aValue['object_id']) || empty($aValue['label'])) {
                return false;
            }
        }

        return true;
    }
}
