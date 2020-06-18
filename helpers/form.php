<?php

use Nails\Common\Helper\Form;

if (!function_exists('form_text')) {
    function form_text($data = '', $value = '', $extra = '')
    {
        return Form::text($data, $value, $extra);
    }
}

if (!function_exists('form_hidden')) {
    function form_hidden($data = '', $value = '', $extra = '')
    {
        return Form::hidden($data, $value, $extra);
    }
}

if (!function_exists('form_password')) {
    function form_password($data = '', $value = '', $extra = '')
    {
        return Form::password($data, $value, $extra);
    }
}

if (!function_exists('form_email')) {
    function form_email($data = '', $value = '', $extra = '')
    {
        return Form::email($data, $value, $extra);
    }
}

if (!function_exists('form_tel')) {
    function form_tel($data = '', $value = '', $extra = '')
    {
        return Form::tel($data, $value, $extra);
    }
}

if (!function_exists('form_number')) {
    function form_number($data = '', $value = '', $extra = '')
    {
        return Form::number($data, $value, $extra);
    }
}

if (!function_exists('form_url')) {
    function form_url($data = '', $value = '', $extra = '')
    {
        return Form::url($data, $value, $extra);
    }
}

if (!function_exists('form_date')) {
    function form_date($data = '', $value = '', $extra = '')
    {
        return Form::date($data, $value, $extra);
    }
}

if (!function_exists('form_time')) {
    function form_time($data = '', $value = '', $extra = '')
    {
        return Form::time($data, $value, $extra);
    }
}

if (!function_exists('form_color')) {
    function form_color($data = '', $value = '', $extra = '')
    {
        return Form::color($data, $value, $extra);
    }
}

if (!function_exists('form_datetime')) {
    function form_datetime($data = '', $value = '', $extra = '')
    {
        return Form::datetime($data, $value, $extra);
    }
}

if (!function_exists('form_boolean')) {
    function form_boolean($data = '', $value = '', $checked = false, $extra = '')
    {
        return Form::boolean($data, $value, $checked, $extra);
    }
}

if (!function_exists('form_open')) {
    function form_open($action = '', $attributes = '', $hidden = [])
    {
        return Form::open($action, $attributes, $hidden);
    }
}

if (!function_exists('form_dropdown_multiple')) {
    function form_dropdown_multiple($name = '', $options = [], $selected = [], $extra = '')
    {
        return Form::dropdownMultiple($name, $options, $selected, $extra);
    }
}

// --------------------------------------------------------------------------

//  Include the CodeIgniter original
include NAILS_CI_SYSTEM_PATH . 'helpers/form_helper.php';
