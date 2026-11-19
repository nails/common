<?php

/**
 * Simple HTTP requests
 *
 * @package     Nails
 * @subpackage  common
 * @category    Factory
 * @author      Nails Dev Team
 */

namespace Nails\Common\Factory;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\NailsException;
use Nails\Common\Helper\ArrayHelper;
use Nails\Config;
use Nails\Environment;
use Nails\Factory;
use Nails\Testing;

/**
 * Class HttpRequest
 *
 * @package Nails\Common\Factory
 */
abstract class HttpRequest
{
    /**
     * The HTTP Method for the request
     *
     * @var string
     */
    const HTTP_METHOD = '';

    // --------------------------------------------------------------------------

    /**
     * The request headers
     *
     * @var array
     */
    protected $aHeaders = [];

    /**
     * The Base URI for the request
     *
     * @var string
     */
    protected $sBaseUri = '';

    /**
     * The path for the request
     *
     * @var string
     */
    protected $sPath = '';

    /**
     * The UserAgent to use for the request
     *
     * @var string
     */
    protected $sUserAgent = 'Nails';

    /**
     * Basic auth username
     *
     * @var string
     */
    protected $sAuthUsername = '';

    /**
     * Basic auth password
     *
     * @var string
     */
    protected $sAuthPassword = '';

    /**
     * Extra Guzzle client constructor settings.
     *
     * Applied after the built-in defaults, so these values win.
     *
     * @var array
     */
    protected $aClientConfig = [];

    /**
     * Extra Guzzle request options.
     *
     * Applied after the options this class builds, so these values win.
     *
     * @var array
     */
    protected $aRequestOptions = [];

    // --------------------------------------------------------------------------

    /**
     * HttpRequest constructor.
     *
     * @param string $sBaseUri The Base URI for the request
     * @param string $sPath    The path for the request
     * @param array  $aHeaders An array of headers to set
     */
    public function __construct(string $sBaseUri = '', string $sPath = '', array $aHeaders = [])
    {
        if (Environment::is(Environment::ENV_TEST)
            && !$sBaseUri
            && !array_key_exists(Testing::TEST_HEADER_NAME, $aHeaders)
        ) {

            $aHeaders[Testing::TEST_HEADER_NAME] = Testing::TEST_HEADER_VALUE;
        }

        $this->baseUri($sBaseUri);
        $this->path($sPath);
        $this->userAgent($this->sUserAgent);

        foreach ($aHeaders as $sHeader => $mValue) {
            $this->setHeader($sHeader, $mValue);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Sets a header
     *
     * @param $sHeader
     * @param $mValue
     *
     * @return $this
     */
    public function setHeader($sHeader, $mValue): self
    {
        if (empty($this->aHeaders)) {
            $this->aHeaders = [];
        }

        $this->aHeaders[$sHeader] = $mValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the request headers
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return isset($this->aHeaders) ? $this->aHeaders : [];
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a single header
     *
     * @param string $sHeader The header to return
     *
     * @return mixed|null
     */
    public function getHeader($sHeader)
    {
        return isset($this->aHeaders[$sHeader]) ? $this->aHeaders[$sHeader] : null;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the required headers for imitating a user
     *
     * @param object|int $oUser The user to imitate
     *
     * @return $this
     */
    public function asUser($oUser): self
    {
        if ($oUser === null) {
            return $this;

        } elseif (is_int($oUser)) {
            $oUser = (object) ['id' => $oUser];

        } elseif (!is_object($oUser) || !property_exists($oUser, 'id')) {
            throw new \InvalidArgumentException(
                sprintf('Passed user must be an object with an `id` property or an integer')
            );
        }

        return $this
            ->setHeader(Testing::TEST_HEADER_NAME, Testing::TEST_HEADER_VALUE)
            ->setHeader(Testing::TEST_HEADER_USER_NAME, $oUser->id);
    }

    // --------------------------------------------------------------------------

    /**
     * Populates the baseUri property of the request
     *
     * @param string $sBaseUri The base for the request
     *
     * @return $this
     */
    public function baseUri(string $sBaseUri): self
    {
        $this->sBaseUri = $sBaseUri ?: Config::get('BASE_URL');
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the base URI
     *
     * @return string
     */
    public function getBaseUri(): string
    {
        return $this->sBaseUri;
    }

    // --------------------------------------------------------------------------

    /**
     * Populates the path property of the request
     *
     * @param string $sPath The path for the request
     *
     * @return $this
     */
    public function path(string $sPath): self
    {
        $this->sPath = $sPath;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->sPath;
    }

    // --------------------------------------------------------------------------

    /**
     * Populates the UserAgent property of the request
     *
     * @param string $sUserAgent The UserAgentfor the request
     *
     * @return $this
     */
    public function userAgent($sUserAgent): self
    {
        return $this->setHeader('User-Agent', $sUserAgent);
    }

    // --------------------------------------------------------------------------

    /**
     * Sets basic auth credentials
     *
     * @param string $sUsername The basic auth username
     * @param string $sPassword The basic auth password
     *
     * @return $this
     */
    public function auth(string $sUsername, string $sPassword): self
    {
        $this->sAuthUsername = $sUsername;
        $this->sAuthPassword = $sPassword;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the total timeout for the request, in seconds.
     *
     * Zero disables the total timeout. Guzzle rejects a positive timeout below
     * one millisecond.
     *
     * @param int|float $mSeconds The timeout, in seconds
     *
     * @return $this
     */
    public function timeout(int|float $mSeconds): self
    {
        return $this->setTimeout(RequestOptions::TIMEOUT, $mSeconds);
    }

    // --------------------------------------------------------------------------

    /**
     * Set how long, in seconds, to wait while connecting.
     *
     * Zero disables the connect timeout. When this is left unset, Guzzle uses
     * its own default (60 seconds in Guzzle 8).
     *
     * @param int|float $mSeconds The timeout, in seconds
     *
     * @return $this
     */
    public function connectTimeout(int|float $mSeconds): self
    {
        return $this->setTimeout(RequestOptions::CONNECT_TIMEOUT, $mSeconds);
    }

    // --------------------------------------------------------------------------

    /**
     * Set how long, in seconds, the connection may sit silent.
     *
     * Zero disables the idle timeout. When this is left unset, Guzzle uses its
     * own default.
     *
     * @param int|float $mSeconds The timeout, in seconds
     *
     * @return $this
     */
    public function readTimeout(int|float $mSeconds): self
    {
        return $this->setTimeout(RequestOptions::READ_TIMEOUT, $mSeconds);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a Guzzle request option.
     *
     * Any option accepted by GuzzleHttp\Client::request() can be set here,
     * including timeout, proxy, cert, json, multipart, sink, curl, verify, and
     * allow_redirects. The value is passed through unchanged and overrides the
     * option this class would otherwise build (headers, auth, query, body, and
     * form parameters). Header maps are combined with setHeader(); the same
     * header name uses the value given here.
     *
     * handler, max_host_connections, max_total_connections, max_idle_handles,
     * and transport_sharing belong to the client. Set those with
     * setClientConfig().
     *
     * @param string $sOption The Guzzle request option name
     * @param mixed  $mValue  The option value
     *
     * @return $this
     */
    public function setOption(string $sOption, mixed $mValue): self
    {
        $this->aRequestOptions[$sOption] = $mValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Merge Guzzle request options.
     *
     * Later calls merge with options already set. Nested values are replaced.
     * Header maps are combined by header name.
     *
     * @param array $aOptions The Guzzle request options
     *
     * @return $this
     * @see self::setOption()
     */
    public function setOptions(array $aOptions): self
    {
        $this->aRequestOptions = $this->mergeOptions($this->aRequestOptions, $aOptions);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * A caller-supplied request option, or null when it has not been set.
     *
     * @param string $sOption The option name
     *
     * @return mixed
     */
    public function getOption(string $sOption): mixed
    {
        return array_key_exists($sOption, $this->aRequestOptions) ? $this->aRequestOptions[$sOption] : null;
    }

    // --------------------------------------------------------------------------

    /**
     * Request options supplied by the caller, before they are merged for sending.
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->aRequestOptions;
    }

    // --------------------------------------------------------------------------

    /**
     * Set a Guzzle client constructor setting.
     *
     * Use this for handler, connection limits, and transport_sharing, and for
     * default request options. A value also given to setOption() is the one
     * used for the request.
     *
     * @param string $sKey   The client setting
     * @param mixed  $mValue The setting value
     *
     * @return $this
     */
    public function setClientConfig(string $sKey, mixed $mValue): self
    {
        $this->aClientConfig[$sKey] = $mValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Merge Guzzle client constructor settings.
     *
     * Later calls merge with settings already set. These are applied after the
     * built-in defaults (base_uri, verify, allow_redirects, http_errors), so
     * keys supplied here override those defaults.
     *
     * @param array $aConfig The client settings
     *
     * @return $this
     */
    public function clientConfig(array $aConfig): self
    {
        $this->aClientConfig = $this->mergeOptions($this->aClientConfig, $aConfig);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Client settings supplied by the caller, before they are merged with the defaults.
     *
     * @return array
     */
    public function getClientConfig(): array
    {
        return $this->aClientConfig;
    }

    // --------------------------------------------------------------------------

    /**
     * Configures and executes the HTTP request
     *
     * @return HttpResponse
     * @throws FactoryException
     */
    public function execute(): HttpResponse
    {
        [$aClientConfig, $aRequestOptions] = $this->build();

        $oClient   = Factory::factory('HttpClient', '', $aClientConfig);
        $oResponse = $oClient->request(static::HTTP_METHOD, $this->sPath, $aRequestOptions);

        return Factory::factory('HttpResponse', '', $oResponse, $this);
    }

    // --------------------------------------------------------------------------

    /**
     * Resolve the Guzzle client configuration and the options for this request.
     *
     * Caller-supplied settings are applied last.
     *
     * @return array[] 0 is the client configuration, 1 is the request options
     */
    protected function build(): array
    {
        $aClientConfig   = [
            'base_uri'        => $this->sBaseUri,
            'verify'          => !(Environment::is(Environment::ENV_DEV) || Environment::is(Environment::ENV_TEST)),
            'allow_redirects' => Environment::not(Environment::ENV_TEST),
            'http_errors'     => false,
        ];
        $aRequestOptions = [
            'headers' => $this->aHeaders,
        ];

        if (!empty($this->sAuthUsername)) {
            $aRequestOptions['auth'] = [
                $this->sAuthUsername,
                $this->sAuthPassword,
            ];
        }

        $this->compile($aClientConfig, $aRequestOptions);

        return [
            $this->mergeOptions($aClientConfig, $this->aClientConfig),
            $this->mergeOptions($aRequestOptions, $this->aRequestOptions),
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Store a timeout request option.
     *
     * @param string    $sOption  The Guzzle timeout option
     * @param int|float $mSeconds The timeout, in seconds
     *
     * @return $this
     */
    private function setTimeout(string $sOption, int|float $mSeconds): self
    {
        if (!is_finite((float) $mSeconds) || $mSeconds < 0) {
            throw new \InvalidArgumentException(
                sprintf('`%s` must be zero or greater.', $sOption)
            );
        }

        return $this->setOption($sOption, $mSeconds);
    }

    // --------------------------------------------------------------------------

    /**
     * Merge two Guzzle option arrays.
     *
     * Header maps are combined by name so later values win per header. Every
     * other key is replaced by the override, including an explicit null.
     *
     * @param array $aBase      The current options
     * @param array $aOverrides The options to apply
     *
     * @return array
     */
    private function mergeOptions(array $aBase, array $aOverrides): array
    {
        $sHeaders = RequestOptions::HEADERS;

        if (array_key_exists($sHeaders, $aOverrides)) {
            if (is_array($aOverrides[$sHeaders])) {
                $aBaseHeaders = [];
                if (isset($aBase[$sHeaders]) && is_array($aBase[$sHeaders])) {
                    $aBaseHeaders = $aBase[$sHeaders];
                }
                $aBase[$sHeaders] = array_replace($aBaseHeaders, $aOverrides[$sHeaders]);
            } else {
                $aBase[$sHeaders] = $aOverrides[$sHeaders];
            }
            unset($aOverrides[$sHeaders]);
        }

        return array_replace($aBase, $aOverrides);
    }

    // --------------------------------------------------------------------------

    /**
     * Compile the request.
     *
     * Adjusts the Guzzle client configuration and request options for this HTTP
     * method. Caller-supplied configuration is applied after this method
     * returns and overrides values written here.
     *
     * @param array $aClientConfig   The config array for the HTTP Client
     * @param array $aRequestOptions The options for the request
     */
    abstract protected function compile(array &$aClientConfig, array &$aRequestOptions): void;
}
