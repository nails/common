<?php

/**
 * This class provides a list of HTTP status codes and their values
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 */

namespace Nails\Common\Service;

class HttpCodes {

    //  1xx Informational
    const STATUS_100 = 'Continue';
    const STATUS_101 = 'Switching Protocols';

    //  2xx Success
    const STATUS_200 = 'OK';
    const STATUS_201 = 'Created';
    const STATUS_202 = 'Accepted';
    const STATUS_203 = 'Non-Authoritative Information';
    const STATUS_204 = 'No Content';
    const STATUS_205 = 'Reset Content';
    const STATUS_206 = 'Partial Content';
    const STATUS_207 = 'Multi-status';
    const STATUS_208 = 'Already imported';
    const STATUS_226 = 'IM used';

    //  3xx Redirection
    const STATUS_300 = 'Multiple Choices';
    const STATUS_301 = 'Moved Permanently';
    const STATUS_302 = 'Found';
    const STATUS_303 = 'See Other';
    const STATUS_304 = 'Not Modified';
    const STATUS_305 = 'Use Proxy';
    const STATUS_306 = 'Switch Proxy';
    const STATUS_307 = 'Temporary redirect';
    const STATUS_308 = 'Permanent redirect';

    //  4xxx Client Error
    const STATUS_400 = 'Bad Request';
    const STATUS_401 = 'Unauthorized';
    const STATUS_402 = 'Payment Required';
    const STATUS_403 = 'Forbidden';
    const STATUS_404 = 'Not Found';
    const STATUS_405 = 'Method Not Allowed';
    const STATUS_406 = 'Not Acceptable';
    const STATUS_407 = 'Proxy Authentication Required';
    const STATUS_408 = 'Request Timeout';
    const STATUS_409 = 'Conflict';
    const STATUS_410 = 'Gone';
    const STATUS_411 = 'Length Required';
    const STATUS_412 = 'Precondition Failed';
    const STATUS_413 = 'Request Entity Too Large';
    const STATUS_414 = 'Request-URI Too Long';
    const STATUS_415 = 'Unsupported Media Type';
    const STATUS_416 = 'Requested Range Not Satisfiable';
    const STATUS_417 = 'Expectation Failed';
    const STATUS_421 = 'Misdirected request';
    const STATUS_422 = 'Unprocessable entity';
    const STATUS_423 = 'Locked';
    const STATUS_424 = 'Failed dependency';
    const STATUS_426 = 'Upgrade required';
    const STATUS_428 = 'Precondition required';
    const STATUS_429 = 'Too many requests';
    const STATUS_431 = 'Request header fields too large';

    //  5xx Server Error
    const STATUS_500 = 'Internal Server Error';
    const STATUS_501 = 'Not Implemented';
    const STATUS_502 = 'Bad Gateway';
    const STATUS_503 = 'Service Unavailable';
    const STATUS_504 = 'Gateway Timeout';
    const STATUS_505 = 'HTTP Version Not Supported';
    const STATUS_506 = 'Variant also negotiates';
    const STATUS_507 = 'Insufficient storage';
    const STATUS_508 = 'Loop detected';
    const STATUS_510 = 'Not extended';
    const STATUS_511 = 'Network authentication required';

    // --------------------------------------------------------------------------

    /**
     * Returns the human readable portion of an HTTP status code
     * @param $iCode integer The numerical HTTP status code
     * @return null
     */
    public function getByCode($iCode)
    {
        $sConstant = 'static::STATUS_' . $iCode;
        if (defined($sConstant )) {
            return constant($sConstant);
        }

        return null;
    }
}
