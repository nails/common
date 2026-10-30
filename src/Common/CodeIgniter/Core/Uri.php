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
             * If illegal characters are found, reject early: throw our own
             * InvalidCharactersException, which ErrorHandler::handleException()
             * special-cases and halts with directly, rather than reporting it
             * as a generic fatal error. This is the same in every environment;
             * there's nothing dev-specific to see here that getMessage() below
             * doesn't already show.
             *
             * This is obviously a fragile check :(
             */
            $sMessage = 'The URI you submitted has disallowed characters.';
            if ($e->getMessage() === $sMessage) {
                throw new InvalidCharactersException($sMessage, HttpCodes::STATUS_NOT_FOUND, $e);
            } else {
                throw $e;
            }
        }
    }
}
