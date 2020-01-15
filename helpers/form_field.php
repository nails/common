<?php

use Nails\Common\Helper\Form;

if (!function_exists('form_field')) {
    function form_field($aField, $sTip = '')
    {
        return Form\Field::text($aField, $sTip);
    }
}

if (!function_exists('form_field_html')) {
    function form_field_html($aField, $sTip = '')
    {
        return Form\Field::html($aField, $sTip);
    }
}

if (!function_exists('form_field_email')) {
    function form_field_email($aField, $sTip = '')
    {
        return Form\Field::email($aField, $sTip);
    }
}

if (!function_exists('form_field_number')) {
    function form_field_number($aField, $sTip = '')
    {
        return Form\Field::number($aField, $sTip);
    }
}

if (!function_exists('form_field_url')) {
    function form_field_url($aField, $sTip = '')
    {
        return Form\Field::url($aField, $sTip);
    }
}

if (!function_exists('form_field_tel')) {
    function form_field_tel($aField, $sTip = '')
    {
        return Form\Field::tel($aField, $sTip);
    }
}

if (!function_exists('form_field_color')) {
    function form_field_color($aField, $sTip = '')
    {
        return Form\Field::color($aField, $sTip);
    }
}

if (!function_exists('form_field_password')) {
    function form_field_password($aField, $sTip = '')
    {
        return Form\Field::password($aField, $sTip);
    }
}

if (!function_exists('form_field_textarea')) {
    function form_field_textarea($aField, $sTip = '')
    {
        return Form\Field::textarea($aField, $sTip);
    }
}

if (!function_exists('form_field_wysiwyg')) {
    function form_field_wysiwyg($aField, $sTip = '')
    {
        return Form\Field::wysiwyg($aField, $sTip);
    }
}

if (!function_exists('form_field_text')) {
    function form_field_text($aField, $sTip = '')
    {
        return Form\Field::text($aField, $sTip);
    }
}

if (!function_exists('form_field_date')) {
    function form_field_date($aField, $sTip = '')
    {
        return Form\Field::date($aField, $sTip);
    }
}

if (!function_exists('form_field_time')) {
    function form_field_time($aField, $sTip = '')
    {
        return Form\Field::time($aField, $sTip);
    }
}

if (!function_exists('form_field_datetime')) {
    function form_field_datetime($aField, $sTip = '')
    {
        return Form\Field::datetime($aField, $sTip);
    }
}

if (!function_exists('form_field_dropdown')) {
    function form_field_dropdown($aField, $aOptions = null, $sTip = '')
    {
        return Form\Field::dropdown($aField, $aOptions, $sTip);
    }
}

if (!function_exists('form_field_dropdown_multiple')) {
    function form_field_dropdown_multiple($aField, $aOptions = null, $sTip = '')
    {
        return Form\Field::dropdownMultiple($aField, $aOptions, $sTip);
    }
}

if (!function_exists('form_field_boolean')) {
    function form_field_boolean($aField, $sTip = '')
    {
        return Form\Field::boolean($aField, $sTip);
    }
}

if (!function_exists('form_field_radio')) {
    function form_field_radio($aField, $aOptions = null, $sTip = '')
    {
        return Form\Field::radio($aField, $aOptions, $sTip);
    }
}

if (!function_exists('form_field_checkbox')) {
    function form_field_checkbox($aField, $aOptions = null, $sTip = '')
    {
        return Form\Field::checkbox($aField, $aOptions, $sTip);
    }
}

if (!function_exists('form_field_submit')) {
    function form_field_submit($aField)
    {
        return Form\Field::submit($aField);
    }
}
