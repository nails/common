<?php

/* Load the base config file from the CodeIgniter package */
require NAILS_CI_APP_PATH . 'config/config.php';

/**
 * Override some of the CI defaults
 */

//  Base Site URL
$config['base_url'] = BASE_URL;

//  Remove index.php from URL's
$config['index_page'] = '';

//  Enable Hooks
$config['enable_hooks'] = true;

//  Set the sub class prefix
$config['subclass_prefix'] = 'NAILS_';

//  Allowed URI characters
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-+@';

//  Increase logging threshold
$config['log_threshold'] = 1;

//  Cache directory
$config['cache_path'] = defined('CACHE_PATH') ? CACHE_PATH : NAILS_APP_PATH . 'cache/private/';

//  The encryption key
$config['encryption_key'] = md5(APP_PRIVATE_KEY);

//  Session variables
$config['sess_driver']             = 'database';
$config['sess_cookie_name']        = 'nailssession';
$config['sess_expiration']         = 0;
$config['sess_table_name']         = $config['sess_save_path'] = 'nails_session';
$config['sess_match_ip']           = false;
$config['sess_time_to_update']     = 300;
$config['sess_regenerate_destroy'] = false;

//  Cookie related variables
$config['cookie_httponly'] = true;

if (defined('CONF_COOKIE_DOMAIN')) {

    //  The developer has specified the specific domain to use for cookies
    $config['cookie_domain'] = CONF_COOKIE_DOMAIN;
} else {

    /**
     * No specific domain has been specified, set a cookie which spans the
     * use of all specified BASE_URLs, i.e BASE_URL and SECURE_BASE_URL
     */

    $config['cookie_domain'] = '';

    /**
     * Are the BASE_URL and SECURE_BASE_URL on the same domain? if so, cool,
     * if not then...
     */

    $baseDomain       = \Nails\Functions::getDomainFromUrl($config['base_url']);
    $secureBaseDomain = \Nails\Functions::getDomainFromUrl(SECURE_BASE_URL);

    if ($baseDomain == $secureBaseDomain) {

        //  If the two match, then define it
        $config['cookie_domain'] = $baseDomain;

    } else {

        $_ERROR = 'The <code>BASE_URL</code> and <code>SECURE_BASE_URL</code> ';
        $_ERROR .= 'constants do not share the same domain, this can cause issues ';
        $_ERROR .= 'with sessions.';

        include NAILS_COMMON_PATH . 'errors/startup_error.php';
    }
}

//  CSRF
$config['csrf_token_name']  = 'nailscsrftest';   //  This is hardcoded into nails.api.js
$config['csrf_cookie_name'] = 'nailscsrftoken'; //  This is hardcoded into nails.api.js
