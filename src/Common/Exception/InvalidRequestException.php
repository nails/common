<?php

/**
 * Base exception for requests that are rejected early (before any controller,
 * model, or query runs) for being malformed in some way, e.g. invalid
 * encoding or disallowed URI characters.
 *
 * Nails\Common\Service\ErrorHandler::handleException() special-cases this
 * family: rather than going through the normal driver-reported fatal-error
 * screen, it halts immediately using the exception's own message and code
 * (as the HTTP status), so these never surface as a 500.
 *
 * @package     Nails
 * @subpackage  common
 * @category    Exceptions
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Exception;

class InvalidRequestException extends NailsException
{
}
