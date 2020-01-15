<?php

/**
 * Form helper
 * This class provides a simple interface for building out form components.
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 */

namespace Nails\Common\Helper;

use Nails\Common\Exception\NailsException;
use Nails\Common\Service\Config;
use Nails\Common\Service\Input;
use Nails\Common\Service\Uri;
use Nails\Factory;

/**
 * Class Form
 *
 * @package Nails\Common\Helper
 */
class Form
{
    /**
     * The following constants represent the various field types available.
     */
    const FIELD_BOOLEAN           = 'boolean';
    const FIELD_BUTTON            = 'button';
    const FIELD_CHECKBOX          = 'checkbox';
    const FIELD_COLOR             = 'color';
    const FIELD_DATE              = 'date';
    const FIELD_DATETIME          = 'datetime';
    const FIELD_DROPDOWN          = 'dropdown';
    const FIELD_DROPDOWN_MULTIPLE = 'dropdownMultiple';
    const FIELD_EMAIL             = 'email';
    const FIELD_HIDDEN            = 'hidden';
    const FIELD_NUMBER            = 'number';
    const FIELD_PASSWORD          = 'password';
    const FIELD_RADIO             = 'radio';
    const FIELD_RENDER            = 'render';
    const FIELD_SUBMIT            = 'submit';
    const FIELD_TEL               = 'tel';
    const FIELD_TEXT              = 'text';
    const FIELD_TEXTAREA          = 'textarea';
    const FIELD_TIME              = 'time';
    const FIELD_UPLOAD            = 'upload';
    const FIELD_URL               = 'url';

    // --------------------------------------------------------------------------

    /**
     * Convinience function for rendering a field
     *
     * @param string $sField The field to render
     * @param array  $aData  The array to render the field with
     *
     * @return string
     * @throws NailsException
     */
    public static function render(string $sField, array $aData = []): string
    {
        if (!is_callable('static::' . $sField)) {
            throw new NailsException('"' . $sField . '"  is not a valid form type');
        }

        return call_user_func('static::' . $sField, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Renders an input
     *
     * @param string       $sType  The type of input to render
     * @param array|string $mData  The data to build the input with
     * @param mixed        $mValue The value of the input
     * @param string       $sExtra Any extra attributes
     *
     * @return string
     */
    private static function input(string $sType, $mData = '', $mValue = '', string $sExtra = ''): string
    {
        $aDefaults = [
            'type'  => $sType,
            'name'  => !is_array($mData) ? $mData : '',
            'value' => $mValue,
        ];

        return '<input ' . _parse_form_attributes($mData, $aDefaults) . $sExtra . ' />';
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a generic text input
     *
     * @param mixed
     * @param string
     * @param mixed
     *
     * @return string
     */
    public static function text($data = '', $value = '', $extra = ''): string
    {
        return static::input('text', $data, $value, $extra);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates an input using the "hidden" type
     *
     * @param mixed
     * @param string
     * @param mixed
     *
     * @return string
     */
    public static function hidden($data = '', $value = '', $extra = '')
    {
        return static::input('hidden', $data, $value, $extra);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates an input using the "password" type
     *
     * @param mixed
     * @param string
     * @param mixed
     *
     * @return string
     */
    public static function password($data = '', $value = '', $extra = '')
    {
        return static::input('password', $data, $value, $extra);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a generic textarea input
     *
     * @param mixed
     * @param string
     * @param mixed
     *
     * @return string
     */
    public static function textarea($data = '', $value = '', $extra = '')
    {
        return form_textarea($data, $value, $extra);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates an input using the "email" type
     *
     * @param mixed  $data  The field's name or the config array
     * @param mixed  $value The form element's value
     * @param string $extra Any additional attributes to give to the field
     *
     * @return string
     */
    public static function email($data = '', $value = '', $extra = '')
    {
        return static::input('email', $data, $value, $extra);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates an input using the "tel" type
     *
     * @param mixed  $data  The field's name or the config array
     * @param mixed  $value The form element's value
     * @param string $extra Any additional attributes to give to the field
     *
     * @return string
     */
    public static function tel($data = '', $value = '', $extra = '')
    {
        return static::input('tel', $data, $value, $extra);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates an input using the "number" type
     *
     * @param mixed  $data  The field's name or the config array
     * @param mixed  $value The form element's value
     * @param string $extra Any additional attributes to give to the field
     *
     * @return string
     */
    public static function number($data = '', $value = '', $extra = '')
    {
        $defaults = [
            'type'  => 'number',
            'name'  => !is_array($data) ? $data : '',
            'value' => $value,
            'step'  => empty($data['step']) ? 'any' : $data['step'],
        ];

        return '<input ' . _parse_form_attributes($data, $defaults) . $extra . ' />';
    }

    // --------------------------------------------------------------------------

    /**
     * Generates an input using the "url" type
     *
     * @param mixed  $data  The field's name or the config array
     * @param mixed  $value The form element's value
     * @param string $extra Any additional attributes to give to the field
     *
     * @return string
     */
    public static function url($data = '', $value = '', $extra = '')
    {
        return static::input('url', $data, $value, $extra);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates an input using the "date" type
     *
     * @param mixed  $data  The field's name or the config array
     * @param mixed  $value The form element's value
     * @param string $extra Any additional attributes to give to the field
     *
     * @return string
     */
    public static function date($data = '', $value = '', $extra = '')
    {
        return static::input('date', $data, $value, $extra);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates an input using the "time" type
     *
     * @param mixed  $data  The field's name or the config array
     * @param mixed  $value The form element's value
     * @param string $extra Any additional attributes to give to the field
     *
     * @return string
     */
    public static function time($data = '', $value = '', $extra = '')
    {
        return static::input('time', $data, $value, $extra);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates an input using the "date" type
     *
     * @param mixed  $data  The field's name or the config array
     * @param mixed  $value The form element's value
     * @param string $extra Any additional attributes to give to the field
     *
     * @return string
     */
    public static function color($data = '', $value = '', $extra = '')
    {
        return static::input('color', $data, $value, $extra);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates an input using the "datetime" type
     *
     * @param mixed  $data  The field's name or the config array
     * @param mixed  $value The form element's value
     * @param string $extra Any additional attributes to give to the field
     *
     * @return string
     */
    public static function datetime($data = '', $value = '', $extra = '')
    {
        return static::input('datetime-local', $data, $value, $extra);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates an input using the "checkbox" type
     *
     * @param mixed  $data    The field's name or the config array
     * @param mixed  $value   The form element's value
     * @param bool   $checked Whether the input is checked
     * @param string $extra   Any additional attributes to give to the field
     *
     * @return string
     */
    public static function checkbox($data = '', $value = '', $checked = false, $extra = '')
    {
        return form_checkbox($data, $value, $checked, $extra);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates an input using the "radio" type
     *
     * @param mixed  $data    The field's name or the config array
     * @param mixed  $value   The form element's value
     * @param bool   $checked Whether the input is checked
     * @param string $extra   Any additional attributes to give to the field
     *
     * @return string
     */
    public static function radio($data = '', $value = '', $checked = false, $extra = '')
    {
        return form_radio($data, $value, $checked, $extra);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates an input using the "checkbox" type
     *
     * @param mixed  $data    The field's name or the config array
     * @param mixed  $value   The form element's value
     * @param bool   $checked Whether the input is checked
     * @param string $extra   Any additional attributes to give to the field
     *
     * @return string
     */
    public static function boolean($data = '', $value = '', $checked = false, $extra = '')
    {
        return '<div class="form-bool toggle toggle-modern"></div>' .
            static::checkbox($data, $value, $checked, $extra);
    }

    // --------------------------------------------------------------------------

    /**
     * Form Declaration
     * Creates the opening portion of the form, taking into account Secure base URL
     *
     * @param string $action     the URI segments of the form destination
     * @param array  $attributes a key/value pair of attributes
     * @param array  $hidden     a key/value pair hidden data
     *
     * @return  string
     */
    public static function open($action = '', $attributes = '', $hidden = [])
    {
        $CI =& get_instance();

        /** @var Config $oConfig */
        $oConfig = Factory::service('Config');
        /** @var Uri $oUri */
        $oUri = Factory::service('Uri');

        if ($attributes == '') {
            $attributes = 'method="post"';
        }

        // If an action is not a full URL then turn it into one
        if ($action && strpos($action, '://') === false) {
            $action = $oConfig->site_url($action);
        }

        // If no action is provided then set to the current url
        $action || $action = $oConfig->site_url($oUri->uri_string());

        $sForm = '<form action="' . $action . '"';
        $sForm .= _attributes_to_string($attributes);
        $sForm .= '>';

        if (!(bool) preg_match('/method="(.+)"/', $sForm)) {
            $sForm = preg_replace('/>$/', ' method="POST">', $sForm);
        }

        // Add CSRF field if enabled, but leave it out for GET requests and requests to external websites
        $sBaseUrl       = $oConfig->base_url();
        $sSecureBaseUrl = $oConfig->secure_base_url();

        if ($oConfig->item('csrf_protection') === true && !(strpos($action, $sBaseUrl) === false || strpos($sForm, 'method="get"'))) {
            $hidden[$CI->security->get_csrf_token_name()] = $CI->security->get_csrf_hash();
        }

        //  If the secure_base_url is different, then do a check for that domain/url too.
        if ($sBaseUrl != $sSecureBaseUrl) {
            if ($oConfig->item('csrf_protection') === true && !(strpos($action, $sSecureBaseUrl) === false || strpos($sForm, 'method="get"'))) {
                $hidden[$CI->security->get_csrf_token_name()] = $CI->security->get_csrf_hash();
            }
        }

        //  Render any hidden fields
        if (is_array($hidden) && count($hidden) > 0) {
            $sForm .= sprintf("<div style=\"display:none\">%s</div>", form_hidden($hidden));
        }

        return $sForm;
    }

    // --------------------------------------------------------------------------

    /**
     * Creates the closing portion of the form
     *
     * @param string $extra Any extra markup to place after the tag
     *
     * @return string
     */
    public static function close($extra = '')
    {
        return form_close($extra);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates an input using the "submit" type
     *
     * @param mixed  $data  The field's name or the config array
     * @param mixed  $value The form element's value
     * @param string $extra Any additional attributes to give to the field
     *
     * @return string
     */
    public static function submit($data = '', $value = '', $extra = '')
    {
        return form_submit($data, $value, $extra);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates an input using the "button" type
     *
     * @param mixed  $data  The field's name or the config array
     * @param mixed  $value The form element's value
     * @param string $extra Any additional attributes to give to the field
     *
     * @return string
     */
    public static function button($data = '', $value = '', $extra = '')
    {
        return form_button($data, $value, $extra);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the error for a specific form field. This is a helper for the
     * form validation class.
     *
     * @param string $field  The field to check
     * @param string $prefix The prefix to give the error string
     * @param string $suffix The suffix to give the error string
     *
     * @return string
     */
    public static function error($field = '', $prefix = '', $suffix = '')
    {
        return form_error($field, $prefix, $suffix);
    }

    // --------------------------------------------------------------------------

    /**
     * Renders a dropdown menu
     *
     * @param mixed $data
     * @param mixed $options
     * @param mixed $selected
     * @param mixed $extra
     *
     * @return string
     */
    public static function dropdown($data = '', $options = [], $selected = [], $extra = '')
    {
        return form_dropdown($data, $options, $selected, $extra);
    }

    // --------------------------------------------------------------------------

    /**
     * Renders a multi-dropdown menu
     *
     * @param mixed $name
     * @param mixed $options
     * @param mixed $selected
     * @param mixed $extra
     *
     * @return string
     */
    public static function dropdownMultiple($name = '', $options = [], $selected = [], $extra = '')
    {
        return form_multiselect($name, $options, $selected, $extra);
    }

    // --------------------------------------------------------------------------

    /**
     * Renders a file input
     *
     * @param mixed $name
     * @param mixed $options
     * @param mixed $selected
     * @param mixed $extra
     *
     * @return string
     */
    public static function upload($data = '', $value = '', $extra = '')
    {
        return form_upload($data, $value, $extra);
    }
}
