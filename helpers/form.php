<?php

use Nails\Common\Helper\Form;

if (!function_exists('form_text')) {
    function form_text($data = '', $value = '', $extra = '')
    {
        return Form::form_text($data, $value, $extra);
    }
}

if (!function_exists('form_email')) {
    function form_email($data = '', $value = '', $extra = '')
    {
        return Form::form_email($data, $value, $extra);
    }
}

if (!function_exists('form_tel')) {
    function form_tel($data = '', $value = '', $extra = '')
    {
        return Form::form_tel($data, $value, $extra);
    }
}

if (!function_exists('form_number')) {
    function form_number($data = '', $value = '', $extra = '')
    {
        return Form::form_number($data, $value, $extra);
    }
}

if (!function_exists('form_url')) {
    function form_url($data = '', $value = '', $extra = '')
    {
        return Form::form_url($data, $value, $extra);
    }
}

if (!function_exists('form_date')) {
    function form_date($data = '', $value = '', $extra = '')
    {
        return Form::form_date($data, $value, $extra);
    }
}

if (!function_exists('form_time')) {
    function form_time($data = '', $value = '', $extra = '')
    {
        return Form::form_time($data, $value, $extra);
    }
}

if (!function_exists('form_color')) {
    function form_color($data = '', $value = '', $extra = '')
    {
        return Form::form_color($data, $value, $extra);
    }
}

if (!function_exists('form_datetime')) {
    function form_datetime($data = '', $value = '', $extra = '')
    {
        return Form::form_datetime($data, $value, $extra);
    }
}

if (!function_exists('form_open')) {
    function form_open($action = '', $attributes = '', $hidden = [])
    {
        return Form::form_open($action, $attributes, $hidden);
    }
}

if (!function_exists('form_field')) {
    function form_field($field, $tip = '')
    {
        return Form::form_field($field, $tip);
    }
}

if (!function_exists('form_field_email')) {
    function form_field_email($field, $tip = '')
    {
        return Form::form_field_email($field, $tip);
    }
}

if (!function_exists('form_field_number')) {
    function form_field_number($field, $tip = '')
    {
        return Form::form_field_number($field, $tip);
    }
}

if (!function_exists('form_field_url')) {
    function form_field_url($field, $tip = '')
    {
        return Form::form_field_url($field, $tip);
    }
}

if (!function_exists('form_field_tel')) {
    function form_field_tel($field, $tip = '')
    {
        return Form::form_field_tel($field, $tip);
    }
}

if (!function_exists('form_field_color')) {
    function form_field_color($field, $tip = '')
    {
        return Form::form_field_color($field, $tip);
    }
}

if (!function_exists('form_field_password')) {
    function form_field_password($field, $tip = '')
    {
        return Form::form_field_password($field, $tip);
    }
}

if (!function_exists('form_field_textarea')) {
    function form_field_textarea($field, $tip = '')
    {
        return Form::form_field_textarea($field, $tip);
    }
}

if (!function_exists('form_field_wysiwyg')) {
    function form_field_wysiwyg($field, $tip = '')
    {
        return Form::form_field_wysiwyg($field, $tip);
    }
}

if (!function_exists('form_field_text')) {
    function form_field_text($field, $tip = '')
    {
        return Form::form_field_text($field, $tip);
    }
}

if (!function_exists('form_field_date')) {
    function form_field_date($field, $tip = '')
    {
        return Form::form_field_date($field, $tip);
    }
}

if (!function_exists('form_field_time')) {
    function form_field_time($field, $tip = '')
    {
        return Form::form_field_time($field, $tip);
    }
}

if (!function_exists('form_field_datetime')) {
    function form_field_datetime($field, $tip = '')
    {
        return Form::form_field_datetime($field, $tip);
    }
}

if (!function_exists('form_field_dropdown')) {
    function form_field_dropdown($field, $options = null, $tip = '')
    {
        return Form::form_field_dropdown($field, $options, $tip);
    }
}

if (!function_exists('form_field_dropdown_multiple')) {
    function form_field_dropdown_multiple($field, $options = null, $tip = '')
    {
        return Form::form_field_dropdown_multiple($field, $options, $tip);
    }
}

if (!function_exists('form_field_boolean')) {
    function form_field_boolean($field, $tip = '')
    {
        return Form::form_field_boolean($field, $tip);
    }
}

if (!function_exists('form_field_radio')) {
    function form_field_radio($field, $options = null, $tip = '')
    {
        return Form::form_field_radio($field, $options, $tip);
    }
}

if (!function_exists('form_field_checkbox')) {
    function form_field_checkbox($field, $options = null, $tip = '')
    {
        return Form::form_field_checkbox($field, $options, $tip);
    }
}

if (!function_exists('form_field_cms_widgets')) {
    function form_field_cms_widgets($field, $tip = '')
    {
        return Form::form_field_cms_widgets($field, $tip);
    }
}

if (!function_exists('form_field_submit')) {
    function form_field_submit($button_value = 'Submit', $button_name = 'submit', $button_attributes = '')
    {
        return Form::form_field_submit($button_value, $button_name, $button_attributes);
    }
}

if (!function_exists('form_field_render')) {
    function form_field_render($aField, $sTip = null)
    {
        return Form::form_field_render($aField, $sTip);
    }
}

if (!function_exists('form_field_dynamic_table')) {
    function form_field_dynamic_table($aField)
    {
        return Form::form_field_dynamic_table($aField);
    }
}

// --------------------------------------------------------------------------

//  Include the CodeIgniter original
include NAILS_CI_SYSTEM_PATH . 'helpers/form_helper.php';
