<?php

/**
 * Provides additional Config functionality as well as bringing support for Nails.
 *
 * @package     Nails
 * @subpackage  common
 * @category    core
 * @author      Nails Dev Team
 * @link
 */

/* load the MX Config class */
require NAILS_COMMON_PATH . 'MX/Config.php';

class CORE_NAILS_Config extends MX_Config
{
    /**
     * Returns the site's URL, secured if necessary
     *
     * @param string $sUrl
     * @param bool   $bForceSecure
     * @return mixed|string
     */
    public function site_url($sUrl = '', $bForceSecure = false)
    {
        $sUrl = parent::site_url($sUrl);

        //  If the URL begins with a slash then attempt to guess the host using $_SERVER
        if (preg_match('/^\//', $sUrl) && !empty($_SERVER['HTTP_HOST'])) {
            $sProtocol = !empty($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
            $sUrl = $sProtocol . '://' . $_SERVER['HTTP_HOST'] . $sUrl;
        }

        if ($bForceSecure || isPageSecure()) {
            $sUrl = preg_replace('#^' . BASE_URL . '#', SECURE_BASE_URL, $sUrl);
        }

        return $sUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * Secure Base URL
     * Returns secure_base_url [. uri_string]
     *
     * @access public
     * @param string $uri
     * @return string
     */
    function secure_base_url($uri = '')
    {
        return SECURE_BASE_URL . ltrim($this->_uri_string($uri), '/');
    }
}
