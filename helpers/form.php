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
     * @param  mixed  $data  The field's name or the config array
     * @param  mixed  $value The form element's value
     * @param  string $extra Any additional attributes to give to the field
     * @return string
     */
    function form_email($data = '', $value = '', $extra = '')
    {
        $defaults = array(
            'type' => 'email',
            'name' => ((!is_array($data)) ? $data : ''),
            'value' => $value
        );

        return "<input "._parse_form_attributes($data, $defaults).$extra." />";
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_number')) {

    /**
     * Generates an input using the "number" type
     * @param  mixed  $data  The field's name or the config array
     * @param  mixed  $value The form element's value
     * @param  string $extra Any additional attributes to give to the field
     * @return string
     */
    function form_number($data = '', $value = '', $extra = '')
    {
        $defaults = array(
            'type' => 'number',
            'name' => ((!is_array($data)) ? $data : ''),
            'value' => $value,
            'step' => empty($data['step']) ? 'any' : $data['step']
        );

        return "<input "._parse_form_attributes($data, $defaults).$extra." />";
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_url')) {

    /**
     * Generates an input using the "url" type
     * @param  mixed  $data  The field's name or the config array
     * @param  mixed  $value The form element's value
     * @param  string $extra Any additional attributes to give to the field
     * @return string
     */
    function form_url($data = '', $value = '', $extra = '')
    {
        $defaults = array(
            'type' => 'url',
            'name' => ((!is_array($data)) ? $data : ''),
            'value' => $value
        );

        return "<input "._parse_form_attributes($data, $defaults).$extra." />";
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_open')) {

    /**
     * Form Declaration
     * Creates the opening portion of the form, taking into account Secure base URL
     * @param   string  the URI segments of the form destination
     * @param   array   a key/value pair of attributes
     * @param   array   a key/value pair hidden data
     * @return  string
     */
    function form_open($action = '', $attributes = '', $hidden = array())
    {
        $CI =& get_instance();

        if ($attributes == '') {
            $attributes = 'method="post"';
        }

        // If an action is not a full URL then turn it into one
        if ($action && strpos($action, '://') === false) {
            $action = $CI->config->site_url($action);
        }

        // If no action is provided then set to the current url
        $action || $action = $CI->config->site_url($CI->uri->uri_string());

        $form = '<form action="'.$action.'"';

        $form .= _attributes_to_string($attributes, true);

        $form .= '>';

        // Add CSRF field if enabled, but leave it out for GET requests and requests to external websites
        $_base_url          = $CI->config->base_url();
        $_secure_base_url   = $CI->config->secure_base_url();

        if ($CI->config->item('csrf_protection') === true && !(strpos($action, $_base_url) === false || strpos($form, 'method="get"'))) {
            $hidden[$CI->security->get_csrf_token_name()] = $CI->security->get_csrf_hash();
        }

        //  If the secure_base_url is different, then do a check for that domain/url too.
        if ($_base_url != $_secure_base_url) {
            if ($CI->config->item('csrf_protection') === true && !(strpos($action, $_secure_base_url) === false || strpos($form, 'method="get"'))) {
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
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     * @return string        The form HTML
     */
    function form_field($field, $tip = '')
    {
        //  Set var defaults
        $_field_id          = isset($field['id'])             ? $field['id']          : null;
        $_field_type        = isset($field['type'])           ? $field['type']        : 'text';
        $_field_oddeven     = isset($field['oddeven'])        ? $field['oddeven']     : null;
        $_field_key         = isset($field['key'])            ? $field['key']         : null;
        $_field_label       = isset($field['label'])          ? $field['label']       : null;
        $_field_default     = isset($field['default'])        ? $field['default']     : null;
        $_field_sub_label   = isset($field['sub_label'])      ? $field['sub_label']   : null;
        $_field_required    = isset($field['required'])       ? $field['required']    : false;
        $_field_placeholder = isset($field['placeholder'])    ? $field['placeholder'] : null;
        $_field_readonly    = isset($field['readonly'])       ? $field['readonly']    : false;
        $_field_error       = isset($field['error'])          ? $field['error']       : false;
        $_field_class       = isset($field['class'])          ? $field['class']       : '';
        $_field_data        = isset($field['data'])           ? $field['data']        : array();
        $_field_info        = isset($field['info'])           ? $field['info']        : false;
        $_field_tip         = isset($field['tip'])            ? $field['tip']         : $tip;

        $_tip               = array();
        $_tip['class']      = is_array($_field_tip) && isset($_field_tip['class'])  ? $_field_tip['class']  : 'fa fa-question-circle fa-lg tip';
        $_tip['rel']        = is_array($_field_tip) && isset($_field_tip['rel'])    ? $_field_tip['rel']    : 'tipsy-left';
        $_tip['title']      = is_array($_field_tip) && isset($_field_tip['title'])  ? $_field_tip['title']  : null;
        $_tip['title']      = is_string($_field_tip) ? $_field_tip : $_tip['title'];

        $_field_id_top      = $_field_id ? 'id="field-' . $_field_id . '"': '';
        $_error             = form_error($_field_key) || $_field_error ? 'error' : '';
        $_error_class       = $_error ? 'error' : '';
        $_readonly          = $_field_readonly ? 'readonly="readonly"' : '';
        $_readonly_cls      = $_field_readonly ? 'readonly' : '';

        // --------------------------------------------------------------------------

        //  Is the label required?
        $_field_label .= $_field_required ? '*' : '';

        //  Prep sublabel
        $_field_sub_label = $_field_sub_label ? '<small>' . $_field_sub_label . '</small>' : '';

        //  Has the field got a tip?
        $_tipclass  = $_tip['title'] ? 'with-tip' : '';
        $_tip       = $_tip['title'] ? '<b class="' . $_tip['class'] . '" rel="' . $_tip['rel'] . '" title="' . htmlentities($_tip['title'], ENT_QUOTES) . '"></b>' : '';

        // --------------------------------------------------------------------------

        //  Prep the field's attributes
        $_attr = '';

        //  Does the field have an id?
        $_attr .= $_field_id ? 'id="' . $_field_id . '" ' : '';

        //  Any data attributes?
        foreach ($_field_data as $attr => $value) :

            $_attr .= ' data-' . $attr . '="' . $value . '"';

        endforeach;

        // --------------------------------------------------------------------------

        //  Generate the field's HTML
        $sFieldAttr  = $_attr;
        $sFieldAttr .= ' class="' . $_field_class . '" ';
        $sFieldAttr .= 'placeholder="' . htmlentities($_field_placeholder, ENT_QUOTES) . '" ';
        $sFieldAttr .= $_readonly;

        switch ($_field_type) {

            case 'password':
            case 'email':
            case 'number':
            case 'url':

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

                    $_field_type   = 'textarea';
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
        $info_block = $_field_info ? '<small class="info">' . $_field_info . '</small>' : '';

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

        // --------------------------------------------------------------------------

        return $_out;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_email')) {

    /**
     * Generates a form field using the "email" input type
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
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
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
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
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     * @return string        The form HTML
     */
    function form_field_url($field, $tip = '')
    {
        $field['type'] = 'url';
        return form_field($field, $tip);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_password')) {

    /**
     * Generates a form field using the "password" input type
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
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
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
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
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
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
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     * @return string        The form HTML
     */
    function form_field_text($field, $tip = '')
    {
        $field['type'] = 'text';
        return form_field($field, $tip);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_mm')) {

    /**
     * Generates a form field containing the media manager to select a file.
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     * @return string        The form HTML
     */
    function form_field_mm($field, $tip = '')
    {
        //  Set var defaults
        $_field                 = array();
        $_field['id']           = isset($field['id'])             ? $field['id']          : null;
        $_field['type']         = isset($field['type'])           ? $field['type']        : 'text';
        $_field['oddeven']      = isset($field['oddeven'])        ? $field['oddeven']     : null;
        $_field['key']          = isset($field['key'])            ? $field['key']         : null;
        $_field['label']        = isset($field['label'])          ? $field['label']       : null;
        $_field['default']      = isset($field['default'])        ? $field['default']     : null;
        $_field['sub_label']    = isset($field['sub_label'])      ? $field['sub_label']   : null;
        $_field['required']     = isset($field['required'])       ? $field['required']    : false;
        $_field['placeholder']  = isset($field['placeholder'])    ? $field['placeholder'] : null;
        $_field['readonly']     = isset($field['readonly'])       ? $field['readonly']    : false;
        $_field['error']        = isset($field['error'])          ? $field['error']       : false;
        $_field['bucket']       = isset($field['bucket'])         ? $field['bucket']      : false;
        $_field['class']        = isset($field['class'])          ? $field['class']       : false;
        $_field['data']         = isset($field['data'])           ? $field['data']        : array();
        $_field['tip']          = isset($field['tip'])            ? $field['tip']         : $tip;

        $_tip                   = array();
        $_tip['class']          = is_array($_field['tip']) && isset($_field['tip']['class']) ? $_field['tip']['class'] : 'fa fa-question-circle fa-lg tip';
        $_tip['rel']            = is_array($_field['tip']) && isset($_field['tip']['rel']) ? $_field['tip']['rel'] : 'tipsy-left';
        $_tip['title']          = is_array($_field['tip']) && isset($_field['tip']['title']) ? $_field['tip']['title'] : null;
        $_tip['title']          = is_string($_field['tip']) ? $_field['tip'] : $_field['title'];

        $_field_error_cls       = form_error($_field['key']) || $_field['error'] ? 'error' : '';
        $_readonly_cls          = $_field['readonly'] ? 'readonly' : '';

        // --------------------------------------------------------------------------

        //  Generate a unique ID for this field
        $_id = 'field_mm_' . md5(microtime());

        // --------------------------------------------------------------------------

        //  Container data
        $_field_oddeven = $_field['oddeven'];
        $_field_type    = $_field['type'];

        // --------------------------------------------------------------------------

        //  Label
        $_field_label = $_field['label'];
        $_field_label .= $_field['required'] ? '*' : '';
        $_field_label .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';

        // --------------------------------------------------------------------------

        //  Choose image button
        $_force_secure = isPageSecure();
        $_url = cdnManagerUrl($_field['bucket'], array('_nails_forms', '_callback_form_field_mm'), $_id, $_force_secure);

        //  Is the site running on SSL? If so then change the protocol so as to avoid 'protocols don't match' errors
        if (isset($_SERVER['HTTPS']) && strtoupper($_SERVER['HTTPS']) == 'ON') :

            $_url = str_replace('http://', 'https://', $_url);

        endif;

        // --------------------------------------------------------------------------

        //  Tip
        $_field_tipclass    = $_tip['title'] ? 'with-tip' : '';
        $_field_tip         = $_tip['title'] ? '<b class="' . $_tip['class'] . '" rel="' . $_tip['rel'] . '" title="' . htmlentities($_tip['title'], ENT_QUOTES) . '"></b>' : '';

        // --------------------------------------------------------------------------

        //  If there's post data, use that value instead
        $_field['default'] = set_value($_field['key'], $_field['default']);

        //  The actual field which is submitted
        $_field_field = '<input type="hidden" name="' . $_field['key'] . '"  class="mm-file-value" value="' . $_field['default'] . '" />';

        // --------------------------------------------------------------------------

        //  Remove button
        $_remove_display = $_field['default'] ? 'inline-block' : 'none';

        // --------------------------------------------------------------------------

        //  If a default has been specified then show a download link
        $_field_download = $_field['default'] ? anchor(cdnServe($_field['default'], true), 'Download File', 'class="btn btn-xs btn-warning"') : '';

        // --------------------------------------------------------------------------

        //  Error
        if ($_field_error_cls && $_field['error']) :

            $_field_error = '<span class="alert alert-danger">' . $_field['error'] . '</span>';

        elseif ($_field_error_cls) :

            $_field_error = form_error($_field['key'], '<span class="alert alert-danger">', '</span>');

        else :

            $_field_error = '';

        endif;

        // --------------------------------------------------------------------------

        //  Quick script to instantiate the field, not indented due to heredoc syntax
        $oCdn    = Factory::service('Cdn', 'nailsapp/module-cdn');
        $_scheme = $oCdn->urlServeScheme(true);

        /**
         * Replace the Mustache style syntax; this could/does get used in mustache
         * templates so these fields get stripped out
         */

        $_scheme = str_replace('{{bucket}}', '{[bucket]}', $_scheme);
        $_scheme = str_replace('{{filename}}', '{[filename]}', $_scheme);
        $_scheme = str_replace('{{extension}}', '{[extension]}', $_scheme);

        $_out  = '<div class="field mm-file ' . $_field_error_cls . ' ' . $_field_oddeven . ' ' . $_readonly_cls . ' ' . $_field_type . '" id="' . $_id . '" data-scheme="' . $_scheme . '">';
        $_out .= '<label>';
        $_out .= '  <span class="label">';
        $_out .= '      ' . $_field_label;
        $_out .= '  </span>';
        $_out .= '  <span class="mm-file-container input ' . $_field_tipclass . '">';
        $_out .= '      <a href="' . $_url . '" data-fancybox-type="iframe" data-width="80%" data-height="80%" class="btn btn-primary mm-file-choose">';
        $_out .= '          Choose';
        $_out .= '      </a>';
        $_out .= '      ' . $_field_tip;
        $_out .= '      <br />';
        $_out .= '      <a href="#" class="btn btn-xs btn-danger mm-file-remove" style="display:' . $_remove_display . '">';
        $_out .= '          Remove';
        $_out .= '      </a>';
        $_out .= '      <span class="mm-file-preview">';
        $_out .= '          ' . $_field_download;
        $_out .= '      </span>';
        $_out .= '      ' . $_field_error;
        $_out .= '  </span>';
        $_out .= '</label>';
        $_out .= '<br /><br />';
        $_out .= $_field_field;
        $_out .= '</div>';

        // --------------------------------------------------------------------------

        return $_out;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_mm_image')) {

    /**
     * Generates a form field containing the media manager to select an image
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     * @return string        The form HTML
     */
    function form_field_mm_image($field, $tip = '')
    {
        //  Set var defaults
        $_field                 = array();
        $_field['id']           = isset($field['id'])             ? $field['id']          : null;
        $_field['type']         = isset($field['type'])           ? $field['type']        : 'text';
        $_field['oddeven']      = isset($field['oddeven'])        ? $field['oddeven']     : null;
        $_field['key']          = isset($field['key'])            ? $field['key']         : null;
        $_field['label']        = isset($field['label'])          ? $field['label']       : null;
        $_field['default']      = isset($field['default'])        ? $field['default']     : null;
        $_field['sub_label']    = isset($field['sub_label'])      ? $field['sub_label']   : null;
        $_field['required']     = isset($field['required'])       ? $field['required']    : false;
        $_field['placeholder']  = isset($field['placeholder'])    ? $field['placeholder'] : null;
        $_field['readonly']     = isset($field['readonly'])       ? $field['readonly']    : false;
        $_field['error']        = isset($field['error'])          ? $field['error']       : false;
        $_field['bucket']       = isset($field['bucket'])         ? $field['bucket']      : false;
        $_field['class']        = isset($field['class'])          ? $field['class']       : false;
        $_field['data']         = isset($field['data'])           ? $field['data']        : array();
        $_field['tip']          = isset($field['tip'])            ? $field['tip']         : $tip;

        $_tip                   = array();
        $_tip['class']          = is_array($_field['tip']) && isset($_field['tip']['class']) ? $_field['tip']['class'] : 'fa fa-question-circle fa-lg tip';
        $_tip['rel']            = is_array($_field['tip']) && isset($_field['tip']['rel']) ? $_field['tip']['rel'] : 'tipsy-left';
        $_tip['title']          = is_array($_field['tip']) && isset($_field['tip']['title']) ? $_field['tip']['title'] : null;
        $_tip['title']          = is_string($_field['tip']) ? $_field['tip'] : $_field['title'];

        $_field_error_cls       = form_error($_field['key']) || $_field['error'] ? 'error' : '';
        $_readonly_cls          = $_field['readonly'] ? 'readonly' : '';

        // --------------------------------------------------------------------------

        //  Generate a unique ID for this field
        $_id = 'field_mm_image_' . md5(microtime());

        // --------------------------------------------------------------------------

        //  Container data
        $_field_oddeven = $_field['oddeven'];
        $_field_type    = $_field['type'];

        // --------------------------------------------------------------------------

        //  Label
        $_field_label = $_field['label'];
        $_field_label .= $_field['required'] ? '*' : '';
        $_field_label .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';

        // --------------------------------------------------------------------------

        //  Choose image button
        $_force_secure = isPageSecure();
        $_url = cdnManagerUrl($_field['bucket'], array('_nails_forms', '_callback_form_field_mm_image'), $_id, $_force_secure);

        //  Is the site running on SSL? If so then change the protocol so as to avoid 'protocols don't match' errors
        if (isset($_SERVER['HTTPS']) && strtoupper($_SERVER['HTTPS']) == 'ON') :

            $_url = str_replace('http://', 'https://', $_url);

        endif;

        // --------------------------------------------------------------------------

        //  Tip
        $_field_tipclass    = $_tip['title'] ? 'with-tip' : '';
        $_field_tip         = $_tip['title'] ? '<b class="' . $_tip['class'] . '" rel="' . $_tip['rel'] . '" title="' . htmlentities($_tip['title'], ENT_QUOTES) . '"></b>' : '';

        // --------------------------------------------------------------------------

        //  If there's post data, use that value instead
        $_field['default'] = set_value($_field['key'], $_field['default']);

        //  The actual field which is submitted
        $_field_field = '<input type="hidden" name="' . $_field['key'] . '"  class="mm-image-value" value="' . $_field['default'] . '" />';

        // --------------------------------------------------------------------------

        //  Remove button
        $_remove_display = $_field['default'] ? 'inline-block' : 'none';

        // --------------------------------------------------------------------------

        //  If a default has been specified then show a download link
        $_field_preview = $_field['default'] ? img(cdnScale($_field['default'], 100, 100)) : '';

        // --------------------------------------------------------------------------

        //  Error
        if ($_field_error_cls && $_field['error']) {

            $_field_error = '<span class="alert alert-danger">' . $_field['error'] . '</span>';

        } elseif ($_field_error_cls) {

            $_field_error = form_error($_field['key'], '<span class="alert alert-danger">', '</span>');

        } else {

            $_field_error = '';
        }

        // --------------------------------------------------------------------------

        //  Quick script to instantiate the field, not indented due to heredoc syntax
        $oCdn    = Factory::service('Cdn', 'nailsapp/module-cdn');
        $_scheme = $oCdn->urlScaleScheme();

        $_scheme = str_replace('{{width}}', 100, $_scheme);
        $_scheme = str_replace('{{height}}', 100, $_scheme);

        /**
         * Replace the Mustache style syntax; this could/does get used in mustache
         * templates so these fields get stripped out
         */

        $_scheme = str_replace('{{bucket}}', '{[bucket]}', $_scheme);
        $_scheme = str_replace('{{filename}}', '{[filename]}', $_scheme);
        $_scheme = str_replace('{{extension}}', '{[extension]}', $_scheme);

        $_out  = '<div class="field mm-image ' . $_field_error_cls . ' ' . $_field_oddeven . ' ' . $_readonly_cls . ' ' . $_field_type . '" id="' . $_id . '" data-scheme="' . $_scheme . '">';
        $_out .= '<label>';
        $_out .= '  <span class="label">';
        $_out .= '      ' . $_field_label;
        $_out .= '  </span>';
        $_out .= '  <span class="mm-image-preview">';
        $_out .= '      ' . $_field_preview;
        $_out .= '  </span>';
        $_out .= '  <span class="mm-image-container input ' . $_field_tipclass . '">';
        $_out .= '      <a href="' . $_url . '" data-fancybox-type="iframe" data-width="80%" data-height="80%" class="btn btn-primary mm-image-choose">';
        $_out .= '          Choose';
        $_out .= '      </a>';
        $_out .= '      ' . $_field_tip;
        $_out .= '      <br />';
        $_out .= '      <a href="#" class="btn btn-xs btn-danger mm-image-remove" style="display:' . $_remove_display . '">';
        $_out .= '          Remove';
        $_out .= '      </a>';
        $_out .= '      ' . $_field_error;
        $_out .= '  </span>';
        $_out .= '</label>';
        $_out .= $_field_field;
        $_out .= '</div>';

        // --------------------------------------------------------------------------

        return $_out;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_multiimage')) {

    /**
     * Generates a form field which allows for the upload of multiple images.
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     * @return string        The form HTML
     */
    function form_field_multiimage($field, $tip = '')
    {
        //  Set var defaults
        $_field_type        = isset($field['type'])           ? $field['type']        : 'text';
        $_field_oddeven     = isset($field['oddeven'])        ? $field['oddeven']     : null;
        $_field_key         = isset($field['key'])            ? $field['key']         : null;
        $_field_label       = isset($field['label'])          ? $field['label']       : null;
        $_field_default     = isset($field['default'])        ? $field['default']     : null;
        $_field_sub_label   = isset($field['sub_label'])      ? $field['sub_label']   : null;
        $_field_required    = isset($field['required'])       ? $field['required']    : false;
        $_field_readonly    = isset($field['readonly'])       ? $field['readonly']    : false;
        $_field_error       = isset($field['error'])          ? $field['error']       : false;
        $_field_bucket      = isset($field['bucket'])         ? $field['bucket']      : false;
        $_field_tip         = isset($field['tip'])            ? $field['tip']         : $tip;

        $_tip               = array();
        $_tip['class']      = is_array($_field_tip) && isset($_field_tip['class'])  ? $_field_tip['class']  : 'fa fa-question-circle fa-lg tip';
        $_tip['rel']        = is_array($_field_tip) && isset($_field_tip['rel'])    ? $_field_tip['rel']    : 'tipsy-left';
        $_tip['title']      = is_array($_field_tip) && isset($_field_tip['title'])  ? $_field_tip['title']  : null;
        $_tip['title']      = is_string($_field_tip) ? $_field_tip : $_field_tip['title'];

        $_error             = form_error($_field_key) || $_field_error ? 'error' : '';
        $_error_class       = $_error ? 'error' : '';
        $_readonly_cls      = $_field_readonly ? 'readonly' : '';

        // --------------------------------------------------------------------------

        //  Generate a unique ID for this field
        $_id = 'field_multiimage_' . md5(microtime());

        // --------------------------------------------------------------------------

        //  Sanitize the key
        $_field_key .= substr($_field_key, -2) != '[]' ? '[]' : '';

        // --------------------------------------------------------------------------

        //  Is the label required?
        $_field_label .= $_field_required ? '*' : '';

        //  Prep sublabel
        $_field_sub_label = $_field_sub_label ? '<small>' . $_field_sub_label . '</small>' : '';

        // --------------------------------------------------------------------------

        //  Set the defaults
        $_field_default = set_value($_field_key, $_field_default);
        $_default_html  = '';

        //  Render any defaults
        if (is_array($_field_default)) :

            foreach ($_field_default as $file) :

                $_default_html .= '<li class="item">';
                $_default_html .= '<a href="#" class="delete" data-object_id="' . $file . '"></a>';
                $_default_html .= img(cdnCrop($file, 92, 92));
                $_default_html .= form_hidden($_field_key, $file);
                $_default_html .= '</li>';

            endforeach;

        endif;

        // --------------------------------------------------------------------------

        //  Error
        if ($_error && $_field_error) :

            $_error = '<span class="alert alert-danger">' . $_field_error . '</span>';

        elseif ($_error) :

            $_error = form_error($_field_key, '<span class="alert alert-danger">', '</span>');

        endif;

        // --------------------------------------------------------------------------

        //  Tip
        $_tipclass  = $_tip['title'] ? 'with-tip' : '';
        $_tip       = $_tip['title'] ? '<b class="' . $_tip['class'] . '" rel="' . $_tip['rel'] . '" title="' . htmlentities($_tip['title'], ENT_QUOTES) . '"></b>' : '';

        // --------------------------------------------------------------------------

        //  Quick script to instantiate the field, not indented due to heredoc syntax
        $oCdn = Factory::service('Cdn', 'nailsapp/module-cdn');

        $_movie_url     = NAILS_ASSETS_URL . 'packages/uploadify/uploadify.swf';
        $_upload_url    = site_url('api/cdn/object/create', isPageSecure());
        $_upload_token  = $oCdn->generateApiUploadToken();
        $_bucket        = $_field_bucket;

$_out = <<<EOT

    <div class="field multiimage $_error_class $_field_oddeven $_readonly_cls $_field_type" id="$_id">
        <label>
            <span class="label">
                $_field_label
                $_field_sub_label
            </span>
            <span class="input $_tipclass">
                <p class="alert alert-danger" id="$_id-uploadify-not-available">
                    <strong>Configuration Error.</strong> Uploadify is not available.
                </p>
                <span id="$_id-uploadify-available" style="display:none;">
                    <ul id="$_id-filelist" class="filelist empty">
                        $_default_html
                        <li class="empty">No Images, add some now.</li>
                    </ul>
                    <button id="$_id-uploadify">Choose Images</button>
                </span>
                $_tip
                $_error
            <span>
        </label>
    </div>

    <script type="text/template" id="$_id-template-uploadify">
        <li class="item uploadify-queue-item" id="$_id-\${fileID}" data-instance_id="\${instanceID}" data-file_id="\${fileID}">
            <a href="#" data-instance_id="\${instanceID}" data-file_id="\${fileID}" class="remove"></a>
            <div class="progress" style="height:0%"></div>
            <div class="data data-cancel">CANCELLED</div>
        </li>
    </script>
    <script type="text/template" id="$_id-template-item">
        <li class="item crunching">
            <div class="crunching"></div>
            <input type="hidden" name="$_field_key" />
        </li>
    </script>
    <div id="$_id-dialog-confirm-delete" title="Confirm Delete" style="display:none;">
        <p>
            <span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 0 0;"></span>
            This item will be removed from the interface and cannot be recovered.
            <strong>Are you sure?</strong>
        </p>
    </div>

    <script type="text/javascript">

    if (typeof($.fn.uploadify) === 'function')
    {
        $('#$_id-uploadify-not-available').hide();
        $('#$_id-uploadify-available').show();

        // --------------------------------------------------------------------------

        $('#$_id-uploadify').uploadify(
        {
            'debug': false,
            'auto': true,
            'swf': '$_movie_url',
            'buttonText':'Add Images',
            'uploader': '$_upload_url',
            'fileObjName': 'upload',
            'fileTypeExts': '*.gif; *.jpg; *.jpeg; *.png',
            'queueID': '$_id-filelist',
            'formData':
            {
                'token': '$_upload_token',
                'bucket': '$_bucket',
                'return': 'URL|THUMB|92x92'
            },
            'itemTemplate': $('#$_id-template-uploadify').html(),
            'onSelect': function()
            {
                if ($('#$_id-filelist li').length)
                {
                    $('#$_id-filelist').removeClass('empty');
                }
            },
            'onUploadStart': function()
            {
                window.onbeforeunload = function()
                {
                    return 'Uploads are in progress. Leaving this page will cause them to stop.';
                };

                //  Disable tabs - SWFUpload aborts uploads if it is hidden.
                $('ul.tabs li a').addClass('disabled');
            },
            'onQueueComplete': function()
            {
                window.onbeforeunload = null;
                $('ul.tabs li a').removeClass('disabled');
            },
            'onUploadProgress': function(file, bytesUploaded, bytesTotal)
            {
                var _percent = bytesUploaded / bytesTotal * 100;
                $('#$_id-' + file.id + ' .progress').css('height', _percent + '%');
            },
            'onUploadSuccess': function(file, data)
            {
                var _data = JSON.parse(data);

                // --------------------------------------------------------------------------

                var _html = $.trim($('#$_id-template-item').html());
                var _item = $($.parseHTML(_html));

                _item.attr('id', '$_id-' + file.id + '-complete');
                $('#$_id-' + file.id).replaceWith(_item);

                // --------------------------------------------------------------------------

                var _target = $('#$_id-' + file.id + '-complete');

                if (!_target.length)
                {
                    _html = $.trim($('#$_id-template-item').html());
                    _item = $($.parseHTML(_html));

                    _item.attr('id', '$_id-' + file.id + '-complete');
                    $('#' + file.id).replaceWith(_item);

                    _target = $('#$_id-' + file.id + '-complete');
                }

                // --------------------------------------------------------------------------

                //  Switch the response code
                if (_data.status === 200)
                {
                    //  Insert the image
                    var _img = $('<img>').attr('src', _data.object_url[0]).on('load', function() {
                        _target.removeClass('crunching');
                    });
                    var _del = $('<a>').attr({
                        'href': '#',
                        'class': 'delete',
                        'data-object_id': _data.object_id
                    });

                    _target.append(_img).append(_del).find('input').val(_data.object_id);

                }
                else
                {
                    //  An error occurred
                    var _filename = $('<p>').addClass('filename').text(file.name);
                    var _message = $('<p>').addClass('message').text(_data.error);

                    _target.addClass('error').append(_filename).append(_message).removeClass('crunching');
                }
            },
            'onUploadError': function(file, errorCode, errorMsg, errorString)
            {
                var _target = $('#$_id-' + file.id + '-complete');

                if (!_target.length)
                {
                    var _html = $.trim($('#$_id-template-item').html());
                    var _item = $($.parseHTML(_html));

                    _item.attr('id', '$_id-' + file.id + '-complete');
                    $('#$_id-' + file.id).replaceWith(_item);

                    _target = $('#$_id-' + file.id + '-complete');
                }

                var _filename = $('<p>').addClass('filename').text(file.name);
                var _message = $('<p>').addClass('message').text(errorString);

                _target.addClass('error').append(_filename).append(_message).removeClass('crunching');
            }

        });

        if (typeof($.fn.sortable) === 'function')
        {
            $('#$_id-filelist').disableSelection().sortable({
                placeholder: 'item placeholder',
                items: "li.item"
            });
        }

        //  Remove an item from the queue
        $(document).on('click', '#$_id-filelist .item .remove', function()
        {
            var _instance_id = $(this).data('instance_id');
            var _file_id = $(this).data('file_id');

            $('#$_id-' + _instance_id).uploadify('cancel', _file_id);
            $('#$_id-' + _file_id + ' .data-cancel').text('Cancelled').show();
            $('#$_id-' + _file_id).addClass('cancelled');

            if ($('#$_id-filelist li.item:not(.cancelled)').length === 0)
            {
                $('#$_id-filelist').addClass('empty');
                $('#$_id-filelist li.empty').css('opacity', 0).delay(1000).animate({
                    opacity: 1
                }, 250);
            }

            return false;

        });

        //  Deletes an uploaded image
        $(document).on('click', '#$_id-filelist .item .delete', function()
        {
            var _object = this;

            $('#$_id-dialog-confirm-delete').dialog(
            {
                resizable: false,
                draggable: false,
                modal: true,
                dialogClass: "no-close",
                buttons:
                {
                    "Delete Image": function()
                    {
                        var _object_id = $(_object).data('object_id');

                        //  Send off the delete request
                        var _call = {
                            'controller'    : 'cdn/object',
                            'method'        : 'delete',
                            'action'        : 'POST',
                            'data'          :
                            {
                                'object_id': _object_id
                            }
                        };
                        var _api = new window.NAILS_API();
                        _api.call(_call);

                        // --------------------------------------------------------------------------

                        $(_object).closest('li.item').addClass('deleted').fadeOut('slow', function()
                        {
                            $(_object).remove();
                        });

                        // --------------------------------------------------------------------------

                        //  Show the empty screens
                        if ($('#$_id-filelist li.item:not(.deleted)').length === 0)
                        {
                            $('#$_id-filelist').addClass('empty');
                        }

                        // --------------------------------------------------------------------------

                        //  Close dialog
                        $(this).dialog("close");
                    },
                    Cancel: function()
                    {
                        $(this).dialog("close");
                    }
                }
            });

            return false;
        });
    }

    </script>

EOT;

        // --------------------------------------------------------------------------

        return $_out;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_date')) {

    /**
     * Generates a form field for dates
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     * @return string        The form HTML
     */
    function form_field_date($field, $tip = '')
    {
        $_field                 = $field;
        $_field['type']         = 'date';
        $_field['class']        = isset($field['class']) ? $field['class'] . ' date' : 'date';
        $_field['placeholder']  = 'YYYY-MM-DD';

        return form_field($_field, $tip);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_datetime')) {

    /**
     * Generates a form field for datetimes
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     * @return string        The form HTML
     */
    function form_field_datetime($field, $tip = '')
    {
        $_field                 = $field;
        $_field['type']         = 'datetime';
        $_field['class']        = isset($field['class']) ? $field['class'] . ' datetime' : 'datetime';
        $_field['placeholder']  = 'YYYY-MM-DD HH:mm:ss';

        return form_field($_field, $tip);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('form_field_dropdown')) {

    /**
     * Generates a form field using the "select" input type
     * @param  array  $field   The config array
     * @param  array  $options The options to use for the dropdown (DEPRECATED: use $field['options'] instead)
     * @param  string $tip     An optional tip (DEPRECATED: use $field['tip'] instead)
     * @return string          The form HTML
     */
    function form_field_dropdown($field, $options = null, $tip = '')
    {
        //  Set var defaults
        $_field                     = array();
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
        $_field['data']             = isset($field['data']) ? $field['data'] : array();
        $_field['disabled_options'] = isset($field['disabled_options']) ? $field['disabled_options'] : array();
        $_field['info']             = isset($field['info']) ? $field['info'] : array();
        $_field['tip']              = isset($field['tip']) ? $field['tip'] : $tip;
        $_field['options']          = isset($field['options']) ? $field['options'] : $options;

        $_tip                   = array();
        $_tip['class']          = is_array($_field['tip']) && isset($_field['tip']['class']) ? $_field['tip']['class'] : 'fa fa-question-circle fa-lg tip';
        $_tip['rel']            = is_array($_field['tip']) && isset($_field['tip']['rel']) ? $_field['tip']['rel'] : 'tipsy-left';
        $_tip['title']          = is_array($_field['tip']) && isset($_field['tip']['title']) ? $_field['tip']['title'] : null;
        $_tip['title']          = is_string($_field['tip']) ? $_field['tip'] : $_field['title'];

        $_field_id_top  = $_field['id'] ? 'id="field-' . $_field['id'] . '"': '';
        $_error         = form_error($_field['key']) ? 'error' : '';
        $_readonly      = $_field['readonly'] ? 'disabled="disabled"' : '';
        $_readonly_cls  = $_field['readonly'] ? 'readonly' : '';

        // --------------------------------------------------------------------------

        $_out  = '<div class="field dropdown ' . $_error . ' ' . $_readonly_cls . ' ' . $_field['oddeven'] . '" ' . $_field_id_top . '>';
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
        $_out .= '<span class="input ' . $_withtip . '">';

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
        $_out .= '<select name="' . $_field['key'] . '" class="' . $_field['class'] . '" style="' . $_field['style'] . '" ' . $_field['id'] . ' ' . $_readonly . $_placeholder . $_data . '>';

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
        $_out .= $_field['info'] ? '<small class="info">' . $_field['info'] . '</small>' : '';

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
     * @param  array  $field   The config array
     * @param  array  $options The options to use for the dropdown (DEPRECATED: use $field['options'] instead)
     * @param  string $tip     An optional tip (DEPRECATED: use $field['tip'] instead)
     * @return string          The form HTML
     */
    function form_field_dropdown_multiple($field, $options = null, $tip = '')
    {
        //  Set var defaults
        $_field                     = array();
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
        $_field['data']             = isset($field['data']) ? $field['data'] : array();
        $_field['disabled_options'] = isset($field['disabled_options']) ? $field['disabled_options'] : array();
        $_field['info']             = isset($field['info']) ? $field['info'] : array();
        $_field['tip']              = isset($field['tip']) ? $field['tip'] : $tip;

        if (is_null($options)) :

            $options = isset($field['options']) ? $field['options'] : array();

        endif;

        $_tip                   = array();
        $_tip['class']          = is_array($_field['tip']) && isset($_field['tip']['class']) ? $_field['tip']['class'] : 'fa fa-question-circle fa-lg tip';
        $_tip['rel']            = is_array($_field['tip']) && isset($_field['tip']['rel']) ? $_field['tip']['rel'] : 'tipsy-left';
        $_tip['title']          = is_array($_field['tip']) && isset($_field['tip']['title']) ? $_field['tip']['title'] : null;
        $_tip['title']          = is_string($_field['tip']) ? $_field['tip'] : $_field['title'];

        $_field_id_top  = $_field['id'] ? 'id="field-' . $_field['id'] . '"': '';
        $_error         = form_error($_field['key']) ? 'error' : '';
        $_readonly      = $_field['readonly'] ? 'disabled="disabled"' : '';
        $_readonly_cls  = $_field['readonly'] ? 'readonly' : '';

        // --------------------------------------------------------------------------

        $_out  = '<div class="field dropdown ' . $_error . ' ' . $_readonly_cls . ' ' . $_field['oddeven'] . '" ' . $_field_id_top . '>';
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
        $_out .= '<span class="input ' . $_withtip . '">';

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
        if ($_POST) :

            $_key = str_replace('[]', '', $_field['key']);
            $_selected = isset($_POST[$_key]) ? $_POST[$_key] : array();

        else :

            //  Use the 'default' variabel
            $_selected = $_field['default'];

        endif;

        //  Build the select
        $_placeholder = null !== $_field['placeholder'] ? 'data-placeholder="' . htmlentities($_field['placeholder'], ENT_QUOTES) . '"' : '';
        $_out .= '<select name="' . $_field['key'] . '" multiple="multiple" class="' . $_field['class'] . '" style="' . $_field['style'] . '" ' . $_field['id'] . ' ' . $_readonly . $_placeholder . $_data . '>';

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
        $_out .= $_field['info'] ? '<small class="info">' . $_field['info'] . '</small>' : '';

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
     * @param  array  $field The config array
     * @param  string $tip   An optional tip (DEPRECATED: use $field['tip'] instead)
     * @return string        The form HTML
     */
    function form_field_boolean($field, $tip = '')
    {
        $_ci =& get_instance();

        // --------------------------------------------------------------------------

        //  Set var defaults
        $_field                 = array();
        $_field['id']           = isset($field['id'])             ? $field['id']          : null;
        $_field['oddeven']      = isset($field['oddeven'])        ? $field['oddeven']     : null;
        $_field['key']          = isset($field['key'])            ? $field['key']         : null;
        $_field['label']        = isset($field['label'])          ? $field['label']       : null;
        $_field['default']      = isset($field['default'])        ? $field['default']     : null;
        $_field['sub_label']    = isset($field['sub_label'])      ? $field['sub_label']   : null;
        $_field['required']     = isset($field['required'])       ? $field['required']    : false;
        $_field['placeholder']  = isset($field['placeholder'])    ? $field['placeholder'] : null;
        $_field['class']        = isset($field['class'])          ? $field['class']       : false;
        $_field['text_on']      = isset($field['text_on'])        ? $field['text_on']     : 'ON';
        $_field['text_off']     = isset($field['text_off'])       ? $field['text_off']    : 'OFF';
        $_field['data']         = isset($field['data'])           ? $field['data']        : array();
        $_field['readonly']     = isset($field['readonly'])       ? $field['readonly']    : false;
        $_field['info']         = isset($field['info'])           ? $field['info']        : false;
        $_field['tip']          = isset($field['tip'])            ? $field['tip']         : $tip;

        $_tip                   = array();
        $_tip['class']          = is_array($_field['tip']) && isset($_field['tip']['class']) ? $_field['tip']['class'] : 'fa fa-question-circle fa-lg tip';
        $_tip['rel']            = is_array($_field['tip']) && isset($_field['tip']['rel']) ? $_field['tip']['rel'] : 'tipsy-left';
        $_tip['title']          = is_array($_field['tip']) && isset($_field['tip']['title']) ? $_field['tip']['title'] : null;
        $_tip['title']          = is_string($_field['tip']) ? $_field['tip'] : $_field['title'];

        $_field_id_top  = $_field['id'] ? 'id="field-' . $_field['id'] . '"': '';
        $_error         = form_error($_field['key']) ? 'error' : '';
        $_readonly      = $_field['readonly'] ? 'disabled="disabled"' : '';
        $_readonly_cls  = $_field['readonly'] ? 'readonly' : '';
        $_class         = $_field['class'] ? 'class="' . $_field['class'] . '"' : '';

        // --------------------------------------------------------------------------

        $_out  = '<div class="field checkbox boolean ' . $_error . ' ' . $_field['oddeven'] . ' ' . $_readonly_cls . '" data-text-on="' . $_field['text_on'] . '" data-text-off="' . $_field['text_off'] . '" ' . $_field_id_top . '>';

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
        $_out .= '<span class="input ' . $_tipclass . '">';
        $_selected = set_value($_field['key'], (bool) $_field['default']);

        $_out .= '<div class="toggle toggle-modern"></div>';
        $_out .= form_checkbox($_field['key'], true, $_selected, $_field['id'] . $_data . ' ' . $_readonly . ' ' . $_class);

        //  Tip
        $_out .= $_tip['title'] ? '<b class="' . $_tip['class'] . '" rel="' . $_tip['rel'] . '" title="' . htmlentities($_tip['title'], ENT_QUOTES) . '"></b>' : '';

        //  Error
        $_out .= form_error($_field['key'], '<span class="alert alert-danger">', '</span>');

        //  Info block
        $_out .= $_field['info'] ? '<small class="info">' . $_field['info'] . '</small>' : '';

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
     * @param  array  $field   The config array
     * @param  array  $options The options to use for the radios (DEPRECATED: use $field['options'] instead)
     * @param  string $tip     An optional tip (DEPRECATED: use $field['tip'] instead)
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
     * @param  array  $field   The config array
     * @param  array  $options The options to use for the checkboxes (DEPRECATED: use $field['options'] instead)
     * @param  string $tip     An optional tip (DEPRECATED: use $field['tip'] instead)
     * @return string          The form HTML
     */
    function form_field_checkbox($field, $options = null, $tip = '')
    {
        $_ci =& get_instance();

        // --------------------------------------------------------------------------

        //  Set var defaults
        $_field                 = array();
        $_field['type']         = isset($field['type']) ? $field['type'] : 'checkbox';
        $_field['id']           = isset($field['id']) ? $field['id'] : null;
        $_field['oddeven']      = isset($field['oddeven']) ? $field['oddeven'] : null;
        $_field['key']          = isset($field['key']) ? $field['key'] : null;
        $_field['label']        = isset($field['label']) ? $field['label'] : null;
        $_field['default']      = isset($field['default']) ? $field['default'] : null;
        $_field['sub_label']    = isset($field['sub_label']) ? $field['sub_label'] : null;
        $_field['required']     = isset($field['required']) ? $field['required'] : false;
        $_field['placeholder']  = isset($field['placeholder']) ? $field['placeholder'] : null;
        $_field['class']        = isset($field['class']) ? $field['class'] : false;
        $_field['tip']          = isset($field['tip']) ? $field['tip'] : $tip;
        $_field['options']      = isset($field['options']) ? $field['options'] : $options;

        $_tip                   = array();
        $_tip['class']          = is_array($_field['tip']) && isset($_field['tip']['class']) ? $_field['tip']['class'] : 'fa fa-question-circle fa-lg tip';
        $_tip['rel']            = is_array($_field['tip']) && isset($_field['tip']['rel']) ? $_field['tip']['rel'] : 'tipsy-left';
        $_tip['title']          = is_array($_field['tip']) && isset($_field['tip']['title']) ? $_field['tip']['title'] : null;
        $_tip['title']          = is_string($_field['tip']) ? $_field['tip'] : $_field['title'];

        $_field_id_top = $_field['id'] ? 'id="field-' . $_field['id'] . '"': '';
        $_error        = form_error($_field['key']) ? 'error' : '';

        // --------------------------------------------------------------------------

        $_out  = '<div class="field ' . $_field['type'] . ' ' . $_error . ' ' . $_field['oddeven'] . '" ' . $_field_id_top . '>';

        //  First option
        $_out .= '<label>';

        //  Label
        $_out .= '<span class="label">';
        $_out .= $_field['label'];
        $_out .= $_field['required'] ? '*' : '';
        $_out .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';
        $_out .= '</span>';

        //  Does the field have an id?
        $_id = !empty($options[0]['id']) ? 'id="' . $options[0]['id'] . '-0" ' : '';

        //  Is the option disabled?
        $_disabled = !empty($options[0]['disabled']) ? 'disabled="disabled" ' : '';

        $_tipclass = $_tip['title'] ? 'with-tip' : '';
        $_disabledclass = $_disabled ? 'is-disabled' : '';

        $_out .= '<span class="input ' . $_tipclass . ' ' . $_disabledclass . '">';


        //  Field
        if (substr($_field['key'], -2) == '[]') :

            //  Field is an array, need to look for the value
            $_values        = $_ci->input->post(substr($_field['key'], 0, -2));
            $_data_selected = isset($options[0]['selected']) ? $options[0]['selected'] : false;
            $_selected      = $_ci->input->post() ? false : $_data_selected;

            if (is_array($_values) && array_search($options[0]['value'], $_values) !== false) :

                $_selected = true;

            endif;

        else :

            //  Normal field, continue as normal Mr Norman!
            if ($_ci->input->post($_field['key'])) :

                $_selected = $_ci->input->post($_field['key']) == $options[0]['value'] ? true : false;

            else :

                $_selected = isset($options[0]['selected']) ? $options[0]['selected'] : false;

            endif;

        endif;

        $_key   = isset($options[0]['key']) ? $options[0]['key'] : $_field['key'];

        if ($_field['type'] == 'checkbox') {

            $_out .= form_checkbox(
                $_key,
                $options[0]['value'],
                $_selected,
                $_id . $_disabled
            );
            $_out .= '<span class="text">' . $options[0]['label'] . '</span>';

        } elseif ($_field['type'] == 'radio') {

            $_out .= form_radio(
                $_key,
                $options[0]['value'],
                $_selected,
                $_id . $_disabled
            );
            $_out .= '<span class="text">' . $options[0]['label'] . '</span>';
        }

        //  Tip
        if (!empty($_tip['title'])) {

            $sTitle = htmlentities($_tip['title'], ENT_QUOTES);
            $_out .= '<b class="' . $_tip['class'] . '" rel="' . $_tip['rel'] . '" title="' . $sTitle . '"></b>';
        }

        $_out .= '</span>';
        $_out .= '</label>';

        //  Remaining options
        $numOptions = count($options);
        for ($i = 1; $i < $numOptions; $i++) :

            $_out .= '<label>';

            //  Label
            $_out .= '<span class="label">&nbsp;</span>';

            //  Does the field have an id?
            $_id = !empty($options[$i]['id']) ? 'id="' . $options[$i]['id'] . '-' . $i . '" ' : '';

            //  Is the option disabled?
            $_disabled = !empty($options[$i]['disabled']) ? 'disabled="disabled" ' : '';
            $_disabledclass = $_disabled ? 'is-disabled' : '';

            $_out .= '<span class="input ' . $_disabledclass . '">';

            //  Input
            if (substr($_field['key'], -2) == '[]') :

                //  Field is an array, need to look for the value
                $_values    = $_ci->input->post(substr($_field['key'], 0, -2));
                $_data_selected = isset($options[$i]['selected']) ? $options[$i]['selected'] : false;
                $_selected      = $_ci->input->post() ? false : $_data_selected;

                if (is_array($_values) && array_search($options[$i]['value'], $_values) !== false) :

                    $_selected = true;

                endif;

            else :

                //  Normal field, continue as normal Mr Norman!
                if ($_ci->input->post($_field['key'])) :

                    $_selected = $_ci->input->post($_field['key']) == $options[$i]['value'] ? true : false;

                else :

                    $_selected = isset($options[$i]['selected']) ? $options[$i]['selected'] : false;

                endif;

            endif;

            $_key = isset($options[$i]['key']) ? $options[$i]['key'] : $_field['key'];

            if ($_field['type'] == 'checkbox') {

                $_out .= form_checkbox(
                    $_key,
                    $options[$i]['value'],
                    $_selected,
                    $_id . $_disabled
                );
                $_out .= '<span class="text">' . $options[$i]['label'] . '</span>';

            } elseif ($_field['type'] == 'radio') {

                $_out .= form_radio(
                    $_key,
                    $options[$i]['value'],
                    $_selected,
                    $_id . $_disabled
                );
                $_out .= '<span class="text">' . $options[$i]['label'] . '</span>';
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

if (!function_exists('form_field_submit')) {

    /**
     * Generates a submit button which is aligned properly with the form_field_* functions
     * @param  string $button_value      The value to give the button
     * @param  string $button_name       The name to give the button
     * @param  string $button_attributes Any additional attributes to give the button
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

//  Include the CodeIgniter original
include 'vendor/rogeriopradoj/codeigniter/system/helpers/form_helper.php';
