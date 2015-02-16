<?php

if (!function_exists('cdn_serve'))
{
    /**
     * Returns the URL for serving raw content from the CDN
     * @param  string  $object        The ID of the object to serve
     * @param  boolean $forceDownload Whether or not the URL should stream to the browser, or forcibly download
     * @return string
     */
    function cdn_serve($object, $forceDownload = false)
    {
        get_instance()->load->library('cdn/cdn');

        return get_instance()->cdn->url_serve($object, $forceDownload);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdn_serve_zipped'))
{
    /**
     * Returns the URL for serving zipped objects
     * @param  array  $objects  An array of object ID's to zip together
     * @param  string $filename the filename to give the zip file
     * @return string
     */
    function cdn_serve_zipped($objects, $filename = 'download.zip')
    {
        get_instance()->load->library('cdn/cdn');

        return get_instance()->cdn->url_serve_zipped($objects, $filename);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdn_thumb'))
{
    /**
     * Returns the URL for a thumbnail of an object
     * @param  integer $object The Object's ID
     * @param  integer $width  The width of the thumbnail
     * @param  integer $height The height of the thumbnail
     * @return string
     */
    function cdn_thumb($object, $width, $height)
    {
        get_instance()->load->library('cdn/cdn');

        return get_instance()->cdn->url_thumb($object, $width, $height);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdn_scale'))
{
    /**
     * Returns the URL for a scaled thumbnail of an object
     * @param  integer $object The Object's ID
     * @param  integer $width  The width of the thumbnail
     * @param  integer $height The height of the thumbnail
     * @return string
     */
    function cdn_scale($object, $width, $height)
    {
        get_instance()->load->library('cdn/cdn');

        return get_instance()->cdn->url_scale($object, $width, $height);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdn_placeholder'))
{
    /**
     * Returns the URL for a placeholder graphic
     * @param  integer $width  The width of the placeholder
     * @param  integer $height The height of the placeholder
     * @param  integer $border The width of the border, if any
     * @return string
     */
    function cdn_placeholder($width = 100, $height = 100, $border = 0)
    {
        get_instance()->load->library('cdn/cdn');

        return get_instance()->cdn->url_placeholder($width, $height, $border);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdn_blank_avatar'))
{
    /**
     * Returns the URL for a blank avatar graphic
     * @param  integer $width  The width of the placeholder
     * @param  integer $height The height of the placeholder
     * @param  integer $sex    The gender of the avatar
     * @return string
     */
    function cdn_blank_avatar($width = 100, $height = 100, $sex = '')
    {
        get_instance()->load->library('cdn/cdn');

        return get_instance()->cdn->url_blank_avatar($width, $height, $sex);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdn_avatar'))
{
    /**
     * Returns the URL for a user's avatar
     * @param  integer $userId The user ID to use
     * @param  integer $width  The width of the avatar
     * @param  integer $height The height of the avatar
     * @return string
     */
    function cdn_avatar($userId = null, $width = 100, $height = 100)
    {
        get_instance()->load->library('cdn/cdn');

        return get_instance()->cdn->url_avatar($userId, $width, $height);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdn_expiring_url'))
{
    /**
     * Returns an expiring url
     * @param  string  $bucket        The bucket which the image resides in
     * @param  string  $expires       The length of time the URL should be valid for, in seconds
     * @param  boolean $forceDownload Whether or not the URL should stream to the browser, or forcibly download
     * @return string
     */
    function cdn_expiring_url($object, $expires, $forceDownload = false)
    {
        get_instance()->load->library('cdn/cdn');

        return get_instance()->cdn->url_expiring($object, $expires, $forceDownload);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('get_ext_from_mime'))
{
    /**
     * Get the extension of a file from it's mime
     * @param  string $mime The mime to look up
     * @return string
     */
    function get_ext_from_mime($mime)
    {
        get_instance()->load->library('cdn/cdn');

        return get_instance()->cdn->get_ext_from_mime($mime);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('get_mime_from_ext'))
{
    /**
     * Get the mime of a file from it's extension
     * @param  string $ext The extension to look up
     * @return string
     */
    function get_mime_from_ext($ext)
    {
        get_instance()->load->library('cdn/cdn');

        return get_instance()->cdn->get_mime_from_ext($ext);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('get_mime_from_file'))
{
    /**
     * Get the mime from a file on disk
     * @param  string $file The file to look up
     * @return string
     */
    function get_mime_from_file($file)
    {
        get_instance()->load->library('cdn/cdn');

        return get_instance()->cdn->get_mime_from_file($file);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdnManageUrl'))
{
    /**
     * Generate a valid URL for the CDN manager
     * @param  string  $bucket   The bucket the manager should use
     * @param  array   $callback The callback the manager should use for the "insert" buttons.
     * @param  boolean $secure   Whether or not the link should be secure or not
     * @return string
     */
    function cdnManageUrl($bucket, $callback = array(), $passback = null, $secure = false)
    {
        $params = array();

        /**
         * The callback should be a two element array, the first being the
         * instance variable, the second being the method name.
         */
        $params['callback'] = $callback;

        /**
         * Passback is any data that the caller wishes to be sent back to the callback
         */

        $params['passback'] = json_encode($passback);

        /**
         * The bucket should be hashed up and paired with an irreversible hash for
         * verification. Why? So that it's not trivial to mess about with buckets
         * willy nilly.
         */

        $nonce = time();

        $params['bucket'] = get_instance()->encrypt->encode($bucket . '|' . $nonce, APP_PRIVATE_KEY);
        $params['hash']   = md5($bucket . '|' . $nonce . '|' . APP_PRIVATE_KEY);

        //  Prep the query string
        $params = http_build_query($params);

        return site_url('cdn/manager/browse?' . $params, $secure);
    }
}
