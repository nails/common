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

class HttpCodes
{
    //  1xx Informational
    const STATUS_100                 = 'Continue';
    const STATUS_CONTINUE            = 100;
    const STATUS_101                 = 'Switching Protocols';
    const STATUS_SWITCHING_PROTOCOLS = 101;

    //  2xx Success
    const STATUS_200                           = 'OK';
    const STATUS_OK                            = 200;
    const STATUS_201                           = 'Created';
    const STATUS_CREATED                       = 201;
    const STATUS_202                           = 'Accepted';
    const STATUS_ACCEPTED                      = 202;
    const STATUS_203                           = 'Non-Authoritative Information';
    const STATUS_NON_AUTHORITATIVE_INFORMATION = 203;
    const STATUS_204                           = 'No Content';
    const STATUS_NO_CONTENT                    = 204;
    const STATUS_205                           = 'Reset Content';
    const STATUS_RESET_CONTENT                 = 205;
    const STATUS_206                           = 'Partial Content';
    const STATUS_PARTIAL_CONTENT               = 206;
    const STATUS_207                           = 'Multi-status';
    const STATUS_MULTI_STATUS                  = 207;
    const STATUS_208                           = 'Already imported';
    const STATUS_ALREADY_IMPORTED              = 208;
    const STATUS_226                           = 'IM used';
    const STATUS_IM_USED                       = 226;

    //  3xx Redirection
    const STATUS_300                = 'Multiple Choices';
    const STATUS_MULTIPLE_CHOICES   = 300;
    const STATUS_301                = 'Moved Permanently';
    const STATUS_MOVED_PERMANENTLY  = 301;
    const STATUS_302                = 'Found';
    const STATUS_FOUND              = 302;
    const STATUS_303                = 'See Other';
    const STATUS_SEE_OTHER          = 303;
    const STATUS_304                = 'Not Modified';
    const STATUS_NOT_MODIFIED       = 304;
    const STATUS_305                = 'Use Proxy';
    const STATUS_USE_PROXY          = 305;
    const STATUS_306                = 'Switch Proxy';
    const STATUS_SWITCH_PROXY       = 306;
    const STATUS_307                = 'Temporary redirect';
    const STATUS_TEMPORARY_REDIRECT = 307;
    const STATUS_308                = 'Permanent redirect';
    const STATUS_PERMANENT_REDIRECT = 308;

    //  4xxx Client Error
    const STATUS_400                             = 'Bad Request';
    const STATUS_BAD_REQUEST                     = 400;
    const STATUS_401                             = 'Unauthorized';
    const STATUS_UNAUTHORIZED                    = 401;
    const STATUS_402                             = 'Payment Required';
    const STATUS_PAYMENT_REQUIRED                = 402;
    const STATUS_403                             = 'Forbidden';
    const STATUS_FORBIDDEN                       = 403;
    const STATUS_404                             = 'Not Found';
    const STATUS_NOT_FOUND                       = 404;
    const STATUS_405                             = 'Method Not Allowed';
    const STATUS_METHOD_NOT_ALLOWED              = 405;
    const STATUS_406                             = 'Not Acceptable';
    const STATUS_NOT_ACCEPTABLE                  = 406;
    const STATUS_407                             = 'Proxy Authentication Required';
    const STATUS_PROXY_AUTHENTICATION_REQUIRED   = 407;
    const STATUS_408                             = 'Request Timeout';
    const STATUS_REQUEST_TIMEOUT                 = 408;
    const STATUS_409                             = 'Conflict';
    const STATUS_CONFLICT                        = 409;
    const STATUS_410                             = 'Gone';
    const STATUS_GONE                            = 410;
    const STATUS_411                             = 'Length Required';
    const STATUS_LENGTH_REQUIRED                 = 411;
    const STATUS_412                             = 'Precondition Failed';
    const STATUS_PRECONDITION_FAILED             = 412;
    const STATUS_413                             = 'Request Entity Too Large';
    const STATUS_REQUEST_ENTITY_TOO_LARGE        = 413;
    const STATUS_414                             = 'Request-URI Too Long';
    const STATUS_REQUES_URI_TOO_LONG             = 414;
    const STATUS_415                             = 'Unsupported Media Type';
    const STATUS_UNSUPPORTED_MEDIA_TYPE          = 415;
    const STATUS_416                             = 'Requested Range Not Satisfiable';
    const STATUS_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const STATUS_417                             = 'Expectation Failed';
    const STATUS_EXPECTATION_FAILED              = 417;
    const STATUS_421                             = 'Misdirected request';
    const STATUS_MISDIRECTED_REQUEST             = 421;
    const STATUS_422                             = 'Unprocessable entity';
    const STATUS_UNPROCESSABLE_ENTITY            = 422;
    const STATUS_423                             = 'Locked';
    const STATUS_LOCKED                          = 423;
    const STATUS_424                             = 'Failed dependency';
    const STATUS_FAILED_DEPENDENCY               = 424;
    const STATUS_426                             = 'Upgrade required';
    const STATUS_UPGRADE_REQUIRED                = 426;
    const STATUS_428                             = 'Precondition required';
    const STATUS_PRECONDITION_REQUIRED           = 428;
    const STATUS_429                             = 'Too many requests';
    const STATUS_TOO_MANY_REQUESTS               = 429;
    const STATUS_431                             = 'Request header fields too large';
    const STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;

    //  5xx Server Error
    const STATUS_500                             = 'Internal Server Error';
    const STATUS_INTERNAL_SERVER_ERROR           = 500;
    const STATUS_501                             = 'Not Implemented';
    const STATUS_NOT_IMPLEMENTED                 = 501;
    const STATUS_502                             = 'Bad Gateway';
    const STATUS_BAD_GATEWAY                     = 502;
    const STATUS_503                             = 'Service Unavailable';
    const STATUS_SERVICE_UNAVAILABLE             = 503;
    const STATUS_504                             = 'Gateway Timeout';
    const STATUS_GATEWAY_TIMEOUT                 = 504;
    const STATUS_505                             = 'HTTP Version Not Supported';
    const STATUS_HTTP_VERSION_NOT_SUPPORTED      = 505;
    const STATUS_506                             = 'Variant also negotiates';
    const STATUS_VARIANT_ALSO_NEGOTIATES         = 506;
    const STATUS_507                             = 'Insufficient storage';
    const STATUS_INSUFFICIENT_STORAGE            = 507;
    const STATUS_508                             = 'Loop detected';
    const STATUS_LOOP_DETECTED                   = 508;
    const STATUS_510                             = 'Not extended';
    const STATUS_NOT_EXTENDED                    = 510;
    const STATUS_511                             = 'Network authentication required';
    const STATUS_NETWORK_AUTHENTICATION_REQUIRED = 511;

    // --------------------------------------------------------------------------

    /**
     * Returns all the HTTP Codes and their human-friendly label
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            //  1xx Informational
            self::STATUS_CONTINUE            => self::STATUS_100,
            self::STATUS_SWITCHING_PROTOCOLS => self::STATUS_101,

            //  2xx Success
            self::STATUS_OK                            => self::STATUS_200,
            self::STATUS_CREATED                       => self::STATUS_201,
            self::STATUS_ACCEPTED                      => self::STATUS_202,
            self::STATUS_NON_AUTHORITATIVE_INFORMATION => self::STATUS_203,
            self::STATUS_NO_CONTENT                    => self::STATUS_204,
            self::STATUS_RESET_CONTENT                 => self::STATUS_205,
            self::STATUS_PARTIAL_CONTENT               => self::STATUS_206,
            self::STATUS_MULTI_STATUS                  => self::STATUS_207,
            self::STATUS_ALREADY_IMPORTED              => self::STATUS_208,
            self::STATUS_IM_USED                       => self::STATUS_226,

            //  3xx Redirection
            self::STATUS_MULTIPLE_CHOICES   =>  self::STATUS_300,
            self::STATUS_MOVED_PERMANENTLY  =>  self::STATUS_301,
            self::STATUS_FOUND              =>  self::STATUS_302,
            self::STATUS_SEE_OTHER          =>  self::STATUS_303,
            self::STATUS_NOT_MODIFIED       =>  self::STATUS_304,
            self::STATUS_USE_PROXY          =>  self::STATUS_305,
            self::STATUS_SWITCH_PROXY       =>  self::STATUS_306,
            self::STATUS_TEMPORARY_REDIRECT =>  self::STATUS_307,
            self::STATUS_PERMANENT_REDIRECT =>  self::STATUS_308,

            //  4xxx Client Error
            self::STATUS_BAD_REQUEST                     =>  self::STATUS_400,
            self::STATUS_UNAUTHORIZED                    =>  self::STATUS_401,
            self::STATUS_PAYMENT_REQUIRED                =>  self::STATUS_402,
            self::STATUS_FORBIDDEN                       =>  self::STATUS_403,
            self::STATUS_NOT_FOUND                       =>  self::STATUS_404,
            self::STATUS_METHOD_NOT_ALLOWED              =>  self::STATUS_405,
            self::STATUS_NOT_ACCEPTABLE                  =>  self::STATUS_406,
            self::STATUS_PROXY_AUTHENTICATION_REQUIRED   =>  self::STATUS_407,
            self::STATUS_REQUEST_TIMEOUT                 =>  self::STATUS_408,
            self::STATUS_CONFLICT                        =>  self::STATUS_409,
            self::STATUS_GONE                            =>  self::STATUS_410,
            self::STATUS_LENGTH_REQUIRED                 =>  self::STATUS_411,
            self::STATUS_PRECONDITION_FAILED             =>  self::STATUS_412,
            self::STATUS_REQUEST_ENTITY_TOO_LARGE        =>  self::STATUS_413,
            self::STATUS_REQUES_URI_TOO_LONG             =>  self::STATUS_414,
            self::STATUS_UNSUPPORTED_MEDIA_TYPE          =>  self::STATUS_415,
            self::STATUS_REQUESTED_RANGE_NOT_SATISFIABLE =>  self::STATUS_416,
            self::STATUS_EXPECTATION_FAILED              =>  self::STATUS_417,
            self::STATUS_MISDIRECTED_REQUEST             =>  self::STATUS_421,
            self::STATUS_UNPROCESSABLE_ENTITY            =>  self::STATUS_422,
            self::STATUS_LOCKED                          =>  self::STATUS_423,
            self::STATUS_FAILED_DEPENDENCY               =>  self::STATUS_424,
            self::STATUS_UPGRADE_REQUIRED                =>  self::STATUS_426,
            self::STATUS_PRECONDITION_REQUIRED           =>  self::STATUS_428,
            self::STATUS_TOO_MANY_REQUESTS               =>  self::STATUS_429,
            self::STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE =>  self::STATUS_431,

            //  5xx Server Error
            self::STATUS_INTERNAL_SERVER_ERROR           =>  self::STATUS_500,
            self::STATUS_NOT_IMPLEMENTED                 =>  self::STATUS_501,
            self::STATUS_BAD_GATEWAY                     =>  self::STATUS_502,
            self::STATUS_SERVICE_UNAVAILABLE             =>  self::STATUS_503,
            self::STATUS_GATEWAY_TIMEOUT                 =>  self::STATUS_504,
            self::STATUS_HTTP_VERSION_NOT_SUPPORTED      =>  self::STATUS_505,
            self::STATUS_VARIANT_ALSO_NEGOTIATES         =>  self::STATUS_506,
            self::STATUS_INSUFFICIENT_STORAGE            =>  self::STATUS_507,
            self::STATUS_LOOP_DETECTED                   =>  self::STATUS_508,
            self::STATUS_NOT_EXTENDED                    =>  self::STATUS_510,
            self::STATUS_NETWORK_AUTHENTICATION_REQUIRED =>  self::STATUS_511,
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether an HTTP Status code is valid
     */
    public static function isValid(int $iCode): bool
    {
        return in_array($iCode, array_keys(self::all()));
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the human readable portion of an HTTP status code
     *
     * @param $iCode int The numerical HTTP status code
     *
     * @return string|null
     */
    public static function getByCode($iCode): ?string
    {
        $sConstant = 'static::STATUS_' . $iCode;
        if (defined($sConstant)) {
            return constant($sConstant);
        }

        return null;
    }
}
