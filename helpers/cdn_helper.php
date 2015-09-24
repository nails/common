<?php

if (!function_exists('cdnServe')) {

    /**
     * Returns the URL for serving raw content from the CDN
     * @param  integer $iObjectId      The ID of the object to serve
     * @param  boolean $bForceDownload Whether or not the URL should stream to the browser, or forcibly download
     * @return string
     */
    function cdnServe($iObjectId, $bForceDownload = false)
    {
        get_instance()->load->library('cdn/cdn');
        return get_instance()->cdn->url_serve($iObjectId, $bForceDownload);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdn_serve')) {

    /**
     * Alias to cdnServe
     * @see cdnServe
     */
    function cdn_serve($iObjectId, $bForceDownload = false)
    {
        return cdnServe($iObjectId, $bForceDownload);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdnServeZipped')) {

    /**
     * Returns the URL for serving zipped objects
     * @param  array  $aObjectIds An array of object ID's to zip together
     * @param  string $sFilename  The filename to give the zip file
     * @return string
     */
    function cdnServeZipped($aObjectIds, $sFilename = 'download.zip')
    {
        get_instance()->load->library('cdn/cdn');
        return get_instance()->cdn->url_serve_zipped($aObjectIds, $sFilename);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdn_serve_zipped')) {

    /**
     * Alias to cdnServeZipped
     * @see cdnServeZipped
     */
    function cdn_serve_zipped($aObjectIds, $sFilename = 'download.zip')
    {
        return cdnServeZipped($aObjectIds, $sFilename);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdnCrop')) {

    /**
     * Returns the URL for a crop of an object
     * @param  integer $iObjectId The Object's ID
     * @param  integer $iWidth    The width of the thumbnail
     * @param  integer $iHeight   The height of the thumbnail
     * @return string
     */
    function cdnCrop($iObjectId, $iWidth, $iHeight)
    {
        get_instance()->load->library('cdn/cdn');
        return get_instance()->cdn->url_crop($iObjectId, $iWidth, $iHeight);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdn_crop')) {

    /**
     * Alias to cdnCrop
     * @see cdnCrop
     */
    function cdn_crop($iObjectId, $iWidth, $iHeight)
    {
        return cdnCrop($iObjectId, $iWidth, $iHeight);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdn_thumb')) {

    /**
     * Alias to cdnCrop
     * @see cdnCrop
     */
    function cdn_thumb($iObjectId, $iWidth, $iHeight)
    {
        return cdnCrop($iObjectId, $iWidth, $iHeight);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdnScale')) {

    /**
     * Returns the URL for a scaled thumbnail of an object
     * @param  integer $iObjectId The Object's ID
     * @param  integer $iWidth    The width of the thumbnail
     * @param  integer $iHeight   The height of the thumbnail
     * @return string
     */
    function cdnScale($iObjectId, $iWidth, $iHeight)
    {
        get_instance()->load->library('cdn/cdn');
        return get_instance()->cdn->url_scale($iObjectId, $iWidth, $iHeight);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdn_scale')) {

    /**
     * Alias to cdnScale
     * @see cdnScale
     */
    function cdn_scale($iObjectId, $iWidth, $iHeight)
    {
        return cdnScale($iObjectId, $iWidth, $iHeight);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdnPlaceholder')) {

    /**
     * Returns the URL for a placeholder graphic
     * @param  integer $iWidth  The width of the placeholder
     * @param  integer $iHeight The height of the placeholder
     * @param  integer $iBorder The width of the border, if any
     * @return string
     */
    function cdnPlaceholder($iWidth, $iHeight, $iBorder = 0)
    {
        get_instance()->load->library('cdn/cdn');
        return get_instance()->cdn->url_placeholder($iWidth, $iHeight, $iBorder);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdn_placeholder')) {

    /**
     * Alias to cdnPlaceholder
     * @see cdnPlaceholder
     */
    function cdn_placeholder($iWidth, $iHeight, $iBorder = 0)
    {
        return cdnPlaceholder($iWidth, $iHeight, $iBorder);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdnBlankAvatar')) {

    /**
     * Returns the URL for a blank avatar graphic
     * @param  integer        $iWidth  The width of the placeholder
     * @param  integer        $iHeight The height of the placeholder
     * @param  string|integer $mSex    The gender of the avatar
     * @return string
     */
    function cdnBlankAvatar($iWidth, $iHeight, $mSex = '')
    {
        get_instance()->load->library('cdn/cdn');
        return get_instance()->cdn->url_blank_avatar($iWidth, $iHeight, $mSex);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdn_blank_avatar')) {

    /**
     * Alias to cdnBlankAvatar
     * @see cdnBlankAvatar
     */
    function cdn_blank_avatar($iWidth, $iHeight, $mSex = '')
    {
        return cdnBlankAvatar($iWidth, $iHeight, $mSex);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdnAvatar')) {

    /**
     * Returns the URL for a user's avatar
     * @param  integer $iUserId The user ID to use
     * @param  integer $iWidth  The width of the avatar
     * @param  integer $iHeight The height of the avatar
     * @return string
     */
    function cdnAvatar($iUserId = null, $iWidth = 100, $iHeight = 100)
    {
        get_instance()->load->library('cdn/cdn');
        return get_instance()->cdn->url_avatar($iUserId, $iWidth, $iHeight);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdn_avatar')) {

    /**
     * Alias to cdnAvatar
     * @see cdnAvatar
     */
    function cdn_avatar($iWidth = 100, $iHeight = 100, $iBorder = 0)
    {
        return cdnAvatar($iWidth, $iHeight, $iBorder);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdnExpiringUrl')) {

    /**
     * Returns an expiring URL
     * @param  integer $iObject        The ID of the object to server
     * @param  integer $expires        The length of time the URL should be valid for, in seconds
     * @param  boolean $bForceDownload Whether or not the URL should stream to the browser, or forcibly download
     * @return string
     */
    function cdnExpiringUrl($iObject, $iExpires, $bForceDownload = false)
    {
        get_instance()->load->library('cdn/cdn');
        return get_instance()->cdn->url_expiring($iObject, $iExpires, $bForceDownload);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdn_expiring_url')) {

    /**
     * Alias to cdnExpiringUrl
     * @see cdnExpiringUrl
     */
    function cdn_expiring_url($iObject, $iExpires, $bForceDownload = false)
    {
        return cdnExpiringUrl($iObject, $iExpires, $bForceDownload);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('getExtFromMime')) {

    /**
     * Get the extension of a file from it's mime
     * @param  string $sMime The mime to look up
     * @return string
     */
    function getExtFromMime($sMime)
    {
        get_instance()->load->library('cdn/cdn');
        return get_instance()->cdn->get_ext_from_mime($sMime);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('get_ext_from_mime')) {

    /**
     * Alias to getExtFromMime
     * @see getExtFromMime
     */
    function get_ext_from_mime($sMime)
    {
        return getExtFromMime($sMime);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('getMimeFromExt')) {

    /**
     * Get the mime of a file from it's extension
     * @param  string $sExt The extension to look up
     * @return string
     */
    function getMimeFromExt($sExt)
    {
        get_instance()->load->library('cdn/cdn');
        return get_instance()->cdn->get_mime_from_ext($sExt);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('get_mime_from_ext')) {

    /**
     * Alias to getMimeFromExt
     * @see getMimeFromExt
     */
    function get_mime_from_ext($sExt)
    {
        return getMimeFromExt($sExt);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('getMimeFromFile')) {

    /**
     * Get the mime from a file on disk
     * @param  string $sFile The file to look up
     * @return string
     */
    function getMimeFromFile($sFile)
    {
        get_instance()->load->library('cdn/cdn');
        return get_instance()->cdn->get_mime_from_file($sFile);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('get_mime_from_file')) {

    /**
     * Alias to getMimeFromFile
     * @see getMimeFromFile
     */
    function get_mime_from_file($sExt)
    {
        return getMimeFromFile($sExt);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('cdnManageUrl')) {

    /**
     * Generate a valid URL for the CDN Manager
     * @param  string  $sBucket   The bucket the manager should use
     * @param  array   $aCallback The callback the manager should use for "insert" buttons
     * @param  mixed   $mPassback Any data to pass back to the callback
     * @param  boolean $bSecure   Whether or not the link should be secure
     * @return string
     */
    function cdnManageUrl($sBucket, $aCallback = array(), $mPassback = null, $bSecure = false)
    {
        $aParams = array();

        /**
         * The callback should be a two element array, the first being the
         * instance variable, the second being the method name.
         */
        $aParams['callback'] = $aCallback;

        /**
         * Passback is any data that the caller wishes to be sent back to the callback
         */

        $aParams['passback'] = json_encode($mPassback);

        /**
         * The bucket should be hashed up and paired with an irreversible hash for
         * verification. Why? So that it's not trivial to mess about with buckets
         * willy nilly.
         */

        $iNonce = time();

        $aParams['bucket'] = get_instance()->encrypt->encode($sBucket . '|' . $iNonce, APP_PRIVATE_KEY);
        $aParams['hash']   = md5($sBucket . '|' . $iNonce . '|' . APP_PRIVATE_KEY);

        //  Prep the query string
        $aParams = http_build_query($aParams);

        return site_url('cdn/manager/browse?' . $aParams, $bSecure);
    }
}
