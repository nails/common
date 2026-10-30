<?php

/**
 * @package     Nails
 * @subpackage  common
 * @category    errors
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\CodeIgniter\Core;

use CI_URI;
use Exception;
use Nails\Common\Exception\Uri\InvalidCharactersException;
use Nails\Common\Service\HttpCodes;
use Nails\Environment;

class Uri extends CI_URI
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
             * If illegal characters are found, and in production, reject early: throw
             * our own InvalidCharactersException, which ErrorHandler::handleException()
             * special-cases and halts with directly, rather than reporting it as a
             * generic fatal error. On non-production environments allow the original
             * exception to bubble so it's more obvious to the dev.
             *
             * This is obviously a fragile check :(
             */
            $sMessage = 'The URI you submitted has disallowed characters.';
            if (Environment::is(Environment::ENV_PROD) && $e->getMessage() === $sMessage) {
                throw new InvalidCharactersException($sMessage, HttpCodes::STATUS_NOT_FOUND, $e);
            } else {
                throw $e;
            }
        }
    }
}
