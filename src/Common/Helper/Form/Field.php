<?php

namespace Nails\Common\Helper\Form;

use Nails\Common\Exception\NailsException;
use Nails\Common\Helper\ArrayHelper;
use Nails\Common\Helper\Form;
use Nails\Common\Service\Input;
use Nails\Factory;

class Field
{
    /**
     * Generates a form field
     *
     * @param array  $field The config array
     * @param string $tip   An optional tip
     *
     * @return string
     */
    protected static function render($field, $tip = ''): string
    {
        //  Set var defaults
        $_field_id           = isset($field['id']) ? $field['id'] : null;
        $_field_type         = isset($field['type']) ? $field['type'] : Form::FIELD_TEXT;
        $_field_oddeven      = isset($field['oddeven']) ? $field['oddeven'] : null;
        $_field_key          = isset($field['key']) ? $field['key'] : null;
        $_field_label        = isset($field['label']) ? $field['label'] : null;
        $_field_default      = isset($field['default']) ? $field['default'] : null;
        $_field_sub_label    = isset($field['sub_label']) ? $field['sub_label'] : null;
        $_field_required     = isset($field['required']) ? $field['required'] : false;
        $_field_placeholder  = isset($field['placeholder']) ? $field['placeholder'] : null;
        $_field_readonly     = isset($field['readonly']) ? $field['readonly'] : false;
        $_field_error        = isset($field['error']) ? $field['error'] : false;
        $_field_class        = isset($field['class']) ? $field['class'] : '';
        $_field_data         = isset($field['data']) ? $field['data'] : [];
        $_field_info         = isset($field['info']) ? $field['info'] : false;
        $_field_info_class   = isset($field['info_class']) ? $field['info_class'] : false;
        $_field_max_length   = isset($field['max_length']) ? (int) $field['max_length'] : null;
        $_field_tip          = isset($field['tip']) ? $field['tip'] : $tip;
        $_field_autocomplete = isset($field['autocomplete']) ? (bool) $field['autocomplete'] : true;
        $_field_helper       = isset($field['helper']) ? $field['helper'] : '';

        $_tip          = [];
        $_tip['class'] = is_array($_field_tip) && isset($_field_tip['class']) ? $_field_tip['class'] : null;
        $_tip['title'] = is_array($_field_tip) && isset($_field_tip['title']) ? $_field_tip['title'] : null;
        $_tip['title'] = is_string($_field_tip) ? $_field_tip : $_tip['title'];

        $_field_id_top = $_field_id ? 'id="field-' . $_field_id . '"' : '';
        $_error        = Form::error($_field_key) || $_field_error ? 'error' : '';
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
        $_tip      = static::getTipHtml((object) $_tip);

        // --------------------------------------------------------------------------

        //  Prep the containers attributes
        $_field_attributes = [];

        //  Move revealer properties to the container if both are present
        //  If only revealer is present then it is a control

        if (array_key_exists('revealer', $_field_data) && array_key_exists('reveal-on', $_field_data)) {
            $_field_attributes[] = sprintf(
                'data-%s="%s"',
                'revealer',
                $_field_data['revealer']
            );
            $_field_attributes[] = sprintf(
                'data-%s="%s"',
                'reveal-on',
                $_field_data['reveal-on']
            );
        }

        $_field_attributes = implode(' ', $_field_attributes);

        // --------------------------------------------------------------------------

        //  Prep the field's attributes
        $_attr = '';

        if ($_field_helper) {
            $_field_data['helper'] = $_field_helper;
        }

        //  Does the field have an id?
        $_attr .= $_field_id ? 'id="' . $_field_id . '" ' : '';

        //  Any data attributes?
        foreach ($_field_data as $attr => $value) {
            $_attr .= ' data-' . $attr . '="' . $value . '"';
        }

        //  Autocomplete?
        if (!$_field_autocomplete) {
            $_attr .= ' autocomplete="off"';
        }

        // --------------------------------------------------------------------------

        //  Generate the field's HTML
        $sFieldAttr = $_attr;
        $sFieldAttr .= ' class="field-input ' . $_field_class . '" ';
        $sFieldAttr .= 'placeholder="' . htmlentities($_field_placeholder, ENT_QUOTES) . '" ';
        $sFieldAttr .= $_readonly;

        $_field_html = call_user_func(
            '\Nails\Common\Helper\Form::' . $_field_type,
            $_field_key,
            set_value($_field_key, $_field_default, false),
            $sFieldAttr
        );

        if (!empty($_field_max_length)) {
            switch ($_field_type) {

                case Form::FIELD_PASSWORD:
                case Form::FIELD_EMAIL:
                case Form::FIELD_NUMBER:
                case Form::FIELD_URL:
                case Form::FIELD_TEXTAREA:
                case Form::FIELD_TEXT:
                    $_max_length_html = '<small class="char-count" data-max-length="' . $_field_max_length . '">';
                    $_max_length_html .= 'Max Length: ' . $_field_max_length;
                    $_max_length_html .= '</small>';
                    break;

                default:
                    $_max_length_html = '';
                    break;
            }
        } else {
            $_max_length_html = '';
        }

        //  Show current value
        if (($_field_type == 'file' || $_field_type == 'upload') && $_field_default) {

            $_field_html .= '<span class="file-download">';
            echo 'Current: ' . $_field_default;
            $_field_html .= '</span>';
        }

        // --------------------------------------------------------------------------

        //  Errors
        if ($_error && $_field_error) {
            $_error = '<span class="alert alert-danger">' . $_field_error . '</span>';
        } elseif ($_error) {
            $_error = Form::error($_field_key, '<span class="alert alert-danger">', '</span>');
        }

        // --------------------------------------------------------------------------

        //  info block
        $info_block = $_field_info ? '<small class="info ' . $_field_info_class . '">' . $_field_info . '</small>' : '';

        // --------------------------------------------------------------------------

        $_out = <<<EOT

        <div class="field $_error_class $_field_oddeven $_readonly_cls $_field_type" $_field_id_top $_field_attributes>
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
                </span>
            </label>
        </div>

        EOT;

        // --------------------------------------------------------------------------

        return $_out;
    }

    // --------------------------------------------------------------------------

    /**
     * This function renders a generic form field; the actual contents of the
     * form field must be passed in via $aField's html parameter
     *
     * @param array  $aField The configuration array
     * @param string $sTip   The tip (deprecated, pass in through $aField)
     *
     * @return string
     *
     * @todo - Turn this into a class
     * @todo - Implement this into the other functions so all are the same
     */
    public static function html($aField, $sTip = null): string
    {
        if ($sTip) {
            trigger_error('Use of second parameter as field tip is deprecated.', E_USER_NOTICE);
        }

        $oField = (object) [
            'id'          => ArrayHelper::getFromArray('id', $aField, null),
            'type'        => ArrayHelper::getFromArray('type', $aField, null),
            'oddeven'     => ArrayHelper::getFromArray('oddeven', $aField, null),
            'key'         => ArrayHelper::getFromArray('key', $aField, null),
            'label'       => ArrayHelper::getFromArray('label', $aField, null),
            'default'     => ArrayHelper::getFromArray('default', $aField, null),
            'sub_label'   => ArrayHelper::getFromArray('sub_label', $aField, null),
            'required'    => ArrayHelper::getFromArray('required', $aField, false),
            'placeholder' => ArrayHelper::getFromArray('placeholder', $aField, null),
            'class'       => ArrayHelper::getFromArray('class', $aField, false),
            'data'        => ArrayHelper::getFromArray('data', $aField, []),
            'readonly'    => ArrayHelper::getFromArray('readonly', $aField, false),
            'info'        => ArrayHelper::getFromArray('info', $aField, false),
            'info_class'  => ArrayHelper::getFromArray('info_class', $aField, false),
            'tip'         => ArrayHelper::getFromArray('tip', $aField, $sTip),
            'error'       => ArrayHelper::getFromArray('error', $aField, null),
            'html'        => ArrayHelper::getFromArray('html', $aField, ''),
        ];

        if (is_array($oField->tip) && !empty($oField->tip['title'])) {
            $oTip = (object) [
                'class' => ArrayHelper::getFromArray('class', $oField->tip, 'tip'),
                'title' => ArrayHelper::getFromArray('title', $oField->tip, null),
            ];
        } elseif (is_string($oField->tip) && !empty($oField->tip)) {
            $oTip = (object) [
                'class' => 'tip',
                'title' => $oField->tip,
            ];
        }

        $sFieldIdTop    = $oField->id ? 'id="field-' . $oField->id . '"' : '';
        $sError         = Form::error($oField->key) || $oField->error ? 'error' : '';
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
        $sTipHtml  = !empty($oTip) ? static::getTipHtml($oTip) : '';

        // --------------------------------------------------------------------------

        //  Prep the field's attributes
        $aClasses = array_filter([
            'field',
            $oField->type,
            $sErrorClass,
            $oField->oddeven,
            $oField->class,
            $sReadOnlyClass,
        ]);

        $aAttr = [
            $oField->id ? 'id="field-' . $oField->id . '"' : '',
            'class="' . (implode(' ', $aClasses)) . '" ',
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
            $sError = Form::error($oField->key, '<span class="alert alert-danger">', '</span>');
        }

        // --------------------------------------------------------------------------

        //  info block
        $sInfoHtml = $oField->info ? '<small class="info ' . $oField->info_class . '">' . $oField->info . '</small>' : '';

        // --------------------------------------------------------------------------

        return <<<EOT

        <div $sFieldAttr>
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
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a form field using the "email" input type
     *
     * @param array  $aField The config array
     * @param string $sTip   An optional tip
     *
     * @return string
     */
    public static function email($aField, $sTip = '')
    {
        $aField['type'] = Form::FIELD_EMAIL;
        return static::render($aField, $sTip);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a form field using the "number" input type
     *
     * @param array  $aField The config array
     * @param string $sTip   An optional tip
     *
     * @return string
     */
    public static function number($aField, $sTip = '')
    {
        $aField['type'] = Form::FIELD_NUMBER;
        return static::render($aField, $sTip);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a form field using the "url" input type
     *
     * @param array  $aField The config array
     * @param string $sTip   An optional tip
     *
     * @return string
     */
    public static function url($aField, $sTip = '')
    {
        $aField['type'] = Form::FIELD_URL;
        return static::render($aField, $sTip);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a form field using the "url" input type
     *
     * @param array  $aField The config array
     * @param string $sTip   An optional tip
     *
     * @return string
     */
    public static function tel($aField, $sTip = '')
    {
        $aField['type'] = Form::FIELD_TEL;
        return static::render($aField, $sTip);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a form field using the "color" input type
     *
     * @param array  $aField The config array
     * @param string $sTip   An optional tip
     *
     * @return string
     */
    public static function color($aField, $sTip = '')
    {
        $aField['type'] = Form::FIELD_COLOR;
        return static::render($aField, $sTip);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a form field using the "password" input type
     *
     * @param array  $aField The config array
     * @param string $sTip   An optional tip
     *
     * @return string
     */
    public static function password($aField, $sTip = '')
    {
        $aField['type'] = Form::FIELD_PASSWORD;
        return static::render($aField, $sTip);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a form field using the "textarea" input type
     *
     * @param array  $aField The config array
     * @param string $sTip   An optional tip
     *
     * @return string
     */
    public static function textarea($aField, $sTip = '')
    {
        $aField['type'] = Form::FIELD_TEXTAREA;
        return static::render($aField, $sTip);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a form field using the "wysiwyg" input type
     *
     * @param array  $aField The config array
     * @param string $sTip   An optional tip
     *
     * @return string
     */
    public static function wysiwyg($aField, $sTip = '')
    {
        $aField['type'] = 'textarea';
        if (empty($aField['class'])) {
            $aField['class'] = '';
        }
        $aField['class'] = trim($aField['class'] . ' wysiwyg');
        return static::render($aField, $sTip);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a form field using the "wysiwyg" input type (using basic config)
     *
     * @param array  $aField The config array
     * @param string $sTip   An optional tip
     *
     * @return string
     */
    public static function wysiwygBasic($aField, $sTip = '')
    {
        if (empty($aField['class'])) {
            $aField['class'] = '';
        }
        $aField['class'] = trim($aField['class'] . ' wysiwyg-basic');
        return static::wysiwyg($aField, $sTip);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a form field using the "text" input type
     *
     * @param array  $aField The config array
     * @param string $sTip   An optional tip
     *
     * @return string
     */
    public static function text($aField, $sTip = '')
    {
        $aField['type'] = Form::FIELD_TEXT;
        return static::render($aField, $sTip);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a form field for dates
     *
     * @param array  $field The config array
     * @param string $sTip  An optional tip
     *
     * @return string
     */
    public static function date($aField, $sTip = '')
    {
        $aField['type']         = Form::FIELD_TEXT;
        $aField['class']        = isset($aField['class']) ? $aField['class'] . ' date' : 'date';
        $aField['placeholder']  = 'YYYY-MM-DD';
        $aField['autocomplete'] = false;

        return static::render($aField, $sTip);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a form field for times
     *
     * @param array  $aField The config array
     * @param string $sTip   An optional tip
     *
     * @return string
     */
    public static function time($aField, $sTip = '')
    {
        $aField['type']         = Form::FIELD_TIME;
        $aField['class']        = isset($aField['class']) ? $aField['class'] . ' time' : 'time';
        $aField['placeholder']  = 'HH:MM';
        $aField['autocomplete'] = false;

        return static::render($aField, $sTip);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a form field for datetimes
     *
     * @param array  $aField The config array
     * @param string $sTip   An optional tip
     *
     * @return string
     */
    public static function datetime($aField, $sTip = '')
    {
        $aField['type']         = Form::FIELD_TEXT;
        $aField['class']        = isset($aField['class']) ? $aField['class'] . ' datetime' : 'datetime';
        $aField['placeholder']  = 'YYYY-MM-DD HH:mm:ss';
        $aField['autocomplete'] = false;

        return static::render($aField, $sTip);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a form field using the "select" input type
     *
     * @param array  $field   The config array
     * @param array  $options The options to use for the dropdown (DEPRECATED: use $field['options'] instead)
     * @param string $tip     An optional tip
     *
     * @return string
     *
     * @todo (Pablo - 2020-01-15) - use render() or html()
     */
    public static function dropdown($field, $options = null, $tip = '')
    {
        //  Set var defaults
        $_field                     = [];
        $_field['id']               = isset($field['id']) ? $field['id'] : null;
        $_field['type']             = isset($field['type']) ? $field['type'] : Form::FIELD_TEXT;
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
        $_tip['class'] = is_array($_field['tip']) && isset($_field['tip']['class']) ? $_field['tip']['class'] : null;
        $_tip['title'] = is_array($_field['tip']) && isset($_field['tip']['title']) ? $_field['tip']['title'] : null;
        $_tip['title'] = is_string($_field['tip']) ? $_field['tip'] : $_field['title'];

        $_field_id_top = $_field['id'] ? 'id="field-' . $_field['id'] . '"' : '';
        $_error        = Form::error($_field['key']) ? 'error' : '';
        $_readonly     = $_field['readonly'] ? 'disabled="disabled"' : '';
        $_readonly_cls = $_field['readonly'] ? 'readonly' : '';

        // --------------------------------------------------------------------------

        //  Prep the containers attributes
        $_field_attributes = [];

        //  Move revealer properties to the container if both are present
        //  If only revealer is present then it is a control

        if (array_key_exists('revealer', $_field['data']) && array_key_exists('reveal-on', $_field['data'])) {
            $_field_attributes[] = sprintf(
                'data-%s="%s"',
                'revealer',
                $_field['data']['revealer']
            );
            $_field_attributes[] = sprintf(
                'data-%s="%s"',
                'reveal-on',
                $_field['data']['reveal-on']
            );
        }

        $_field_attributes = implode(' ', $_field_attributes);

        // --------------------------------------------------------------------------

        $_out = '<div class="field dropdown ' . $_error . ' ' . $_readonly_cls . ' ' . $_field['oddeven'] . '" ' . $_field_id_top . ' ' . $_field_attributes . '>';
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
        foreach ($_field['data'] as $attr => $value) {
            $_data .= ' data-' . $attr . '="' . $value . '"';
        }

        //  Get the selected options
        $_selected = set_value($_field['key'], $_field['default']);

        //  Build the select
        $_placeholder = null !== $_field['placeholder'] ? 'data-placeholder="' . htmlentities($_field['placeholder'], ENT_QUOTES) . '"' : '';
        $_out         .= '<select name="' . $_field['key'] . '" class="' . $_field['class'] . '" style="' . $_field['style'] . '" ' . $_field['id'] . ' ' . $_readonly . $_placeholder . $_data . '>';

        foreach ($_field['options'] as $value => $label) {

            if (is_array($label)) {

                $_out .= '<optgroup label="' . $value . '">';
                foreach ($label as $k => $v) {

                    //  Selected?
                    $_checked = $k == $_selected ? ' selected="selected"' : '';

                    //  Disabled?
                    $_disabled = array_search($k, $_field['disabled_options']) !== false ? ' disabled="disabled"' : '';

                    $_out .= '<option value="' . $k . '"' . $_checked . $_disabled . '>' . $v . '</option>';
                }
                $_out .= '</optgroup>';

            } else {

                //  Selected?
                $_checked = $value == $_selected ? ' selected="selected"' : '';

                //  Disabled?
                $_disabled = array_search($value, $_field['disabled_options']) !== false ? ' disabled="disabled"' : '';

                $_out .= '<option value="' . $value . '"' . $_checked . $_disabled . '>' . $label . '</option>';

            }
        }
        $_out .= '</select>';

        // --------------------------------------------------------------------------

        if ($_readonly) {
            $_out .= Form::hidden($_field['key'], $_field['default']);
        }

        //  Tip
        $_out .= static::getTipHtml((object) $_tip);

        //  Error
        $_out .= Form::error($_field['key'], '<span class="alert alert-danger">', '</span>');

        //  Info
        $_out .= $_field['info'] ? '<small class="info ' . $_field['info_class'] . '">' . $_field['info'] . '</small>' : '';

        $_out .= '</span>';

        $_out .= '</label>';
        $_out .= '</div>';

        // --------------------------------------------------------------------------

        return $_out;
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a form field using the "select" input type, with multiple selections allowed
     *
     * @param array  $field   The config array
     * @param array  $options The options to use for the dropdown (DEPRECATED: use $field['options'] instead)
     * @param string $tip     An optional tip
     *
     * @return string
     *
     * @todo (Pablo - 2020-01-15) - use render() or html()
     */
    public static function dropdownMultiple($field, $options = null, $tip = '')
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

        if (!preg_match('/\[\]$/', $_field['key'])) {
            $_field['key'] .= '[]';
        }

        if (is_null($options)) {
            $options = isset($field['options']) ? $field['options'] : [];
        }

        $_tip          = [];
        $_tip['class'] = is_array($_field['tip']) && isset($_field['tip']['class']) ? $_field['tip']['class'] : null;
        $_tip['title'] = is_array($_field['tip']) && isset($_field['tip']['title']) ? $_field['tip']['title'] : null;
        $_tip['title'] = is_string($_field['tip']) ? $_field['tip'] : $_field['title'];

        $_field_id_top = $_field['id'] ? 'id="field-' . $_field['id'] . '"' : '';
        $_error        = Form::error($_field['key']) ? 'error' : '';
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
        foreach ($_field['data'] as $attr => $value) {
            $_data .= ' data-' . $attr . '="' . $value . '"';
        }

        //  Any defaults?
        //  @todo (Pablo - 2020-01-16) - This done to support CSV items (e.g. MySQL `SET`s) - feels a bit hack arbitrary
        if (!is_array($_field['default'])) {
            $_field['default'] = explode(',', $_field['default']);
        }

        //  Get the selected options
        $_selected = set_value(preg_replace('/\[\]$/', '', $_field['key']), $_field['default']);

        //  Build the select
        $_placeholder = null !== $_field['placeholder'] ? 'data-placeholder="' . htmlentities($_field['placeholder'], ENT_QUOTES) . '"' : '';
        $_out         .= '<select name="' . $_field['key'] . '" multiple="multiple" class="' . $_field['class'] . '" style="' . $_field['style'] . '" ' . $_field['id'] . ' ' . $_readonly . $_placeholder . $_data . '>';

        foreach ($options as $value => $label) {

            //  Selected?
            if (is_array($_selected)) {
                if (in_array($value, $_selected)) {
                    $_checked = ' selected="selected"';
                } else {
                    $_checked = '';
                }
            } else {
                $_checked = $value == $_selected ? ' selected="selected"' : '';
            }

            //  Disabled?
            $_disabled = array_search($value, $_field['disabled_options']) !== false ? ' disabled="disabled"' : '';

            $_out .= '<option value="' . $value . '"' . $_checked . $_disabled . '>' . $label . '</option>';
        }
        $_out .= '</select>';

        if ($_readonly) {
            $_out .= Form::hidden($_field['key'], $_field['default']);
        }

        // --------------------------------------------------------------------------

        //  Tip
        $_out .= static::getTipHtml((object) $_tip);

        //  Error
        $_out .= Form::error($_field['key'], '<span class="alert alert-danger">', '</span>');

        //  Info
        $_out .= $_field['info'] ? '<small class="info ' . $_field['info_class'] . '">' . $_field['info'] . '</small>' : '';

        $_out .= '</span';

        $_out .= '</label>';
        $_out .= '</div>';

        // --------------------------------------------------------------------------

        return $_out;
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a form field using the "select" input type containing two options.
     *
     * @param array  $field The config array
     * @param string $tip   An optional tip
     *
     * @return string
     *
     * @todo (Pablo - 2020-01-15) - use render() or html()
     */
    public static function boolean($field, $tip = '')
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
        $_tip['class'] = is_array($_field['tip']) && isset($_field['tip']['class']) ? $_field['tip']['class'] : null;
        $_tip['title'] = is_array($_field['tip']) && isset($_field['tip']['title']) ? $_field['tip']['title'] : null;
        $_tip['title'] = is_string($_field['tip']) ? $_field['tip'] : $_field['title'];

        $_field_id_top = $_field['id'] ? 'id="field-' . $_field['id'] . '"' : '';
        $_error        = Form::error($_field['key']) ? 'error' : '';
        $_readonly     = $_field['readonly'] ? 'disabled="disabled"' : '';
        $_readonly_cls = $_field['readonly'] ? 'readonly' : '';
        $_class        = $_field['class'] ? 'class="' . $_field['class'] . '"' : '';

        // --------------------------------------------------------------------------

        $_out = '<div class="field boolean ' . $_error . ' ' . $_field['oddeven'] . ' ' . $_readonly_cls . '" ' . $_field_id_top . '>';

        //  Does the field have an id?
        $_field['id'] = $_field['id'] ? 'id="' . $_field['id'] . '" ' : '';

        //  Any data attributes?
        $aDataAttr = array_merge(
            [
                'is-boolean-field' => 'true',
                'text-on'          => $_field['text_on'],
                'text-off'         => $_field['text_off'],
            ],
            $_field['data']
        );

        array_walk($aDataAttr, function (&$sValue, $sKey) {
            $sValue = sprintf(
                'data-%s="%s"',
                $sKey,
                $sValue
            );
        });

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

        $_out .= Form::boolean(
            $_field['key'],
            true,
            $_selected,
            implode(
                ' ',
                array_merge(
                    [
                        $_field['id'],
                        $_readonly,
                        $_class,
                    ],
                    $aDataAttr
                )
            )
        );

        //  Tip
        $_out .= static::getTipHtml((object) $_tip);

        //  Error
        $_out .= Form::error($_field['key'], '<span class="alert alert-danger">', '</span>');

        //  Info block
        $_out .= $_field['info'] ? '<small class="info ' . $_field['info_class'] . '">' . $_field['info'] . '</small>' : '';

        $_out .= '</span>';
        $_out .= '</div>';

        // --------------------------------------------------------------------------

        return $_out;
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a form field using the "radio" input type
     *
     * @param array  $aField   The config array
     * @param array  $aOptions The options to use for the radios (DEPRECATED: use $aField['options'] instead)
     * @param string $sTip     An optional tip
     *
     * @return string
     */
    public static function radio($aField, $aOptions = null, $sTip = '')
    {
        $aField['type'] = Form::FIELD_RADIO;
        return static::checkbox($aField, $aOptions, $sTip);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a form field using the "checkbox" input type
     *
     * @param array  $field   The config array
     * @param array  $options The options to use for the checkboxes (DEPRECATED: use $field['options'] instead)
     * @param string $tip     An optional tip
     *
     * @return string
     *
     * @todo (Pablo - 2020-01-15) - use render() or html()
     */
    public static function checkbox($field, $options = null, $tip = '')
    {
        //  Set var defaults
        $_field                = [];
        $_field['type']        = isset($field['type']) ? $field['type'] : Form::FIELD_CHECKBOX;
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
        $_field['data']        = isset($field['data']) ? $field['data'] : [];

        $_tip          = [];
        $_tip['class'] = is_array($_field['tip']) && isset($_field['tip']['class']) ? $_field['tip']['class'] : null;
        $_tip['title'] = is_array($_field['tip']) && isset($_field['tip']['title']) ? $_field['tip']['title'] : null;
        $_tip['title'] = is_string($_field['tip']) ? $_field['tip'] : $_field['title'];

        $_field_id_top = $_field['id'] ? 'id="field-' . $_field['id'] . '"' : '';
        $_error        = Form::error($_field['key']) ? 'error' : '';

        // --------------------------------------------------------------------------

        //  Prep the containers attributes
        $_field_attributes = [];

        //  Move revealer properties to the container if both are present
        //  If only revealer is present then it is a control

        if (array_key_exists('revealer', $_field['data']) && array_key_exists('reveal-on', $_field['data'])) {
            $_field_attributes[] = sprintf(
                'data-%s="%s"',
                'revealer',
                $_field['data']['revealer']
            );
            $_field_attributes[] = sprintf(
                'data-%s="%s"',
                'reveal-on',
                $_field['data']['reveal-on']
            );
        }

        $_field_attributes = implode(' ', $_field_attributes);

        // --------------------------------------------------------------------------

        $_out = '<div class="field ' . $_field['type'] . ' ' . $_error . ' ' . $_field['oddeven'] . '" ' . $_field_id_top . ' ' . $_field_attributes . '>';

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

        /** @var Input $oInput */
        $oInput = Factory::service('Input');

        //  Field
        if (substr($_field['key'], -2) == '[]') {

            //  Field is an array, need to look for the value
            $_values        = $oInput->post(substr($_field['key'], 0, -2));
            $_data_selected = isset($_field['options'][0]['selected']) ? $_field['options'][0]['selected'] : false;
            $_selected      = $oInput->post() ? false : $_data_selected;

            if (is_array($_values) && array_search($_field['options'][0]['value'], $_values) !== false) {
                $_selected = true;
            }

        } else {
            //  Normal field, continue as normal Mr Norman!
            if ($oInput->post($_field['key'])) {
                $_selected = $oInput->post($_field['key']) == $_field['options'][0]['value'];
            } else {
                $_selected = isset($_field['options'][0]['selected']) ? $_field['options'][0]['selected'] : false;
            }
        }

        $_key = isset($_field['options'][0]['key']) ? $_field['options'][0]['key'] : $_field['key'];

        if ($_field['type'] == Form::FIELD_CHECKBOX) {

            $_out .= Form::checkbox(
                $_key,
                $_field['options'][0]['value'],
                $_selected,
                $_id . $_disabled
            );
            $_out .= '<span class="text">' . $_field['options'][0]['label'] . '</span>';
        } elseif ($_field['type'] == Form::FIELD_RADIO) {

            $_out .= Form::radio(
                $_key,
                $_field['options'][0]['value'],
                $_selected,
                $_id . $_disabled
            );
            $_out .= '<span class="text">' . $_field['options'][0]['label'] . '</span>';
        }

        //  Tip
        $_out .= static::getTipHtml((object) $_tip);

        $_out .= '</span>';
        $_out .= '</label>';

        //  Remaining options
        $numOptions = count($_field['options']);
        for ($i = 1; $i < $numOptions; $i++) {

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
            if (substr($_field['key'], -2) == '[]') {

                //  Field is an array, need to look for the value
                $_values        = $oInput->post(substr($_field['key'], 0, -2));
                $_data_selected = isset($_field['options'][$i]['selected']) ? $_field['options'][$i]['selected'] : false;
                $_selected      = $oInput->post() ? false : $_data_selected;

                if (is_array($_values) && array_search($_field['options'][$i]['value'], $_values) !== false) {
                    $_selected = true;
                }

            } else {
                //  Normal field, continue as normal Mr Norman!
                if ($oInput->post($_field['key'])) {
                    $_selected = $oInput->post($_field['key']) == $_field['options'][$i]['value'];
                } else {
                    $_selected = isset($_field['options'][$i]['selected']) ? $_field['options'][$i]['selected'] : false;
                }
            }

            $_key = isset($_field['options'][$i]['key']) ? $_field['options'][$i]['key'] : $_field['key'];

            if ($_field['type'] == Form::FIELD_CHECKBOX) {

                $_out .= Form::checkbox(
                    $_key,
                    $_field['options'][$i]['value'],
                    $_selected,
                    $_id . $_disabled
                );
                $_out .= '<span class="text">' . $_field['options'][$i]['label'] . '</span>';
            } elseif ($_field['type'] == Form::FIELD_RADIO) {

                $_out .= Form::radio(
                    $_key,
                    $_field['options'][$i]['value'],
                    $_selected,
                    $_id . $_disabled
                );
                $_out .= '<span class="text">' . $_field['options'][$i]['label'] . '</span>';
            }

            $_out .= '</span>';
            $_out .= '</label>';
        }

        //  Error
        $_out .= Form::error($_field['key'], '<span class="alert alert-danger">', '</span>');

        $_out .= '</div>';

        // --------------------------------------------------------------------------

        return $_out;
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a submit button which is aligned properly with the fields
     *
     * @param array $aField The configuration array
     *
     * @return string
     */
    public static function submit(array $aField)
    {
        $sKey     = ArrayHelper::getFromArray('key', $aField, []);
        $sDefault = ArrayHelper::getFromArray('default', $aField, '');

        $aField['html'] = Form::submit($sKey, $sDefault);
        return static::html($aField);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a button which is aligned properly with the fields
     *
     * @param array $aField The configuration array
     *
     * @return string
     */
    public static function button(array $aField)
    {
        $sKey     = ArrayHelper::getFromArray('key', $aField, []);
        $sDefault = ArrayHelper::getFromArray('default', $aField, '');

        $aField['html'] = Form::button($sKey, $sDefault);
        return static::html($aField);
    }

    // --------------------------------------------------------------------------

    /**
     * This function renders an upload field
     *
     * @param array $aField The configuration array
     *
     * @return string
     */
    public static function upload(array $aField)
    {
        $aField['html'] = Form::upload($aField);
        return static::html($aField);
    }

    // --------------------------------------------------------------------------

    /**
     * This function renders a timecode input
     *
     * @param array $aField The configuration array
     *
     * @return string
     */
    public static function timecode(array $aField): string
    {
        $sKey     = getFromArray('key', $aField);
        $sDefault = getFromArray('default', $aField);
        $sDefault = set_value($sKey, $sDefault);

        $aField['html'] = Form::timecode($sKey, $sDefault);

        return static::html($aField);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a form field containing the media manager to select a file.
     *
     * @param array $aField The config array
     *
     * @return string
     *
     * @todo (Pablo - 2020-01-15) - Remove this when \Nails\Admin\Service\Form is complete
     */
    public static function cdn_object_picker($aField): string
    {
        return \Nails\Cdn\Helper\Form::form_field_cdn_object_picker($aField);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a form field containing multiple object pickers
     *
     * @param array $aField The config array
     *
     * @return string
     *
     * @todo (Pablo - 2020-01-15) - Remove this when \Nails\Admin\Service\Form is complete
     */
    public static function cdn_object_picker_multi($aField): string
    {
        return \Nails\Cdn\Helper\Form::form_field_cdn_object_picker_multi($aField);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a form field containing multiple object pickers
     *
     * @param array $aField The config array
     *
     * @return string
     *
     * @todo (Pablo - 2020-01-15) - Remove this when \Nails\Admin\Service\Form is complete
     */
    public static function cdn_object_picker_multi_with_label($aField): string
    {
        return \Nails\Cdn\Helper\Form::form_field_cdn_object_picker_multi_with_label($aField);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a form field containing a button to open the CMS widgets manager
     *
     * @param array $aField The config array
     *
     * @return string
     *
     * @todo (Pablo - 2020-01-15) - Remove this when \Nails\Admin\Service\Form is complete
     */
    public static function cms_widgets($aField): string
    {
        return \Nails\Cms\Helper\Form::form_field_cms_widgets($aField);
    }

    // --------------------------------------------------------------------------

    /**
     * This function renders a dynamic table
     *
     * @param array $aField The configuration array
     *
     * @return string
     *
     * @todo (Pablo - 2020-01-15) - Remove this when \Nails\Admin\Service\Form is complete
     */
    public static function dynamic_table(array $aField): string
    {
        return \Nails\Admin\Helper\Form::form_field_dynamic_table($aField);
    }

    // --------------------------------------------------------------------------

    /**
     * This function renders an address field
     *
     * @param array $aField The configuration array
     *
     * @return string
     *
     * @todo (Pablo - 2020-03-19) - Remove this when \Nails\Admin\Service\Form is complete
     */
    public static function address(array $aField): string
    {
        return \Nails\Address\Helper\Form::address($aField);
    }

    // --------------------------------------------------------------------------

    public static function getTipHtml(object $oTip): string
    {
        $sClass = $oTip->class ?? 'tip';
        $sTitle = htmlentities($oTip->title ?? '', ENT_QUOTES);

        if (empty($sTitle)) {
            return '';
        }

        return <<<EOT
            <span class="$sClass">
                <span class="hint--left hint--large" aria-label="$sTitle">
                    <b class="fa fa-question-circle fa-lg"></b>
                </span>
            </span>
        EOT;
    }
}
