<?php

use Nails\Common\Helper\Url;
use Nails\Common\Service\FileCache;
use Nails\Config;
use Nails\Factory;

/* Load the base config file from the CodeIgniter package */
require Config::get('NAILS_CI_APP_PATH') . 'config/config.php';

/**
 * Override some of the CI defaults
 */

//  Base Site URL
$config['base_url'] = Config::get('BASE_URL');

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
/** @var FileCache $oFileCache */
$oFileCache           = Factory::service('FileCache');
$config['cache_path'] = $oFileCache->getDir();

//  The encryption key
$config['encryption_key'] = md5(Config::get('APP_PRIVATE_KEY'));

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
$config['cookie_domain']   = '';

if (Config::get('CONF_COOKIE_DOMAIN')) {

    //  The developer has specified the specific domain to use for cookies
    $config['cookie_domain'] = Config::get('CONF_COOKIE_DOMAIN');

} else {

    /**
     * No specific domain has been specified, set a cookie which spans the
     * use of all specified BASE_URLs, i.e BASE_URL and SECURE_BASE_URL
     */

    $sBaseDomain       = Url::extractRegistrableDomain(Config::get('BASE_URL'));
    $sSecureBaseDomain = Url::extractRegistrableDomain(Config::get('SECURE_BASE_URL'));

    if ($sBaseDomain === $sSecureBaseDomain) {

        $config['cookie_domain'] = $sBaseDomain;

    } else {

        $_ERROR = 'The <code>BASE_URL</code> and <code>SECURE_BASE_URL</code> ';
        $_ERROR .= 'values do not share the same domain, this can cause issues ';
        $_ERROR .= 'with sessions.';

        include Config::get('NAILS_COMMON_PATH') . 'errors/startup_error.php';
    }
}

//  CSRF
$config['csrf_token_name']  = 'nailscsrftest';
$config['csrf_cookie_name'] = 'nailscsrftoken';
