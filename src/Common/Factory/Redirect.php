<?php

namespace Nails\Common\Factory;

use Closure;
use Nails\Bootstrap;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\Redirect\InvalidDestinationException;
use Nails\Common\Exception\Redirect\InvalidHttpResponseCodeException;
use Nails\Common\Exception\Redirect\InvalidMethodException;
use Nails\Common\Service;
use Nails\Common\Service\UserFeedback;
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

    protected string $sUrl;
    protected string $sMethod;
    protected bool $bAllowExternal;
    protected int $iHttpResponseCode;
    protected string $sAppDomain;
    /** @var string[] */
    protected array $aSafeDomains = [];

    /**
     * The following properties and associated constructor arguments make it
     * easier to test this class and are not intended for use by the app
     */
    protected Service\UserFeedback $oUserFeedback;
    protected string $sBootstrapClass;

    // --------------------------------------------------------------------------

    /**
     * @param string            $sUrl
     * @param string            $sMethod
     * @param int               $iHttpResponseCode
     * @param bool              $bAllowExternal
     * @param array             $aSafeDomains
     * @param UserFeedback|null $oUserFeedback
     * @param string|null       $sBootstrapClass
     *
     * @throws FactoryException
     * @throws InvalidHttpResponseCodeException
     * @throws InvalidMethodException
     */
    public function __construct(
        string $sUrl = '',
        string $sMethod = self::METHOD_LOCATION,
        int $iHttpResponseCode = self::HTTP_CODE_TEMPORARY,
        bool $bAllowExternal = false,
        array $aSafeDomains = [],

        /**
         * The following properties and associated constructor arguments make it
         * easier to test this class and are not intended for use by the app
         */
        ?Service\UserFeedback $oUserFeedback = null,
        ?string $sBootstrapClass = null
    ) {
        $this
            ->setUrl($sUrl)
            ->setMethod($sMethod)
            ->setHttpResponseCode($iHttpResponseCode)
            ->allowExternal($bAllowExternal)
            ->setSafeDomains($aSafeDomains);

        $this->oUserFeedback   = $oUserFeedback ?? Factory::service('UserFeedback');
        $this->sBootstrapClass = $sBootstrapClass ?? Bootstrap::class;
    }

    // --------------------------------------------------------------------------

    public function setAppDomain(string $sAppDomain): self
    {
        $this->sAppDomain = $sAppDomain;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the application's domain
     *
     * @return string
     */
    public function getAppDomain(): string
    {
        return $this->sAppDomain ?? Config::get('BASE_URL');
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
     * Adds a domain to the list of safe domains to which redirects are permitted.
     * A URL whose host matches any safe domain is considered internal.
     *
     * @param string $sDomain A full URL or host – the host component is extracted for comparison.
     *
     * @return $this
     */
    public function addSafeDomain(string $sDomain): self
    {
        $this->aSafeDomains[] = $sDomain;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Replaces the entire list of safe domains.
     *
     * @param string[] $aDomains
     *
     * @return $this
     */
    public function setSafeDomains(array $aDomains): self
    {
        $this->aSafeDomains = $aDomains;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all safe domains. The effective list is built by merging:
     *   1. The application's URL (always included)
     *   2. Any domains defined via the REDIRECT_SAFE_DOMAINS config variable
     *   3. Any domains added at the instance level via the constructor or setter methods
     *
     * @return string[]
     */
    public function getSafeDomains(): array
    {
        $aConfigDomains = (array) (Config::get('REDIRECT_SAFE_DOMAINS') ?? []);
        return array_values(array_filter(array_unique(array_merge(
            [$this->getAppDomain()],
            $aConfigDomains,
            $this->aSafeDomains,
        ))));
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
     * @param int $iHttpResponseCode
     *
     * @return $this
     * @throws InvalidHttpResponseCodeException
     */
    public function setHttpResponseCode(int $iHttpResponseCode): self
    {
        if (!in_array($iHttpResponseCode, static::getHttpCodes())) {
            throw new InvalidHttpResponseCodeException(sprintf(
                '`%s` is not a valid HTTP code, must be one of: %s',
                $iHttpResponseCode,
                implode(', ', static::getHttpCodes())
            ));
        }

        $this->iHttpResponseCode = $iHttpResponseCode;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @return int
     */
    public function getHttpResponseCode(): int
    {
        return $this->iHttpResponseCode;
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
     * @param Closure|null $cInspect
     *
     * @return void
     * @throws InvalidDestinationException
     */
    public function execute(?Closure $cInspect = null): void
    {
        $sUrl = $this->getUrl();
        if (!preg_match('/^https?:\/\//', $sUrl)) {
            $sUrl = $this->getAppDomain() . $sUrl;
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
                    ? $cInspect($sHeader, $this->getHttpResponseCode())
                    : header($sHeader, true, $this->getHttpResponseCode());
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

        foreach ($this->getSafeDomains() as $sSafeDomain) {
            $aSafeDomain = parse_url($sSafeDomain);
            if ($aUrl['host'] === ($aSafeDomain['host'] ?? null)) {
                return false;
            }
        }

        return true;
    }
}
