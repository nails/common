<?php

namespace Nails\Common\Factory;

use Nails\Bootstrap;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\Redirect\InvalidDestinationException;
use Nails\Common\Exception\Redirect\InvalidLocationHttpResponseCodeException;
use Nails\Common\Exception\Redirect\InvalidMethodException;
use Nails\Common\Exception\Redirect\RedirectException;
use Nails\Common\Service;
use Nails\Config;
use Nails\Factory;

/**
 * Class Redirect
 *
 * @package Nails\Common\Factory
 */
class Redirect
{
    const METHOD_LOCATION = 'location';
    const METHOD_REFRESH  = 'refresh';

    const HTTP_CODE_PERMANENT = Service\HttpCodes::STATUS_MOVED_PERMANENTLY;
    const HTTP_CODE_TEMPORARY = Service\HttpCodes::STATUS_FOUND;

    // --------------------------------------------------------------------------

    protected string $sLocalHost;
    protected string $sUrl;
    protected string $sMethod;
    protected bool $bAllowExternal;
    protected int $iLocationHttpResponseCode;

    /**
     * The following properties and associated constructor arguments make it
     * easier to test this class and are not intended for use by the app
     */
    protected Service\UserFeedback $oUserFeedback;
    protected string $sBootstrapClass;

    // --------------------------------------------------------------------------

    /**
     * @param string                    $sUrl
     * @param string                    $sMethod
     * @param int                       $iLocationHttpResponseCode
     * @param bool                      $bAllowExternal
     * @param Service\UserFeedback|null $oUserFeedback
     * @param string|null               $sBootstrapClass
     *
     * @throws InvalidMethodException
     * @throws RedirectException
     * @throws FactoryException
     */
    public function __construct(
        string $sUrl = '',
        string $sMethod = self::METHOD_LOCATION,
        int $iLocationHttpResponseCode = self::HTTP_CODE_TEMPORARY,
        bool $bAllowExternal = false,

        /**
         * The following properties and associated constructor arguments make it
         * easier to test this class and are not intended for use by the app
         */
        Service\UserFeedback $oUserFeedback = null,
        string $sBootstrapClass = null
    ) {
        $this
            ->setUrl($sUrl)
            ->setMethod($sMethod)
            ->setLocationHttpResponseCode($iLocationHttpResponseCode)
            ->allowExternal($bAllowExternal);

        $this->oUserFeedback   = $oUserFeedback ?? Factory::service('UserFeedback');
        $this->sBootstrapClass = $sBootstrapClass ?? Bootstrap::class;
    }

    // --------------------------------------------------------------------------

    /**
     * @param string $sUrl
     *
     * @return $this
     */
    public function setUrl(string $sUrl): self
    {
        $this->sUrl = siteUrl($sUrl);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->sUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * @param string $sLocalHost
     *
     * @return $this
     */
    public function setLocalHost(string $sLocalHost): self
    {
        $this->sLocalHost = $sLocalHost;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getLocalHost(): string
    {
        return $this->sLocalHost ?? Config::get('BASE_URL');
    }

    // --------------------------------------------------------------------------

    /**
     * @param string $sMethod
     *
     * @return $this
     * @throws InvalidMethodException
     */
    public function setMethod(string $sMethod): self
    {
        $sMethod = strtolower($sMethod);
        if (!in_array($sMethod, static::getMethods())) {
            throw new InvalidMethodException(sprintf(
                '`%s` is not a valid redirect method, must be one of: %s',
                $sMethod,
                implode(', ', static::getMethods())
            ));
        }

        $this->sMethod = $sMethod;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->sMethod;
    }

    // --------------------------------------------------------------------------

    /**
     * @param int $iLocationHttpResponseCode
     *
     * @return $this
     * @throws InvalidLocationHttpResponseCodeException
     */
    public function setLocationHttpResponseCode(int $iLocationHttpResponseCode): self
    {
        if (!in_array($iLocationHttpResponseCode, static::getHttpCodes())) {
            throw new InvalidLocationHttpResponseCodeException(sprintf(
                '`%s` is not a valid HTTP code, must be one of: %s',
                $iLocationHttpResponseCode,
                implode(', ', static::getHttpCodes())
            ));
        }

        $this->iLocationHttpResponseCode = $iLocationHttpResponseCode;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @return int
     */
    public function getLocationHttpResponseCode(): int
    {
        return $this->iLocationHttpResponseCode;
    }

    // --------------------------------------------------------------------------

    /**
     * @param bool $bAllow
     *
     * @return $this
     */
    public function allowExternal(bool $bAllow = true): self
    {
        $this->bAllowExternal = $bAllow;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @return bool
     */
    public function isAllowExternal(): bool
    {
        return $this->bAllowExternal;
    }

    // --------------------------------------------------------------------------

    /**
     * @return string[]
     */
    public static function getMethods(): array
    {
        return [
            static::METHOD_LOCATION,
            static::METHOD_REFRESH,
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * @return int[]
     */
    public static function getHttpCodes(): array
    {
        return [
            static::HTTP_CODE_TEMPORARY,
            static::HTTP_CODE_PERMANENT,
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * @param \Closure|null $cInspect
     *
     * @return void
     * @throws InvalidDestinationException
     */
    public function execute(\Closure $cInspect = null): void
    {
        $sUrl = $this->getUrl();
        if (!preg_match('/^https?:\/\//', $sUrl)) {
            $sUrl = $this->getLocalHost() . $sUrl;
        }

        // --------------------------------------------------------------------------

        $this->isValidDestination($sUrl);

        // --------------------------------------------------------------------------

        /**
         * Persist any generated UserFeedback and call the Bootstrap::shutdown method,
         * the system will be killed imminently so this is the last chance to clean up.
         */
        $this->oUserFeedback->persist();
        call_user_func($this->sBootstrapClass . '::shutdown');

        // --------------------------------------------------------------------------

        switch ($this->getMethod()) {
            case static::METHOD_REFRESH:
                $sHeader = 'Refresh:0;url=' . $sUrl;
                $cInspect
                    ? $cInspect($sHeader)
                    : header($sHeader);
                break;

            case static::METHOD_LOCATION:
            default:
                $sHeader = 'Location: ' . $sUrl;
                $cInspect
                    ? $cInspect($sHeader, $this->getLocationHttpResponseCode())
                    : header($sHeader, true, $this->getLocationHttpResponseCode());
                break;
        }

        if (!$cInspect) {
            exit;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * @return $this
     * @throws InvalidDestinationException
     */
    protected function isValidDestination(string $sUrl): self
    {
        if (!$this->isAllowExternal() && $this->isUrlExternal($sUrl)) {
            throw new InvalidDestinationException('Invalid redirect URL; external redirect not permitted');
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    protected function isUrlExternal(string $sUrl): bool
    {
        $aUrl = parse_url($sUrl);

        if (empty($aUrl['host'])) {
            return false;
        }

        $aLocalHost = parse_url($this->getLocalHost());

        return $aUrl['host'] !== ($aLocalHost['host'] ?? null);
    }
}
