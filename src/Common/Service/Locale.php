<?php

namespace Nails\Common\Service;

use Nails\Factory;

class Locale
{
    /**
     * The default locale's language segment
     *
     * @var string
     */
    const DEFAULT_LANGUAGE = 'en';

    /**
     * The default locale's region segment
     *
     * @var string
     */
    const DEFAULT_REGION = 'GB';

    /**
     * The default locale's script segment
     *
     * @var string
     */
    const DEFAULT_SCRIPT = null;

    // --------------------------------------------------------------------------

    /**
     * The active Locale
     *
     * @var \Nails\Common\Factory\Locale
     */
    protected $oLocale;

    // --------------------------------------------------------------------------

    /**
     * Locale constructor.
     *
     * @param \Nails\Common\Factory\Locale|null $oLocale The locale to set
     */
    public function __construct(
        \Nails\Common\Factory\Locale $oLocale = null
    ) {
        $this->set($oLocale ?? $this->detect());
    }

    // --------------------------------------------------------------------------

    /**
     * Attempts to detect the locale from the request
     *
     * @return \Nails\Common\Factory\Locale|null
     */
    public function detect(): \Nails\Common\Factory\Locale
    {
        /**
         * Detect the locale from the request, in order of preference
         * - Explicitly provided via $_GET['locale']
         * - Explicitly provided via /{locale}/.*
         * - User preference
         * - Request headers
         */

        $oLocale = $this->getDefautLocale();
        $this
            ->detectFromQuery($oLocale)
            ->detectFromHeader($oLocale)
            ->detectFromCookie($oLocale)
            ->detectFromUser($oLocale);

        return $this
            ->set($oLocale)
            ->get();
    }

    // --------------------------------------------------------------------------

    /**
     * Looks for a local index in the query string
     *
     * @param \Nails\Common\Factory\Locale $oLocale The locale object to update
     *
     * @return $this
     */
    protected function detectFromQuery(\Nails\Common\Factory\Locale &$oLocale)
    {
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Parses the request headers for a locale
     *
     * @param \Nails\Common\Factory\Locale $oLocale The locale object to update
     *
     * @return $this
     */
    protected function detectFromHeader(\Nails\Common\Factory\Locale &$oLocale)
    {
        if (extension_loaded('int')) {
            dd(\Locale::acceptFromHttp());
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Looks for a locale cookie
     *
     * @param \Nails\Common\Factory\Locale $oLocale The locale object to update
     *
     * @return $this
     */
    protected function detectFromCookie(\Nails\Common\Factory\Locale &$oLocale)
    {
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if a logged in user has a preference set
     *
     * @param \Nails\Common\Factory\Locale $oLocale The locale object to update
     *
     * @return $this
     */
    protected function detectFromUser(\Nails\Common\Factory\Locale &$oLocale)
    {
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Manually sets a locale
     *
     * @param \Nails\Common\Factory\Locale $oLocale
     */
    public function set(\Nails\Common\Factory\Locale $oLocale = null): self
    {
        $this->oLocale = $oLocale;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the active Locale
     *
     * @return \Nails\Common\Factory\Locale
     */
    public function get(): \Nails\Common\Factory\Locale
    {
        return $this->oLocale;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default locale to use for the system
     *
     * @return \Nails\Common\Factory\Locale
     * @throws \Nails\Common\Exception\FactoryException
     */
    public function getDefautLocale(): \Nails\Common\Factory\Locale
    {
        return Factory::factory('Locale')
            ->setLanguage(static::DEFAULT_LANGUAGE)
            ->setRegion(static::DEFAULT_REGION)
            ->setScript(static::DEFAULT_SCRIPT);
    }
}
