<?php

/**
 * @package     Nails
 * @subpackage  common
 * @category    errors
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\CodeIgniter\Core;

use CI_Uri;
use Exception;
use Nails\Common\Service\ErrorHandler;
use Nails\Common\Service\HttpCodes;
use Nails\Environment;

class Uri extends CI_Uri
{
    /**
     * Filters the URI and prevents illegal characters
     *
     * @param string $str THE URI
     *
     * @throws Exception
     */
    public function filter_uri(&$str)
    {
        try {

            parent::filter_uri($str);

        } catch (Exception $e) {
            /**
             * If illegal characters are found, and in production halt with error. This
             * runs very early on so we can't use a 404 or similar. We don't want this
             * to bubble to error handlers as it'll just pollute the logs. On non-production
             * environments allow the exception to bubble so it's more obvious to the dev.
             *
             * This is obviously a fragile check :(
             */
            $sMessage = 'The URI you submitted has disallowed characters.';
            if (Environment::is(Environment::ENV_PROD) && $e->getMessage() === $sMessage) {
                ErrorHandler::halt($sMessage, '', HttpCodes::STATUS_NOT_ACCEPTABLE);
            } else {
                throw $e;
            }
        }
    }
}
