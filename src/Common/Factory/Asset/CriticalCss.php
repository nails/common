<?php

namespace Nails\Common\Factory\Asset;

use Nails\Common\Service\Asset;

/**
 * Class CriticalCss
 *
 * @package Nails\Common\Factory\Asset
 */
class CriticalCss
{
    /** @var Asset */
    protected $oAsset;

    /** @var string */
    protected $sDeferredStylesheet;

    /** @var string[] */
    protected $aInlineCss = [];

    // --------------------------------------------------------------------------

    /**
     * CriticalCss constructor.
     *
     * @param Asset $oAsset The asset service
     */
    public function __construct(Asset $oAsset)
    {
        $this->oAsset = $oAsset;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the path of the deferred stylesheet
     *
     * @param string $sPath The deferred stylesheet's path
     *
     * @return $this
     */
    public function setDeferredStylesheet(string $sPath): self
    {
        $this->sDeferredStylesheet = $sPath;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the currently set deferred stylesheet
     *
     * @return string|null
     */
    public function getDeferredStylesheet(): ?string
    {
        return $this->sDeferredStylesheet;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the inline styles to use, either as explicit CSS or paths
     *
     * @param string[] $aInlineCss The CSS or paths
     */
    public function setInlineCss(array $aInlineCss): self
    {
        $this->aInlineCss = array_filter(array_unique($aInlineCss));
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the currently set inline CSS or paths
     *
     * @return string[]
     */
    public function getInlineCss(): array
    {
        return $this->aInlineCss;
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the critical CSS output
     *
     * @return string
     */
    public function render(): string
    {
        $sInline   = $this->renderInline();
        $sDeferred = $sInline
            ? $this->renderDeferredStylesheet()
            : $this->renderImmediateStylesheet();

        return sprintf(
            '%s%s',
            $sInline,
            $sDeferred
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the inline styles
     *
     * @return string
     */
    protected function renderInline(): string
    {
        $aOut = [];
        foreach ($this->getInlineCss() as $sInlineCss) {
            $aOut[] = strpos($sInlineCss, DIRECTORY_SEPARATOR) === 0
                ? (fileExistsCS($sInlineCss) ? file_get_contents($sInlineCss) : '')
                : $sInlineCss;
        }

        $aOut = array_filter(array_unique($aOut));

        return !empty($aOut)
            ? sprintf(
                '<style type="text/css">%s</style>',
                implode('', $aOut)
            ) : '';
    }

    // --------------------------------------------------------------------------

    protected function getDeferredStylesheetUrl(): ?string
    {
        $sStylesheet = $this->getDeferredStylesheet();
        if (empty($sStylesheet)) {
            return null;
        }

        if (preg_match('/^https?:\/\//', $sStylesheet)) {
            return $sStylesheet;
        }

        return $this->oAsset->buildUrl($this->oAsset->getCssDir() . $sStylesheet);
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the deferred stylesheet
     *
     * @return string
     */
    protected function renderDeferredStylesheet(): string
    {
        $sUrl = $this->getDeferredStylesheetUrl();
        return $sUrl
            ? sprintf(
                '<link rel="stylesheet" as="style" href="%s" media="print" onload="this.media=\'all\'">',
                $sUrl
            ) : '';
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the deferred stylesheet immediately (used when there is no inline critical CSS)
     *
     * @return string
     */
    protected function renderImmediateStylesheet(): string
    {
        $sUrl = $this->getDeferredStylesheetUrl();
        return $sUrl
            ? sprintf(
                '<link href="%s" rel="stylesheet" type="text/css"/>',
                $sUrl
            ) : '';
    }
}
