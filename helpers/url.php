<?php

/**
 * This file provides url related helper functions
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

if (!function_exists('redirect')) {

    /**
     * Header Redirect
     *
     * Header redirect in two flavors
     * For very fine grained control over headers, you could use the Output
     * Library's set_header() function.
     *
     * Overriding so as to call the post_system hook before exit()'ing
     * @param  string  $uri                The uri to redirect to
     * @param  string  $method             The redirect method
     * @param  integer $http_response_code The response code to send
     * @return void
     */
    function redirect($uri = '', $method = 'location', $http_response_code = 302)
    {
        /**
         * Call the post_system hook, the system will be killed in approximately 13
         * lines so this is the last chance to cleanup.
         */

        $hook =& load_class('Hooks', 'core');
        $hook->call_hook('post_system');

        // --------------------------------------------------------------------------

        if (!preg_match('#^https?://#i', $uri)) {

            $uri = site_url($uri);
        }

        switch ($method) {

            case 'refresh':

                header("Refresh:0;url=".$uri);
                break;

            default:

                header('Location: ' . $uri, true, $http_response_code);
                break;
        }

        exit;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('tel')) {

    /**
     * Generates a hyperlink using the tel: scheme
     * @param  string $uri        The phone number to link
     * @param  string $title      The title to give the hyperlink
     * @param  string $attributes Any attributes to give the hyperlink
     * @return string
     */
    function tel($uri = '', $title = '', $attributes = '')
    {
        $title = empty($title) ? $uri : $title;

        $uri = preg_replace('/[^\+0-9]/', '', $uri);
        $uri   = 'tel://' . $uri;

        return anchor($uri, $title, $attributes);
    }
}

// --------------------------------------------------------------------------

//  Include the CodeIgniter original
include FCPATH . 'vendor/codeigniter/framework/system/helpers/url_helper.php';
