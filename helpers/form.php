<?php

/**
 * This file provides form related helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

use Nails\Factory;

if (!function_exists('form_email')) {

    /**
     * Generates an input using the "email" type
     *
     * @param  mixed  $data  The field's name or the config array
     * @param  mixed  $value The form element's value
     * @param  string $extra Any additional attributes to give to the field
     *
     * @return string
     */
    function form_email($data = '', $value = '', $extra = '')
    {
        $defaults = [
            'type'  => 'email',
            'name'  => !is_array($data) ? $data : '',
            'value' => $value,
        ];

        return '<input ' . _parse_form_attributes($data, $defaults) . $extra . ' />';
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_tel')) {

    /**
     * Generates an input using the "tel" type
     *
     * @param  mixed  $data  The field's name or the config array
     * @param  mixed  $value The form element's value
     * @param  string $extra Any additional attributes to give to the field
     *
     * @return string
     */
    function form_tel($data = '', $value = '', $extra = '')
    {
        $defaults = [
            'type'  => 'tel',
            'name'  => !is_array($data) ? $data : '',
            'value' => $value,
        ];

        return '<input ' . _parse_form_attributes($data, $defaults) . $extra . ' />';
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_number')) {

    /**
     * Generates an input using the "number" type
     *
     * @param  mixed  $data  The field's name or the config array
     * @param  mixed  $value The form element's value
     * @param  string $extra Any additional attributes to give to the field
     *
     * @return string
     */
    function form_number($data = '', $value = '', $extra = '')
    {
        $defaults = [
            'type'  => 'number',
            'name'  => !is_array($data) ? $data : '',
            'value' => $value,
            'step'  => empty($data['step']) ? 'any' : $data['step'],
        ];

        return '<input ' . _parse_form_attributes($data, $defaults) . $extra . ' />';
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_url')) {

    /**
     * Generates an input using the "url" type
     *
     * @param  mixed  $data  The field's name or the config array
     * @param  mixed  $value The form element's value
     * @param  string $extra Any additional attributes to give to the field
     *
     * @return string
     */
    function form_url($data = '', $value = '', $extra = '')
    {
        $defaults = [
            'type'  => 'url',
            'name'  => !is_array($data) ? $data : '',
            'value' => $value,
        ];

        return '<input ' . _parse_form_attributes($data, $defaults) . $extra . ' />';
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_date')) {

    /**
     * Generates an input using the "date" type
     *
     * @param  mixed  $data  The field's name or the config array
     * @param  mixed  $value The form element's value
     * @param  string $extra Any additional attributes to give to the field
     *
     * @return string
     */
    function form_date($data = '', $value = '', $extra = '')
    {
        $defaults = [
            'type'  => 'date',
            'name'  => !is_array($data) ? $data : '',
            'value' => $value,
        ];

        return '<input ' . _parse_form_attributes($data, $defaults) . $extra . ' />';
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_time')) {

    /**
     * Generates an input using the "time" type
     *
     * @param  mixed  $data  The field's name or the config array
     * @param  mixed  $value The form element's value
     * @param  string $extra Any additional attributes to give to the field
     *
     * @return string
     */
    function form_time($data = '', $value = '', $extra = '')
    {
        $defaults = [
            'type'  => 'time',
            'name'  => !is_array($data) ? $data : '',
            'value' => $value,
        ];

        return '<input ' . _parse_form_attributes($data, $defaults) . $extra . ' />';
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_color')) {

    /**
     * Generates an input using the "date" type
     *
     * @param  mixed  $data  The field's name or the config array
     * @param  mixed  $value The form element's value
     * @param  string $extra Any additional attributes to give to the field
     *
     * @return string
     */
    function form_color($data = '', $value = '', $extra = '')
    {
        $defaults = [
            'type'  => 'color',
            'name'  => !is_array($data) ? $data : '',
            'value' => $value,
        ];

        return '<input ' . _parse_form_attributes($data, $defaults) . $extra . ' />';
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_datetime')) {

    /**
     * Generates an input using the "datetime" type
     *
     * @param  mixed  $data  The field's name or the config array
     * @param  mixed  $value The form element's value
     * @param  string $extra Any additional attributes to give to the field
     *
     * @return string
     */
    function form_datetime($data = '', $value = '', $extra = '')
    {
        $defaults = [
            'type'  => 'datetime-local',
            'name'  => !is_array($data) ? $data : '',
            'value' => $value,
        ];

        return '<input ' . _parse_form_attributes($data, $defaults) . $extra . ' />';
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_open')) {

    /**
     * Form Declaration
     * Creates the opening portion of the form, taking into account Secure base URL
     *
     * @param   string  the URI segments of the form destination
     * @param   array   a key/value pair of attributes
     * @param   array   a key/value pair hidden data
     *
     * @return  string
     */
    function form_open($action = '', $attributes = '', $hidden = [])
    {
        $CI =& get_instance();

        $oConfig = Factory::service('Config');
        $oUri    = Factory::service('Uri');

        if ($attributes == '') {
            $attributes = 'method="post"';
        }

        // If an action is not a full URL then turn it into one
        if ($action && strpos($action, '://') === false) {
            $action = $oConfig->site_url($action);
        }

        // If no action is provided then set to the current url
        $action || $action = $oConfig->site_url($oUri->uri_string());

        $form = '<form action="' . $action . '"';

        $form .= _attributes_to_string($attributes, true);

        $form .= '>';

        // Add CSRF field if enabled, but leave it out for GET requests and requests to external websites
        $_base_url        = $oConfig->base_url();
        $_secure_base_url = $oConfig->secure_base_url();

        if ($oConfig->item('csrf_protection') === true && !(strpos($action, $_base_url) === false || strpos($form, 'method="get"'))) {
            $hidden[$CI->security->get_csrf_token_name()] = $CI->security->get_csrf_hash();
        }

        //  If the secure_base_url is different, then do a check for that domain/url too.
        if ($_base_url != $_secure_base_url) {
            if ($oConfig->item('csrf_protection') === true && !(strpos($action, $_secure_base_url) === false || strpos($form, 'method="get"'))) {
                $hidden[$CI->security->get_csrf_token_name()] = $CI->security->get_csrf_hash();
            }
        }

        //  Render any hidden fields
        if (is_array($hidden) && count($hidden) > 0) {
            $form .= sprintf("<div style=\"display:none\">%s</div>", form_hidden($hidden));
        }

        return $form;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field')) {

    /**
     * Generates a form field
     *
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string        The form HTML
     */
    function form_field($field, $tip = '')
    {
        //  Set var defaults
        $_field_id          = isset($field['id']) ? $field['id'] : null;
        $_field_type        = isset($field['type']) ? $field['type'] : 'text';
        $_field_oddeven     = isset($field['oddeven']) ? $field['oddeven'] : null;
        $_field_key         = isset($field['key']) ? $field['key'] : null;
        $_field_label       = isset($field['label']) ? $field['label'] : null;
        $_field_default     = isset($field['default']) ? $field['default'] : null;
        $_field_sub_label   = isset($field['sub_label']) ? $field['sub_label'] : null;
        $_field_required    = isset($field['required']) ? $field['required'] : false;
        $_field_placeholder = isset($field['placeholder']) ? $field['placeholder'] : null;
        $_field_readonly    = isset($field['readonly']) ? $field['readonly'] : false;
        $_field_error       = isset($field['error']) ? $field['error'] : false;
        $_field_class       = isset($field['class']) ? $field['class'] : '';
        $_field_data        = isset($field['data']) ? $field['data'] : [];
        $_field_info        = isset($field['info']) ? $field['info'] : false;
        $_field_info_class  = isset($field['info_class']) ? $field['info_class'] : false;
        $_field_max_length  = isset($field['max_length']) ? (int) $field['max_length'] : null;
        $_field_tip         = isset($field['tip']) ? $field['tip'] : $tip;

        $_tip          = [];
        $_tip['class'] = is_array($_field_tip) && isset($_field_tip['class']) ? $_field_tip['class'] : 'fa fa-question-circle fa-lg tip';
        $_tip['rel']   = is_array($_field_tip) && isset($_field_tip['rel']) ? $_field_tip['rel'] : 'tipsy-left';
        $_tip['title'] = is_array($_field_tip) && isset($_field_tip['title']) ? $_field_tip['title'] : null;
        $_tip['title'] = is_string($_field_tip) ? $_field_tip : $_tip['title'];

        $_field_id_top = $_field_id ? 'id="field-' . $_field_id . '"' : '';
        $_error        = form_error($_field_key) || $_field_error ? 'error' : '';
        $_error_class  = $_error ? 'error' : '';
        $_readonly     = $_field_readonly ? 'readonly="readonly"' : '';
        $_readonly_cls = $_field_readonly ? 'readonly' : '';

        // --------------------------------------------------------------------------

        //  Is the label required?
        $_field_label .= $_field_required ? '*' : '';

        //  Prep sublabel
        $_field_sub_label = $_field_sub_label ? '<small>' . $_field_sub_label . '</small>' : '';

        //  Has the field got a tip?
        $_tipclass = $_tip['title'] ? 'with-tip' : '';
        $_tip      = $_tip['title'] ? '<b class="' . $_tip['class'] . '" rel="' . $_tip['rel'] . '" title="' . htmlentities($_tip['title'], ENT_QUOTES) . '"></b>' : '';

        // --------------------------------------------------------------------------

        //  Prep the field's attributes
        $_attr = '';

        //  Does the field have an id?
        $_attr .= $_field_id ? 'id="' . $_field_id . '" ' : '';

        //  Any data attributes?
        foreach ($_field_data as $attr => $value) {

            $_attr .= ' data-' . $attr . '="' . $value . '"';
        }

        // --------------------------------------------------------------------------

        //  Generate the field's HTML
        $sFieldAttr = $_attr;
        $sFieldAttr .= ' class="field-input ' . $_field_class . '" ';
        $sFieldAttr .= 'placeholder="' . htmlentities($_field_placeholder, ENT_QUOTES) . '" ';
        $sFieldAttr .= $_readonly;

        switch ($_field_type) {

            case 'password':
            case 'email':
            case 'number':
            case 'url':
            case 'time':
            case 'color':
            case 'tel':

                $sMethodName = 'form_' . $_field_type;
                $_field_html = $sMethodName(
                    $_field_key,
                    set_value($_field_key, $_field_default),
                    $sFieldAttr
                );
                break;

            case 'wysiwyg':
            case 'textarea':

                if ($_field_type == 'wysiwyg') {

                    $_field_type  = 'textarea';
                    $_field_class .= ' wysiwyg';
                }

                $_field_html = form_textarea(
                    $_field_key,
                    set_value($_field_key, $_field_default),
                    $sFieldAttr
                );
                break;

            case 'upload':
            case 'file':

                $_field_html = form_upload(
                    $_field_key,
                    null,
                    $sFieldAttr
                );
                break;

            case 'text':
            default:

                $_field_html = form_input(
                    $_field_key,
                    set_value($_field_key, $_field_default),
                    $sFieldAttr
                );
                break;
        }

        if (!empty($_field_max_length)) {
            switch ($_field_type) {

                case 'password':
                case 'email':
                case 'number':
                case 'url':
                case 'textarea':
                case 'text':
                    $_max_length_html = '<small class="char-count" data-max-length="' . $_field_max_length . '">Max Length: ' . $_field_max_length . '</small>';
                    break;
                default:
                    $_max_length_html = '';
                    break;
            }
        } else {
            $_max_length_html = '';
        }

        //  Download original file, if type is file and original is available
        if (($_field_type == 'file' || $_field_type == 'upload') && $_field_default) :

            $_field_html .= '<span class="file-download">';

            $_ext = end(explode('.', $_field_default));

            switch ($_ext) {

                case 'jpg' :
                case 'png' :
                case 'gif' :

                    $_field_html .= 'Download: ' . anchor(cdnServe($_field_default), img(cdnCrop($_field_default, 35, 35)), 'class="fancybox"');
                    break;

                // --------------------------------------------------------------------------

                default :

                    $_field_html .= anchor(cdnServe($_field_default, true), 'Download', 'class="btn btn-xs btn-primary" target="_blank"');
                    break;
            }

            $_field_html .= '</span>';

        endif;

        // --------------------------------------------------------------------------

        //  Errors
        if ($_error && $_field_error) :

            $_error = '<span class="alert alert-danger">' . $_field_error . '</span>';

        elseif ($_error) :

            $_error = form_error($_field_key, '<span class="alert alert-danger">', '</span>');

        endif;

        // --------------------------------------------------------------------------

        //  info block
        $info_block = $_field_info ? '<small class="info ' . $_field_info_class . '">' . $_field_info . '</small>' : '';

        // --------------------------------------------------------------------------

        $_out = <<<EOT

    <div class="field $_error_class $_field_oddeven $_readonly_cls $_field_type" $_field_id_top>
        <label>
            <span class="label">
                $_field_label
                $_field_sub_label
            </span>
            <span class="input $_tipclass">
                $_field_html
                $_max_length_html
                $_tip
                $_error
                $info_block
            <span>
        </label>
    </div>

EOT;

        // --------------------------------------------------------------------------

        return $_out;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_email')) {

    /**
     * Generates a form field using the "email" input type
     *
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string        The form HTML
     */
    function form_field_email($field, $tip = '')
    {
        $field['type'] = 'email';
        return form_field($field, $tip);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_number')) {

    /**
     * Generates a form field using the "number" input type
     *
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string        The form HTML
     */
    function form_field_number($field, $tip = '')
    {
        $field['type'] = 'number';
        return form_field($field, $tip);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_url')) {

    /**
     * Generates a form field using the "url" input type
     *
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string        The form HTML
     */
    function form_field_url($field, $tip = '')
    {
        $field['type'] = 'url';
        return form_field($field, $tip);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_tel')) {

    /**
     * Generates a form field using the "url" input type
     *
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string        The form HTML
     */
    function form_field_tel($field, $tip = '')
    {
        $field['type'] = 'tel';
        return form_field($field, $tip);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_color')) {

    /**
     * Generates a form field using the "color" input type
     *
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string        The form HTML
     */
    function form_field_color($field, $tip = '')
    {
        $field['type'] = 'color';
        return form_field($field, $tip);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_password')) {

    /**
     * Generates a form field using the "password" input type
     *
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string        The form HTML
     */
    function form_field_password($field, $tip = '')
    {
        $field['type'] = 'password';
        return form_field($field, $tip);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_textarea')) {

    /**
     * Generates a form field using the "textarea" input type
     *
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string        The form HTML
     */
    function form_field_textarea($field, $tip = '')
    {
        $field['type'] = 'textarea';
        return form_field($field, $tip);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_wysiwyg')) {

    /**
     * Generates a form field using the "textarea" input type, and sets it's class
     * to "wysiwyg"
     *
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string        The form HTML
     */
    function form_field_wysiwyg($field, $tip = '')
    {
        $field['type'] = 'textarea';

        if (isset($field['class'])) {

            $field['class'] .= ' wysiwyg';
        } else {

            $field['class'] = 'wysiwyg';
        }

        return form_field($field, $tip);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_text')) {

    /**
     * Generates a form field using the "text" input type
     *
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string        The form HTML
     */
    function form_field_text($field, $tip = '')
    {
        $field['type'] = 'text';
        return form_field($field, $tip);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_cdn_object_picker')) {

    /**
     * Generates a form field containing the media manager to select a file.
     * @todo  when form builder is updated, ensure that other things can create custom field types
     *
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string        The form HTML
     */
    function form_field_cdn_object_picker($field, $tip = '')
    {
        //  Set var defaults
        $_field_id         = isset($field['id']) ? $field['id'] : null;
        $_field_type       = isset($field['type']) ? $field['type'] : 'text';
        $_field_oddeven    = isset($field['oddeven']) ? $field['oddeven'] : null;
        $_field_key        = isset($field['key']) ? $field['key'] : null;
        $_field_label      = isset($field['label']) ? $field['label'] : null;
        $_field_default    = isset($field['default']) ? $field['default'] : null;
        $_field_sub_label  = isset($field['sub_label']) ? $field['sub_label'] : null;
        $_field_required   = isset($field['required']) ? $field['required'] : false;
        $_field_readonly   = isset($field['readonly']) ? $field['readonly'] : false;
        $_field_error      = isset($field['error']) ? $field['error'] : false;
        $_field_class      = isset($field['class']) ? $field['class'] : '';
        $_field_data       = isset($field['data']) ? $field['data'] : [];
        $_field_info       = isset($field['info']) ? $field['info'] : false;
        $_field_info_class = isset($field['info_class']) ? $field['info_class'] : false;
        $_field_tip        = isset($field['tip']) ? $field['tip'] : $tip;

        //  CDN Specific
        $_field_bucket = isset($field['bucket']) ? $field['bucket'] : null;

        $_tip          = [];
        $_tip['class'] = is_array($_field_tip) && isset($_field_tip['class']) ? $_field_tip['class'] : 'fa fa-question-circle fa-lg tip';
        $_tip['rel']   = is_array($_field_tip) && isset($_field_tip['rel']) ? $_field_tip['rel'] : 'tipsy-left';
        $_tip['title'] = is_array($_field_tip) && isset($_field_tip['title']) ? $_field_tip['title'] : null;
        $_tip['title'] = is_string($_field_tip) ? $_field_tip : $_tip['title'];

        $_field_id_top = $_field_id ? 'id="field-' . $_field_id . '"' : '';
        $_error        = form_error($_field_key) || $_field_error ? 'error' : '';
        $_error_class  = $_error ? 'error' : '';
        $_readonly     = $_field_readonly ? 'readonly="readonly"' : '';
        $_readonly_cls = $_field_readonly ? 'readonly' : '';

        // --------------------------------------------------------------------------

        //  Is the label required?
        $_field_label .= $_field_required ? '*' : '';

        //  Prep sublabel
        $_field_sub_label = $_field_sub_label ? '<small>' . $_field_sub_label . '</small>' : '';

        //  Has the field got a tip?
        $_tipclass = $_tip['title'] ? 'with-tip' : '';
        $_tip      = $_tip['title'] ? '<b class="' . $_tip['class'] . '" rel="' . $_tip['rel'] . '" title="' . htmlentities($_tip['title'], ENT_QUOTES) . '"></b>' : '';

        // --------------------------------------------------------------------------

        //  Prep the field's attributes
        $_attr = '';

        //  Does the field have an id?
        $_attr .= $_field_id ? 'id="' . $_field_id . '" ' : '';

        //  Any data attributes?
        foreach ($_field_data as $attr => $value) {

            $_attr .= ' data-' . $attr . '="' . $value . '"';
        }

        // --------------------------------------------------------------------------

        //  Generate the field's HTML
        $sFieldAttr = $_attr;
        $sFieldAttr .= ' class="' . $_field_class . '" ';

        $_field_html = cdnObjectPicker(
            $_field_key,
            $_field_bucket,
            set_value($_field_key, $_field_default),
            $sFieldAttr,
            '',
            !empty($_readonly)
        );

        // --------------------------------------------------------------------------

        //  Errors
        if ($_error && $_field_error) {

            $_error = '<span class="alert alert-danger">' . $_field_error . '</span>';
        } elseif ($_error) {

            $_error = form_error($_field_key, '<span class="alert alert-danger">', '</span>');
        }

        // --------------------------------------------------------------------------

        //  info block
        $info_block = $_field_info ? '<small class="info ' . $_field_info_class . '">' . $_field_info . '</small>' : '';

        // --------------------------------------------------------------------------

        $_out = <<<EOT

    <div class="field $_error_class $_field_oddeven $_readonly_cls $_field_type" $_field_id_top>
        <label>
            <span class="label">
                $_field_label
                $_field_sub_label
            </span>
            <span class="input $_tipclass">
                $_field_html
                $_tip
                $_error
                $info_block
            <span>
        </label>
    </div>

EOT;

        return $_out;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_cdn_object_picker_multi_with_label')) {

    /**
     * Generates a form field containing multiple object pickers
     * @todo  when form builder is updated, ensure that other things can create custom field types
     *
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string        The form HTML
     */
    function form_field_cdn_object_picker_multi($field, $tip = '')
    {
        //  Set var defaults
        $_field_id         = isset($field['id']) ? $field['id'] : null;
        $_field_type       = isset($field['type']) ? $field['type'] : 'text';
        $_field_oddeven    = isset($field['oddeven']) ? $field['oddeven'] : null;
        $_field_key        = isset($field['key']) ? $field['key'] : null;
        $_field_label      = isset($field['label']) ? $field['label'] : null;
        $_field_default    = isset($field['default']) ? $field['default'] : [];
        $_field_bucket     = isset($field['bucket']) ? $field['bucket'] : null;
        $_field_sub_label  = isset($field['sub_label']) ? $field['sub_label'] : null;
        $_field_required   = isset($field['required']) ? $field['required'] : false;
        $_field_readonly   = isset($field['readonly']) ? $field['readonly'] : false;
        $_field_error      = isset($field['error']) ? $field['error'] : false;
        $_field_class      = isset($field['class']) ? $field['class'] : '';
        $_field_data       = isset($field['data']) ? $field['data'] : [];
        $_field_info       = isset($field['info']) ? $field['info'] : false;
        $_field_info_class = isset($field['info_class']) ? $field['info_class'] : false;
        $_field_tip        = isset($field['tip']) ? $field['tip'] : $tip;

        //  CDN Specific
        $_field_bucket = isset($field['bucket']) ? $field['bucket'] : null;

        $_tip          = [];
        $_tip['class'] = is_array($_field_tip) && isset($_field_tip['class']) ? $_field_tip['class'] : 'fa fa-question-circle fa-lg tip';
        $_tip['rel']   = is_array($_field_tip) && isset($_field_tip['rel']) ? $_field_tip['rel'] : 'tipsy-left';
        $_tip['title'] = is_array($_field_tip) && isset($_field_tip['title']) ? $_field_tip['title'] : null;
        $_tip['title'] = is_string($_field_tip) ? $_field_tip : $_tip['title'];

        $_field_id_top = $_field_id ? 'id="field-' . $_field_id . '"' : '';
        $_error        = form_error($_field_key) || $_field_error ? 'error' : '';
        $_error_class  = $_error ? 'error' : '';
        $_readonly     = $_field_readonly ? 'readonly="readonly"' : '';
        $_readonly_cls = $_field_readonly ? 'readonly' : '';

        // --------------------------------------------------------------------------

        //  Is the label required?
        $_field_label .= $_field_required ? '*' : '';

        //  Prep sublabel
        $_field_sub_label = $_field_sub_label ? '<small>' . $_field_sub_label . '</small>' : '';

        //  Has the field got a tip?
        $_tipclass = $_tip['title'] ? 'with-tip' : '';
        $_tip      = $_tip['title'] ? '<b class="' . $_tip['class'] . '" rel="' . $_tip['rel'] . '" title="' . htmlentities($_tip['title'], ENT_QUOTES) . '"></b>' : '';

        // --------------------------------------------------------------------------

        //  Prep the field's attributes
        $_attr = '';

        //  Does the field have an id?
        $_attr .= $_field_id ? 'id="' . $_field_id . '" ' : '';

        //  Any data attributes?
        foreach ($_field_data as $attr => $value) {

            $_attr .= ' data-' . $attr . '="' . $value . '"';
        }

        // --------------------------------------------------------------------------

        //  Generate the field's HTML
        $sFieldAttr = $_attr;
        $sFieldAttr .= ' class="' . $_field_class . '" ';
        $sFieldAttr .= $_readonly;

        // Small hack to inject data-bind into the input.
        $_field_html = cdnObjectPicker(
            '" data-bind="attr:{name: \'download[\' + \$index() + \'][download_id]\'}, value: download_id"',
            $_field_bucket
        );

        // --------------------------------------------------------------------------

        //  Errors
        if ($_error && $_field_error) {

            $_error = '<span class="alert alert-danger">' . $_field_error . '</span>';
        } elseif ($_error) {

            $_error = form_error($_field_key, '<span class="alert alert-danger">', '</span>');
        }

        // --------------------------------------------------------------------------

        //  info block
        $info_block = $_field_info ? '<small class="info ' . $_field_info_class . '">' . $_field_info . '</small>' : '';

        // --------------------------------------------------------------------------

        //  Start generating the markup
        $_field_html_id     = '<input type="hidden" name="' . $_field_key . '[{{index}}][id]" value="{{id}}">';
        $_field_html_object = cdnObjectPicker(
            $_field_key . '[{{index}}][object_id]',
            $_field_bucket,
            '{{object_id}}',
            'data-index="{{index}}"'
        );
        $_field_html_remove = '<a href="#" class="js-cdn-multi-action-remove" data-index="{{index}}">';
        $_field_html_remove .= '<b class="fa fa-lg fa-times-circle text-danger"></b>';
        $_field_html_remove .= '</a>';

        //  JS template
        $jsTpl = <<<EOT
            <tr>
                <td>
                    $_field_html_id
                    $_field_html_object
                </td>
                <td class="text-center">
                    $_field_html_remove
                </td>
            </tr>
EOT;

        //  Generate the initial objects
        $_default_html = '';
        $oMustache     = Factory::service('Mustache');

        if (!empty($_POST)) {

            if (strpos($_field_key, '[') !== false) {

                preg_match_all('/(.+?)\[([a-zA-Z0-9_\]\[]+)\]/', $_field_key, $aKeyBits);

                $aKeyBits[2] = explode('][', $aKeyBits[2][0]);
                $aKeyBits    = array_merge($aKeyBits[1], $aKeyBits[2]);
                $sPostKey    = '$_POST';

                foreach ($aKeyBits as $sKeyBit) {
                    $sPostKey .= '[\'' . $sKeyBit . '\']';
                }

                //  @todo find a way to not be evil
                $aValues = eval('return !empty(' . $sPostKey . ') ? ' . $sPostKey . ' : array();');
            } else {

                $aValues = $_POST[$_field_key];
            }
        } else {

            $aValues = $_field_default;
        }

        for ($i = 0; $i < count($aValues); $i++) {

            $aValues[$i]['index'] = $i;
            $_default_html        .= $oMustache->render($jsTpl, $aValues[$i]);
        }

        $sFieldAttr .= ' data-defaults="' . htmlentities(json_encode($aValues)) . '"';

        // --------------------------------------------------------------------------

        $_out = <<<EOT
            <div class="field cdn-multi cdn-multi-with-label $_error_class $_field_oddeven $_readonly_cls $_field_type" $_field_id_top $sFieldAttr>
                <div>
                    <span class="label">
                        $_field_label
                        $_field_sub_label
                    </span>
                    <span class="input $_tipclass">
                        <table class="">
                            <thead>
                                <th width="*">File</th>
                                <th width="10"></th>
                            </thead>
                            <tbody class="js-row-target">
                                $_default_html
                            </tbody>
                            <tbody>
                                <tr>
                                    <td colspan="3">
                                        <button type="button" class="btn btn-xs btn-success js-cdn-multi-action-add">
                                            <span class="fa fa-plus"></span> Add download
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        $_tip
                        $_error
                        $info_block
                    <span>
                </div>
                <script type="text/x-template" class="js-row-tpl">
                    $jsTpl
                </script>
            </div>
EOT;

        return $_out;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_cdn_object_picker_multi_with_label')) {

    /**
     * Generates a form field containing multiple object pickers
     * @todo  when form builder is updated, ensure that other things can create custom field types
     *
     * @param  array  $aConfig The config array
     * @param  string $sTip    An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string        The form HTML
     */
    function form_field_cdn_object_picker_multi_with_label($aConfig, $sTip = '')
    {
        //  Set var defaults
        $sFieldId        = getFromArray('id', $aConfig, null);
        $sFieldType      = getFromArray('type', $aConfig, 'text');
        $sFieldOddEven   = getFromArray('oddeven', $aConfig, null);
        $sFieldKey       = getFromArray('key', $aConfig, null);
        $sFieldLabel     = getFromArray('label', $aConfig, null);
        $sFieldDefault   = getFromArray('default', $aConfig, []);
        $sFieldSubLabel  = getFromArray('sub_label', $aConfig, null);
        $sFieldRequired  = getFromArray('required', $aConfig, false);
        $sFieldReadonly  = getFromArray('readonly', $aConfig, false);
        $sFieldError     = getFromArray('error', $aConfig, false);
        $sFieldClass     = getFromArray('class', $aConfig, '');
        $sFieldData      = getFromArray('data', $aConfig, []);
        $sFieldInfo      = getFromArray('info', $aConfig, false);
        $sFieldInfoClass = getFromArray('info_class', $aConfig, false);
        $sFieldTip       = getFromArray('tip', $aConfig, $sTip);

        $sFieldObjectKey        = getFromArray('object_key', $aConfig, 'object_id');
        $sFieldTableLabelObject = getFromArray('table_label_object', $aConfig, 'File');

        $sFieldLabelKey        = getFromArray('label_key', $aConfig, 'label');
        $sFieldTableLabelLabel = getFromArray('table_label_label', $aConfig, 'Label');

        //  CDN Specific
        $sFieldBucket = isset($aConfig['bucket']) ? $aConfig['bucket'] : null;

        $aTip = [
            'class' => getFromArray('class', (array) $sFieldTip, 'fa fa-question-circle fa-lg tip'),
            'rel'   => getFromArray('rel', (array) $sFieldTip, 'tipsy-left'),
            'title' => getFromArray('title', (array) $sFieldTip, $sFieldTip),
        ];

        $sFieldIdTop    = $sFieldId ? 'id="field-' . $sFieldId . '"' : '';
        $sError         = form_error($sFieldKey) || $sFieldError ? 'error' : '';
        $sErrorClass    = $sError ? 'error' : '';
        $sReadonly      = $sFieldReadonly ? 'readonly="readonly"' : '';
        $sReadonlyClass = $sFieldReadonly ? 'readonly' : '';

        // --------------------------------------------------------------------------

        //  Is the label required?
        $sFieldLabel .= $sFieldRequired ? '*' : '';

        //  Prep sublabel
        $sFieldSubLabel = $sFieldSubLabel ? '<small>' . $sFieldSubLabel . '</small>' : '';

        //  Has the field got a tip?
        $sTipClass = $aTip['title'] ? 'with-tip' : '';
        $sTip      = $aTip['title'] ? '<b class="' . $aTip['class'] . '" rel="' . $aTip['rel'] . '" title="' . htmlentities($aTip['title'], ENT_QUOTES) . '"></b>' : '';

        // --------------------------------------------------------------------------

        //  Prep the field's attributes
        $sFieldAttr = '';

        //  Does the field have an id?
        $sFieldAttr .= $sFieldId ? 'id="' . $sFieldId . '" ' : '';

        //  Any data attributes?
        foreach ($sFieldData as $attr => $value) {
            $sFieldAttr .= ' data-' . $attr . '="' . $value . '"';
        }

        // --------------------------------------------------------------------------

        //  Generate the field's HTML
        $sFieldAttr .= ' class="' . $sFieldClass . '" ';
        $sFieldAttr .= $sReadonly;

        // --------------------------------------------------------------------------

        //  Errors
        if ($sError && $sFieldError) {
            $sError = '<span class="alert alert-danger">' . $sFieldError . '</span>';
        } elseif ($sError) {
            $sError = form_error($sFieldKey, '<span class="alert alert-danger">', '</span>');
        }

        // --------------------------------------------------------------------------

        //  info block
        $sInfoBlock = $sFieldInfo ? '<small class="info ' . $sFieldInfoClass . '">' . $sFieldInfo . '</small>' : '';

        // --------------------------------------------------------------------------

        //  Start generating the markup
        $sFieldHtmlId     = '<input type="hidden" name="' . $sFieldKey . '[{{index}}][id]" value="{{id}}">';
        $sFieldHtmlObject = cdnObjectPicker(
            $sFieldKey . '[{{index}}][' . $sFieldObjectKey . ']',
            $sFieldBucket,
            '{{' . $sFieldObjectKey . '}}',
            'data-index="{{index}}"'
        );
        $sFieldHtmlLabel  = '<input type="text" name="' . $sFieldKey . '[{{index}}][' . $sFieldLabelKey . ']" value="{{' . $sFieldLabelKey . '}}" data-index="{{index}}" class="js-label">';
        $sFieldHtmlRemove = '<a href="#" class="js-cdn-multi-action-remove" data-index="{{index}}">';
        $sFieldHtmlRemove .= '<b class="fa fa-lg fa-times-circle text-danger"></b>';
        $sFieldHtmlRemove .= '</a>';

        //  JS template
        $jsTpl = <<<EOT
            <tr>
                <td>
                    $sFieldHtmlId
                    $sFieldHtmlObject
                </td>
                <td>
                    $sFieldHtmlLabel
                </td>
                <td class="text-center">
                    $sFieldHtmlRemove
                </td>
            </tr>
EOT;

        //  Generate the initial objects
        $sDefaultHtml = '';
        $oMustache    = Factory::service('Mustache');

        if (!empty($_POST)) {

            if (strpos($sFieldKey, '[') !== false) {

                preg_match_all('/(.+?)\[([a-zA-Z0-9_\]\[]+)\]/', $sFieldKey, $aKeyBits);

                $aKeyBits[2] = explode('][', $aKeyBits[2][0]);
                $aKeyBits    = array_merge($aKeyBits[1], $aKeyBits[2]);
                $sPostKey    = '$_POST';

                foreach ($aKeyBits as $sKeyBit) {
                    $sPostKey .= '[\'' . $sKeyBit . '\']';
                }

                //  @todo find a way to not be evil
                $aValues = eval('return !empty(' . $sPostKey . ') ? ' . $sPostKey . ' : array();');
            } else {
                $aValues = isset($_POST[$sFieldKey]) ? $_POST[$sFieldKey] : [];
            }

        } else {
            $aValues = $sFieldDefault;
        }

        for ($i = 0; $i < count($aValues); $i++) {
            $aValues[$i]          = (array) $aValues[$i];
            $aValues[$i]['index'] = $i;
            $sDefaultHtml         .= $oMustache->render($jsTpl, $aValues[$i]);
        }

        $sFieldAttr .= ' data-defaults="' . htmlentities(json_encode($aValues)) . '"';

        // --------------------------------------------------------------------------

        $_out = <<<EOT
            <div class="field cdn-multi cdn-multi-with-label $sErrorClass $sFieldOddEven $sReadonlyClass $sFieldType" $sFieldIdTop $sFieldAttr>
                <div>
                    <span class="label">
                        $sFieldLabel
                        $sFieldSubLabel
                    </span>
                    <span class="input $sTipClass">
                        <table class="">
                            <thead>
                                <th width="300">$sFieldTableLabelObject</th>
                                <th width="*">$sFieldTableLabelLabel</th>
                                <th width="10"></th>
                            </thead>
                            <tbody class="js-row-target">
                                $sDefaultHtml
                            </tbody>
                            <tbody>
                                <tr>
                                    <td colspan="3">
                                        <button type="button" class="btn btn-xs btn-success js-cdn-multi-action-add">
                                            <span class="fa fa-plus"></span> Add download
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        $sTip
                        $sError
                        $sInfoBlock
                    <span>
                </div>
                <script type="text/x-template" class="js-row-tpl">
                    $jsTpl
                </script>
            </div>
EOT;

        return $_out;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_mm')) {

    /**
     * Generates a form field containing the media manager to select a file.
     * @deprecated Use form_field_cdn_object_picker instead
     *
     * @param  array  $aField The config array
     * @param  string $sTip   An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string         The form HTML
     */
    function form_field_mm($aField, $sTip = '')
    {
        return form_field_cdn_object_picker($aField, $sTip);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_mm_image')) {

    /**
     * Generates a form field containing the media manager to select an image
     * @deprecated Use form_field_cdn_object_picker instead
     *
     * @param  array  $aField The config array
     * @param  string $sTip   An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string         The form HTML
     */
    function form_field_mm_image($aField, $sTip = '')
    {
        return form_field_cdn_object_picker($aField, $sTip);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_date')) {

    /**
     * Generates a form field for dates
     *
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string        The form HTML
     */
    function form_field_date($field, $tip = '')
    {
        $_field                = $field;
        $_field['type']        = 'date';
        $_field['class']       = isset($field['class']) ? $field['class'] . ' date' : 'date';
        $_field['placeholder'] = 'YYYY-MM-DD';

        return form_field($_field, $tip);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_time')) {

    /**
     * Generates a form field for times
     *
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string        The form HTML
     */
    function form_field_time($field, $tip = '')
    {
        $_field                = $field;
        $_field['type']        = 'time';
        $_field['class']       = isset($field['class']) ? $field['class'] . ' date' : 'date';
        $_field['placeholder'] = 'HH:MM';

        return form_field($_field, $tip);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_datetime')) {

    /**
     * Generates a form field for datetimes
     *
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string        The form HTML
     */
    function form_field_datetime($field, $tip = '')
    {
        $_field                = $field;
        $_field['type']        = 'datetime';
        $_field['class']       = isset($field['class']) ? $field['class'] . ' datetime' : 'datetime';
        $_field['placeholder'] = 'YYYY-MM-DD HH:mm:ss';

        return form_field($_field, $tip);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_dropdown')) {

    /**
     * Generates a form field using the "select" input type
     *
     * @param  array  $field   The config array
     * @param  array  $options The options to use for the dropdown (DEPRECATED: use $field['options'] instead)
     * @param  string $tip     An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string          The form HTML
     */
    function form_field_dropdown($field, $options = null, $tip = '')
    {
        //  Set var defaults
        $_field                     = [];
        $_field['id']               = isset($field['id']) ? $field['id'] : null;
        $_field['type']             = isset($field['type']) ? $field['type'] : 'text';
        $_field['oddeven']          = isset($field['oddeven']) ? $field['oddeven'] : null;
        $_field['key']              = isset($field['key']) ? $field['key'] : null;
        $_field['label']            = isset($field['label']) ? $field['label'] : null;
        $_field['default']          = isset($field['default']) ? $field['default'] : null;
        $_field['sub_label']        = isset($field['sub_label']) ? $field['sub_label'] : null;
        $_field['required']         = isset($field['required']) ? $field['required'] : false;
        $_field['placeholder']      = isset($field['placeholder']) ? $field['placeholder'] : null;
        $_field['class']            = isset($field['class']) ? $field['class'] : false;
        $_field['style']            = isset($field['style']) ? $field['style'] : false;
        $_field['readonly']         = isset($field['readonly']) ? $field['readonly'] : false;
        $_field['data']             = isset($field['data']) ? $field['data'] : [];
        $_field['disabled_options'] = isset($field['disabled_options']) ? $field['disabled_options'] : [];
        $_field['info']             = isset($field['info']) ? $field['info'] : [];
        $_field['info_class']       = isset($field['info_class']) ? $field['info_class'] : false;
        $_field['tip']              = isset($field['tip']) ? $field['tip'] : $tip;
        $_field['options']          = isset($field['options']) ? $field['options'] : $options;

        $_tip          = [];
        $_tip['class'] = is_array($_field['tip']) && isset($_field['tip']['class']) ? $_field['tip']['class'] : 'fa fa-question-circle fa-lg tip';
        $_tip['rel']   = is_array($_field['tip']) && isset($_field['tip']['rel']) ? $_field['tip']['rel'] : 'tipsy-left';
        $_tip['title'] = is_array($_field['tip']) && isset($_field['tip']['title']) ? $_field['tip']['title'] : null;
        $_tip['title'] = is_string($_field['tip']) ? $_field['tip'] : $_field['title'];

        $_field_id_top = $_field['id'] ? 'id="field-' . $_field['id'] . '"' : '';
        $_error        = form_error($_field['key']) ? 'error' : '';
        $_readonly     = $_field['readonly'] ? 'disabled="disabled"' : '';
        $_readonly_cls = $_field['readonly'] ? 'readonly' : '';

        // --------------------------------------------------------------------------

        $_out = '<div class="field dropdown ' . $_error . ' ' . $_readonly_cls . ' ' . $_field['oddeven'] . '" ' . $_field_id_top . '>';
        $_out .= '<label>';

        //  Label
        $_out .= '<span class="label">';
        $_out .= $_field['label'];
        $_out .= $_field['required'] ? '*' : '';
        $_out .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';
        $_out .= '</span>';

        // --------------------------------------------------------------------------

        //  Field
        $_withtip = $_tip['title'] ? 'with-tip' : '';
        $_out     .= '<span class="input ' . $_withtip . '">';

        //  Does the field have an id?
        $_field['id'] = $_field['id'] ? 'id="' . $_field['id'] . '" ' : '';

        //  Any data attributes?
        $_data = '';
        foreach ($_field['data'] as $attr => $value) :

            $_data .= ' data-' . $attr . '="' . $value . '"';

        endforeach;

        //  Get the selected options
        $_selected = set_value($_field['key'], $_field['default']);

        //  Build the select
        $_placeholder = null !== $_field['placeholder'] ? 'data-placeholder="' . htmlentities($_field['placeholder'], ENT_QUOTES) . '"' : '';
        $_out         .= '<select name="' . $_field['key'] . '" class="' . $_field['class'] . '" style="' . $_field['style'] . '" ' . $_field['id'] . ' ' . $_readonly . $_placeholder . $_data . '>';

        foreach ($_field['options'] as $value => $label) :

            if (is_array($label)) :

                $_out .= '<optgroup label="' . $value . '">';
                foreach ($label as $k => $v) :

                    //  Selected?
                    $_checked = $k == $_selected ? ' selected="selected"' : '';

                    //  Disabled?
                    $_disabled = array_search($k, $_field['disabled_options']) !== false ? ' disabled="disabled"' : '';

                    $_out .= '<option value="' . $k . '"' . $_checked . $_disabled . '>' . $v . '</option>';

                endforeach;
                $_out .= '</optgroup>';

            else :

                //  Selected?
                $_checked = $value == $_selected ? ' selected="selected"' : '';

                //  Disabled?
                $_disabled = array_search($value, $_field['disabled_options']) !== false ? ' disabled="disabled"' : '';

                $_out .= '<option value="' . $value . '"' . $_checked . $_disabled . '>' . $label . '</option>';

            endif;

        endforeach;
        $_out .= '</select>';

        // --------------------------------------------------------------------------

        if ($_readonly) :

            $_out .= form_hidden($_field['key'], $_field['default']);

        endif;

        //  Tip
        $_out .= $_tip['title'] ? '<b class="' . $_tip['class'] . '" rel="' . $_tip['rel'] . '" title="' . htmlentities($_tip['title'], ENT_QUOTES) . '"></b>' : '';

        //  Error
        $_out .= form_error($_field['key'], '<span class="alert alert-danger">', '</span>');

        //  Info
        $_out .= $_field['info'] ? '<small class="info ' . $_field['info_class'] . '">' . $_field['info'] . '</small>' : '';

        $_out .= '</span>';

        $_out .= '</label>';
        $_out .= '</div>';

        // --------------------------------------------------------------------------

        return $_out;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_dropdown_multiple')) {

    /**
     * Generates a form field using the "select" input type, with multiple selections allowed
     *
     * @param  array  $field   The config array
     * @param  array  $options The options to use for the dropdown (DEPRECATED: use $field['options'] instead)
     * @param  string $tip     An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string          The form HTML
     */
    function form_field_dropdown_multiple($field, $options = null, $tip = '')
    {
        //  Set var defaults
        $_field                     = [];
        $_field['id']               = isset($field['id']) ? $field['id'] : null;
        $_field['type']             = isset($field['type']) ? $field['type'] : 'text';
        $_field['oddeven']          = isset($field['oddeven']) ? $field['oddeven'] : null;
        $_field['key']              = isset($field['key']) ? $field['key'] : null;
        $_field['label']            = isset($field['label']) ? $field['label'] : null;
        $_field['default']          = isset($field['default']) ? $field['default'] : null;
        $_field['sub_label']        = isset($field['sub_label']) ? $field['sub_label'] : null;
        $_field['required']         = isset($field['required']) ? $field['required'] : false;
        $_field['placeholder']      = isset($field['placeholder']) ? $field['placeholder'] : null;
        $_field['class']            = isset($field['class']) ? $field['class'] : false;
        $_field['style']            = isset($field['style']) ? $field['style'] : false;
        $_field['readonly']         = isset($field['readonly']) ? $field['readonly'] : false;
        $_field['data']             = isset($field['data']) ? $field['data'] : [];
        $_field['disabled_options'] = isset($field['disabled_options']) ? $field['disabled_options'] : [];
        $_field['info']             = isset($field['info']) ? $field['info'] : [];
        $_field['info_class']       = isset($field['info_class']) ? $field['info_class'] : false;
        $_field['tip']              = isset($field['tip']) ? $field['tip'] : $tip;

        if (is_null($options)) {
            $options = isset($field['options']) ? $field['options'] : [];
        }

        $_tip          = [];
        $_tip['class'] = is_array($_field['tip']) && isset($_field['tip']['class']) ? $_field['tip']['class'] : 'fa fa-question-circle fa-lg tip';
        $_tip['rel']   = is_array($_field['tip']) && isset($_field['tip']['rel']) ? $_field['tip']['rel'] : 'tipsy-left';
        $_tip['title'] = is_array($_field['tip']) && isset($_field['tip']['title']) ? $_field['tip']['title'] : null;
        $_tip['title'] = is_string($_field['tip']) ? $_field['tip'] : $_field['title'];

        $_field_id_top = $_field['id'] ? 'id="field-' . $_field['id'] . '"' : '';
        $_error        = form_error($_field['key']) ? 'error' : '';
        $_readonly     = $_field['readonly'] ? 'disabled="disabled"' : '';
        $_readonly_cls = $_field['readonly'] ? 'readonly' : '';

        // --------------------------------------------------------------------------

        $_out = '<div class="field dropdown ' . $_error . ' ' . $_readonly_cls . ' ' . $_field['oddeven'] . '" ' . $_field_id_top . '>';
        $_out .= '<label>';

        //  Label
        $_out .= '<span class="label">';
        $_out .= $_field['label'];
        $_out .= $_field['required'] ? '*' : '';
        $_out .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';
        $_out .= '</span>';

        // --------------------------------------------------------------------------

        //  Field
        $_withtip = $_tip['title'] ? 'with-tip' : '';
        $_out     .= '<span class="input ' . $_withtip . '">';

        //  Does the field have an id?
        $_field['id'] = $_field['id'] ? 'id="' . $_field['id'] . '" ' : '';

        //  Any data attributes?
        $_data = '';
        foreach ($_field['data'] as $attr => $value) :

            $_data .= ' data-' . $attr . '="' . $value . '"';

        endforeach;

        //  Any defaults?
        $_field['default'] = (array) $_field['default'];

        //  Get the selected options
        $_selected = set_value($_field['key'], $_field['default']);

        //  Build the select
        $_placeholder = null !== $_field['placeholder'] ? 'data-placeholder="' . htmlentities($_field['placeholder'], ENT_QUOTES) . '"' : '';
        $_out         .= '<select name="' . $_field['key'] . '" multiple="multiple" class="' . $_field['class'] . '" style="' . $_field['style'] . '" ' . $_field['id'] . ' ' . $_readonly . $_placeholder . $_data . '>';

        foreach ($options as $value => $label) :

            //  Selected?
            if (is_array($_selected)) :
                if (in_array($value, $_selected)) :
                    $_checked = ' selected="selected"';
                else :
                    $_checked = '';
                endif;
            else :
                $_checked = $value == $_selected ? ' selected="selected"' : '';
            endif;

            //  Disabled?
            $_disabled = array_search($value, $_field['disabled_options']) !== false ? ' disabled="disabled"' : '';

            $_out .= '<option value="' . $value . '"' . $_checked . $_disabled . '>' . $label . '</option>';

        endforeach;
        $_out .= '</select>';

        if ($_readonly) :

            $_out .= form_hidden($_field['key'], $_field['default']);

        endif;

        // --------------------------------------------------------------------------

        //  Tip
        $_out .= $_tip['title'] ? '<b class="' . $_tip['class'] . '" rel="' . $_tip['rel'] . '" title="' . htmlentities($_tip['title'], ENT_QUOTES) . '"></b>' : '';

        //  Error
        $_out .= form_error($_field['key'], '<span class="alert alert-danger">', '</span>');

        //  Info
        $_out .= $_field['info'] ? '<small class="info ' . $_field['info_class'] . '">' . $_field['info'] . '</small>' : '';

        $_out .= '</span';

        $_out .= '</label>';
        $_out .= '</div>';

        // --------------------------------------------------------------------------

        return $_out;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_boolean')) {

    /**
     * Generates a form field using the "select" input type containing two options.
     *
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string        The form HTML
     */
    function form_field_boolean($field, $tip = '')
    {
        //  Set var defaults
        $_field                = [];
        $_field['id']          = isset($field['id']) ? $field['id'] : null;
        $_field['oddeven']     = isset($field['oddeven']) ? $field['oddeven'] : null;
        $_field['key']         = isset($field['key']) ? $field['key'] : null;
        $_field['label']       = isset($field['label']) ? $field['label'] : null;
        $_field['default']     = isset($field['default']) ? $field['default'] : null;
        $_field['sub_label']   = isset($field['sub_label']) ? $field['sub_label'] : null;
        $_field['required']    = isset($field['required']) ? $field['required'] : false;
        $_field['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : null;
        $_field['class']       = isset($field['class']) ? $field['class'] : false;
        $_field['text_on']     = isset($field['text_on']) ? $field['text_on'] : 'ON';
        $_field['text_off']    = isset($field['text_off']) ? $field['text_off'] : 'OFF';
        $_field['data']        = isset($field['data']) ? $field['data'] : [];
        $_field['readonly']    = isset($field['readonly']) ? $field['readonly'] : false;
        $_field['info']        = isset($field['info']) ? $field['info'] : false;
        $_field['info_class']  = isset($field['info_class']) ? $field['info_class'] : false;
        $_field['tip']         = isset($field['tip']) ? $field['tip'] : $tip;

        $_tip          = [];
        $_tip['class'] = is_array($_field['tip']) && isset($_field['tip']['class']) ? $_field['tip']['class'] : 'fa fa-question-circle fa-lg tip';
        $_tip['rel']   = is_array($_field['tip']) && isset($_field['tip']['rel']) ? $_field['tip']['rel'] : 'tipsy-left';
        $_tip['title'] = is_array($_field['tip']) && isset($_field['tip']['title']) ? $_field['tip']['title'] : null;
        $_tip['title'] = is_string($_field['tip']) ? $_field['tip'] : $_field['title'];

        $_field_id_top = $_field['id'] ? 'id="field-' . $_field['id'] . '"' : '';
        $_error        = form_error($_field['key']) ? 'error' : '';
        $_readonly     = $_field['readonly'] ? 'disabled="disabled"' : '';
        $_readonly_cls = $_field['readonly'] ? 'readonly' : '';
        $_class        = $_field['class'] ? 'class="' . $_field['class'] . '"' : '';

        // --------------------------------------------------------------------------

        $_out = '<div class="field checkbox boolean ' . $_error . ' ' . $_field['oddeven'] . ' ' . $_readonly_cls . '" data-text-on="' . $_field['text_on'] . '" data-text-off="' . $_field['text_off'] . '" ' . $_field_id_top . '>';

        //  Does the field have an id?
        $_field['id'] = $_field['id'] ? 'id="' . $_field['id'] . '" ' : '';

        //  Any data attributes?
        $_data = 'data-is-boolean-field="true"';
        foreach ($_field['data'] as $attr => $value) {

            $_data .= ' data-' . $attr . '="' . $value . '"';
        }

        //  Label
        $_out .= '<span class="label">';
        $_out .= $_field['label'];
        $_out .= $_field['required'] ? '*' : '';
        $_out .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';
        $_out .= '</span>';

        //  Field
        $_tipclass = $_tip['title'] ? 'with-tip' : '';
        $_out      .= '<span class="input ' . $_tipclass . '">';
        $_selected = set_value($_field['key'], (bool) $_field['default']);

        $_out .= '<div class="toggle toggle-modern"></div>';
        $_out .= form_checkbox($_field['key'], true, $_selected, $_field['id'] . $_data . ' ' . $_readonly . ' ' . $_class);

        //  Tip
        $_out .= $_tip['title'] ? '<b class="' . $_tip['class'] . '" rel="' . $_tip['rel'] . '" title="' . htmlentities($_tip['title'], ENT_QUOTES) . '"></b>' : '';

        //  Error
        $_out .= form_error($_field['key'], '<span class="alert alert-danger">', '</span>');

        //  Info block
        $_out .= $_field['info'] ? '<small class="info ' . $_field['info_class'] . '">' . $_field['info'] . '</small>' : '';

        $_out .= '</span>';
        $_out .= '</div>';

        // --------------------------------------------------------------------------

        return $_out;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_radio')) {

    /**
     * Generates a form field using the "radio" input type
     *
     * @param  array  $field   The config array
     * @param  array  $options The options to use for the radios (DEPRECATED: use $field['options'] instead)
     * @param  string $tip     An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string          The form HTML
     */
    function form_field_radio($field, $options = null, $tip = '')
    {
        $field['type'] = 'radio';
        return form_field_checkbox($field, $options, $tip);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_checkbox')) {

    /**
     * Generates a form field using the "checkbox" input type
     *
     * @param  array  $field   The config array
     * @param  array  $options The options to use for the checkboxes (DEPRECATED: use $field['options'] instead)
     * @param  string $tip     An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string          The form HTML
     */
    function form_field_checkbox($field, $options = null, $tip = '')
    {
        //  Set var defaults
        $_field                = [];
        $_field['type']        = isset($field['type']) ? $field['type'] : 'checkbox';
        $_field['id']          = isset($field['id']) ? $field['id'] : null;
        $_field['oddeven']     = isset($field['oddeven']) ? $field['oddeven'] : null;
        $_field['key']         = isset($field['key']) ? $field['key'] : null;
        $_field['label']       = isset($field['label']) ? $field['label'] : null;
        $_field['default']     = isset($field['default']) ? $field['default'] : null;
        $_field['sub_label']   = isset($field['sub_label']) ? $field['sub_label'] : null;
        $_field['required']    = isset($field['required']) ? $field['required'] : false;
        $_field['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : null;
        $_field['class']       = isset($field['class']) ? $field['class'] : false;
        $_field['tip']         = isset($field['tip']) ? $field['tip'] : $tip;
        $_field['options']     = isset($field['options']) ? $field['options'] : $options;

        $_tip          = [];
        $_tip['class'] = is_array($_field['tip']) && isset($_field['tip']['class']) ? $_field['tip']['class'] : 'fa fa-question-circle fa-lg tip';
        $_tip['rel']   = is_array($_field['tip']) && isset($_field['tip']['rel']) ? $_field['tip']['rel'] : 'tipsy-left';
        $_tip['title'] = is_array($_field['tip']) && isset($_field['tip']['title']) ? $_field['tip']['title'] : null;
        $_tip['title'] = is_string($_field['tip']) ? $_field['tip'] : $_field['title'];

        $_field_id_top = $_field['id'] ? 'id="field-' . $_field['id'] . '"' : '';
        $_error        = form_error($_field['key']) ? 'error' : '';

        // --------------------------------------------------------------------------

        $_out = '<div class="field ' . $_field['type'] . ' ' . $_error . ' ' . $_field['oddeven'] . '" ' . $_field_id_top . '>';

        //  First option
        $_out .= '<label>';

        //  Label
        $_out .= '<span class="label">';
        $_out .= $_field['label'];
        $_out .= $_field['required'] ? '*' : '';
        $_out .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';
        $_out .= '</span>';

        //  Does the field have an id?
        $_id = !empty($_field['options'][0]['id']) ? 'id="' . $_field['options'][0]['id'] . '-0" ' : '';

        //  Is the option disabled?
        $_disabled = !empty($_field['options'][0]['disabled']) ? 'disabled="disabled" ' : '';

        $_tipclass      = $_tip['title'] ? 'with-tip' : '';
        $_disabledclass = $_disabled ? 'is-disabled' : '';

        $_out .= '<span class="input ' . $_tipclass . ' ' . $_disabledclass . '">';

        $oInput = Factory::service('Input');

        //  Field
        if (substr($_field['key'], -2) == '[]') :

            //  Field is an array, need to look for the value
            $_values        = $oInput->post(substr($_field['key'], 0, -2));
            $_data_selected = isset($_field['options'][0]['selected']) ? $_field['options'][0]['selected'] : false;
            $_selected      = $oInput->post() ? false : $_data_selected;

            if (is_array($_values) && array_search($_field['options'][0]['value'], $_values) !== false) :

                $_selected = true;

            endif;

        else :

            //  Normal field, continue as normal Mr Norman!
            if ($oInput->post($_field['key'])) :

                $_selected = $oInput->post($_field['key']) == $_field['options'][0]['value'] ? true : false;

            else :

                $_selected = isset($_field['options'][0]['selected']) ? $_field['options'][0]['selected'] : false;

            endif;

        endif;

        $_key = isset($_field['options'][0]['key']) ? $_field['options'][0]['key'] : $_field['key'];

        if ($_field['type'] == 'checkbox') {

            $_out .= form_checkbox(
                $_key,
                $_field['options'][0]['value'],
                $_selected,
                $_id . $_disabled
            );
            $_out .= '<span class="text">' . $_field['options'][0]['label'] . '</span>';
        } elseif ($_field['type'] == 'radio') {

            $_out .= form_radio(
                $_key,
                $_field['options'][0]['value'],
                $_selected,
                $_id . $_disabled
            );
            $_out .= '<span class="text">' . $_field['options'][0]['label'] . '</span>';
        }

        //  Tip
        if (!empty($_tip['title'])) {

            $sTitle = htmlentities($_tip['title'], ENT_QUOTES);
            $_out   .= '<b class="' . $_tip['class'] . '" rel="' . $_tip['rel'] . '" title="' . $sTitle . '"></b>';
        }

        $_out .= '</span>';
        $_out .= '</label>';

        //  Remaining options
        $numOptions = count($_field['options']);
        for ($i = 1; $i < $numOptions; $i++) :

            $_out .= '<label>';

            //  Label
            $_out .= '<span class="label">&nbsp;</span>';

            //  Does the field have an id?
            $_id = !empty($_field['options'][$i]['id']) ? 'id="' . $_field['options'][$i]['id'] . '-' . $i . '" ' : '';

            //  Is the option disabled?
            $_disabled      = !empty($_field['options'][$i]['disabled']) ? 'disabled="disabled" ' : '';
            $_disabledclass = $_disabled ? 'is-disabled' : '';

            $_out .= '<span class="input ' . $_disabledclass . '">';

            //  Input
            if (substr($_field['key'], -2) == '[]') :

                //  Field is an array, need to look for the value
                $_values        = $oInput->post(substr($_field['key'], 0, -2));
                $_data_selected = isset($_field['options'][$i]['selected']) ? $_field['options'][$i]['selected'] : false;
                $_selected      = $oInput->post() ? false : $_data_selected;

                if (is_array($_values) && array_search($_field['options'][$i]['value'], $_values) !== false) :

                    $_selected = true;

                endif;

            else :

                //  Normal field, continue as normal Mr Norman!
                if ($oInput->post($_field['key'])) :

                    $_selected = $oInput->post($_field['key']) == $_field['options'][$i]['value'] ? true : false;

                else :

                    $_selected = isset($_field['options'][$i]['selected']) ? $_field['options'][$i]['selected'] : false;

                endif;

            endif;

            $_key = isset($_field['options'][$i]['key']) ? $_field['options'][$i]['key'] : $_field['key'];

            if ($_field['type'] == 'checkbox') {

                $_out .= form_checkbox(
                    $_key,
                    $_field['options'][$i]['value'],
                    $_selected,
                    $_id . $_disabled
                );
                $_out .= '<span class="text">' . $_field['options'][$i]['label'] . '</span>';
            } elseif ($_field['type'] == 'radio') {

                $_out .= form_radio(
                    $_key,
                    $_field['options'][$i]['value'],
                    $_selected,
                    $_id . $_disabled
                );
                $_out .= '<span class="text">' . $_field['options'][$i]['label'] . '</span>';
            }

            $_out .= '</span>';
            $_out .= '</label>';

        endfor;

        //  Error
        $_out .= form_error($_field['key'], '<span class="alert alert-danger">', '</span>');

        $_out .= '</div>';

        // --------------------------------------------------------------------------

        return $_out;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_cms_widgets')) {

    /**
     * Generates a form field containing a button to open the CMS widgets manager
     * @todo oh God, sort this file out, use a proper form building class
     *
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     *
     * @return string        The form HTML
     */
    function form_field_cms_widgets($field, $tip = '')
    {
        //  Set var defaults
        $_field                = [];
        $_field['id']          = isset($field['id']) ? $field['id'] : null;
        $_field['oddeven']     = isset($field['oddeven']) ? $field['oddeven'] : null;
        $_field['key']         = isset($field['key']) ? $field['key'] : null;
        $_field['label']       = isset($field['label']) ? $field['label'] : null;
        $_field['default']     = isset($field['default']) ? $field['default'] : null;
        $_field['sub_label']   = isset($field['sub_label']) ? $field['sub_label'] : null;
        $_field['required']    = isset($field['required']) ? $field['required'] : false;
        $_field['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : null;
        $_field['class']       = isset($field['class']) ? $field['class'] : false;
        $_field['data']        = isset($field['data']) ? $field['data'] : [];
        $_field['readonly']    = isset($field['readonly']) ? $field['readonly'] : false;
        $_field['info']        = isset($field['info']) ? $field['info'] : false;
        $_field['info_class']  = isset($field['info_class']) ? $field['info_class'] : false;
        $_field['tip']         = isset($field['tip']) ? $field['tip'] : $tip;

        $_tip          = [];
        $_tip['class'] = is_array($_field['tip']) && isset($_field['tip']['class']) ? $_field['tip']['class'] : 'fa fa-question-circle fa-lg tip';
        $_tip['rel']   = is_array($_field['tip']) && isset($_field['tip']['rel']) ? $_field['tip']['rel'] : 'tipsy-left';
        $_tip['title'] = is_array($_field['tip']) && isset($_field['tip']['title']) ? $_field['tip']['title'] : null;
        $_tip['title'] = is_string($_field['tip']) ? $_field['tip'] : $_field['title'];

        $_field_id_top = $_field['id'] ? 'id="field-' . $_field['id'] . '"' : '';
        $_error        = form_error($_field['key']) ? 'error' : '';
        $_readonly     = $_field['readonly'] ? 'disabled="disabled"' : '';
        $_readonly_cls = $_field['readonly'] ? 'readonly' : '';
        $_class        = $_field['class'] ? 'class="' . $_field['class'] . '"' : '';

        // --------------------------------------------------------------------------

        $_out = '<div class="field cms-widgets ' . $_error . ' ' . $_field['oddeven'] . ' ' . $_readonly_cls . '" ' . $_field_id_top . '>';

        //  Does the field have an id?
        $_field['id'] = $_field['id'] ? 'id="' . $_field['id'] . '" ' : '';

        //  Any data attributes?
        $_data = '';
        foreach ($_field['data'] as $attr => $value) {

            $_data .= ' data-' . $attr . '="' . $value . '"';
        }

        //  Label
        $_out .= '<span class="label">';
        $_out .= $_field['label'];
        $_out .= $_field['required'] ? '*' : '';
        $_out .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';
        $_out .= '</span>';

        //  If the default value is nto a string then encode it, allow devs to pass the configuration array if desired
        if (!is_string($_field['default'])) {
            $_field['default'] = json_encode($_field['default']);
        }

        //  Field
        $_tipclass = $_tip['title'] ? 'with-tip' : '';
        $_out      .= '<span class="input ' . $_tipclass . '">';

        $_default = set_value($_field['key'], $_field['default']);

        /**
         * Posted items will not be escaped and as such will not render as correct JSON by the JS.
         */
        if (empty($_POST)) {
            $_default = htmlentities($_default);
        }

        $_out .= '<textarea class="widget-data hidden" name="' . $_field['key'] . '" ' . $_field['id'] . '>' . $_default . '</textarea>';
        $_out .= '<button type="button" class="btn btn-primary btn-sm open-editor" data-key="' . $_field['key'] . '">';
        $_out .= '<span class="fa fa-cogs">&nbsp;</span> Open Widget Editor';
        $_out .= '</button>';

        //  Tip
        $_out .= $_tip['title'] ? '<b class="' . $_tip['class'] . '" rel="' . $_tip['rel'] . '" title="' . htmlentities($_tip['title'], ENT_QUOTES) . '"></b>' : '';

        //  Error
        $_out .= form_error($_field['key'], '<span class="alert alert-danger">', '</span>');

        //  Info block
        $_out .= $_field['info'] ? '<small class="info ' . $_field['info_class'] . '">' . $_field['info'] . '</small>' : '';

        $_out .= '</span>';
        $_out .= '</div>';

        // --------------------------------------------------------------------------

        return $_out;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_submit')) {

    /**
     * Generates a submit button which is aligned properly with the form_field_* functions
     *
     * @param  string $button_value      The value to give the button
     * @param  string $button_name       The name to give the button
     * @param  string $button_attributes Any additional attributes to give the button
     *
     * @return string                    The form HTML
     */
    function form_field_submit($button_value = 'Submit', $button_name = 'submit', $button_attributes = '')
    {
        $_field_html = form_submit($button_name, $button_value, $button_attributes);

        // --------------------------------------------------------------------------

        $_out = <<<EOT

    <div class="field submit">
        <span class="label">&nbsp;</span>
        <span class="input">
            $_field_html
        </span>
    </div>

EOT;

        // --------------------------------------------------------------------------

        return $_out;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_render')) {

    /**
     * This function renders a generic form field; the actual contents of the
     * form field must be passed in via $aField's html parameter
     * @todo - Implement this into the other functions so all are the same
     * @todo - Turn this into a class
     *
     * @param array  $aField The configuration array
     * @param string $sTip   The tip (deprecated, pass in through $aField)
     *
     * @return string
     */
    function form_field_render($aField, $sTip = null)
    {
        if ($sTip) {
            trigger_error('Use of second parameter as field tip is deprecated.', E_USER_NOTICE);
        }

        $oField = (object) [
            'id'          => getFromArray('id', $aField, null),
            'type'        => getFromArray('type', $aField, null),
            'oddeven'     => getFromArray('oddeven', $aField, null),
            'key'         => getFromArray('key', $aField, null),
            'label'       => getFromArray('label', $aField, null),
            'default'     => getFromArray('default', $aField, null),
            'sub_label'   => getFromArray('sub_label', $aField, null),
            'required'    => getFromArray('required', $aField, false),
            'placeholder' => getFromArray('placeholder', $aField, null),
            'class'       => getFromArray('class', $aField, false),
            'data'        => getFromArray('data', $aField, []),
            'readonly'    => getFromArray('readonly', $aField, false),
            'info'        => getFromArray('info', $aField, false),
            'info_class'  => getFromArray('info_class', $aField, false),
            'tip'         => getFromArray('tip', $aField, $sTip),
            'error'       => getFromArray('error', $aField, null),
            'html'        => getFromArray('html', $aField, ''),
        ];

        if (is_array($oField->tip)) {
            $oTip = (object) [
                'class' => getFromArray('class', $oField->tip, 'fa fa-question-circle fa-lg tip'),
                'rel'   => getFromArray('rel', $oField->tip, 'tipsy-left'),
                'title' => getFromArray('title', $oField->tip, null),
            ];
        } elseif (is_string($oField->tip)) {
            $oTip = (object) [
                'class' => 'fa fa-question-circle fa-lg tip',
                'rel'   => 'tipsy-left',
                'title' => $oField->tip,
            ];
        }

        $sFieldIdTop    = $oField->id ? 'id="field-' . $oField->id . '"' : '';
        $sError         = form_error($oField->key) || $oField->error ? 'error' : '';
        $sErrorClass    = $sError ? 'error' : '';
        $sReadonly      = $oField->readonly ? 'readonly="readonly"' : '';
        $sReadOnlyClass = $oField->readonly ? 'readonly' : '';

        // --------------------------------------------------------------------------

        //  Is the label required?
        $oField->label .= $oField->required ? '*' : '';

        //  Prep sublabel
        $oField->sub_label = $oField->sub_label ? '<small>' . $oField->sub_label . '</small>' : '';

        //  Has the field got a tip?
        $sTipClass = !empty($oTip) ? 'with-tip' : '';
        $sTipHtml  = !empty($oTip) ? '<b class="' . $oTip->class . '" rel="' . $oTip->rel . '" title="' . htmlentities($oTip->title, ENT_QUOTES) . '"></b>' : '';

        // --------------------------------------------------------------------------

        //  Prep the field's attributes
        $aAttr = [
            $oField->id ? 'id="' . $oField->id . '" ' : '',
            'class="' . $oField->class . '" ',
            $sReadonly,
        ];

        //  Any data attributes?
        foreach ($oField->data as $sAttr => $sValue) {
            $aAttr[] = 'data-' . $sAttr . '="' . $sValue . '"';
        }

        $sFieldAttr = implode(' ', array_filter($aAttr));

        // --------------------------------------------------------------------------

        //  Errors
        if ($sError && $oField->error) {
            //  Manually defined error
            $sError = '<span class="alert alert-danger">' . $oField->error . '</span>';
        } elseif ($sError) {
            //  Automatic error
            $sError = form_error($oField->key, '<span class="alert alert-danger">', '</span>');
        }

        // --------------------------------------------------------------------------

        //  info block
        $sInfoHtml = $oField->info ? '<small class="info ' . $oField->info_class . '">' . $oField->info . '</small>' : '';

        // --------------------------------------------------------------------------

        $sOut = <<<EOT

    <div class="field $oField->type $sErrorClass $oField->oddeven $sReadOnlyClass " $sFieldIdTop>
        <label>
            <span class="label">
                $oField->label
                $oField->sub_label
            </span>
            <span class="input $sTipClass">
                $oField->html
                $sTipHtml
                $sError
                $sInfoHtml
            <span>
        </label>
    </div>

EOT;

        return $sOut;
    }
}

// --------------------------------------------------------------------------

//  Include the CodeIgniter original
include FCPATH . 'vendor/rogeriopradoj/codeigniter/system/helpers/form_helper.php';
