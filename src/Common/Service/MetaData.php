<?php

namespace Nails\Common\Service;

use Nails\Common\Factory\Locale;
use Nails\Common\Service;
use Nails\Config;

/**
 * Class MetaData
 *
 * @package Nails\Common\Service
 */
class MetaData
{
    protected ?string $sAppName            = null;
    protected array   $aTitles             = [];
    protected bool    $bTitleAppendAppName = true;
    protected ?Locale $oLocale             = null;
    protected ?string $sDescription        = null;
    protected ?string $sCanonicalUrl       = null;
    protected array   $aKeywords           = [];
    protected ?string $sImageUrl           = null;
    protected ?int    $iImageWidth         = null;
    protected ?int    $iImageHeight        = null;
    protected ?string $sThemeColour        = null;
    protected ?string $sTwitterHandle      = null;
    protected array   $aHtmlClasses        = [];
    protected array   $aBodyClasses        = [];
    protected string  $sTitleSeparator     = ' - ';
    protected string  $sClassSeparator     = ' ';
    protected bool    $bNoIndex            = false;

    /** @var bool */
    protected $bFollow = true;

    // --------------------------------------------------------------------------

    /**
     * Kept for backwards compatability
     *
     * @var string
     * @deprecated
     */
    public string $title = '';

    // --------------------------------------------------------------------------

    public function setAppName(string $sAppName): self
    {
        $this->sAppName = $sAppName;
        return $this;
    }

    // --------------------------------------------------------------------------

    public function getAppName(): string
    {
        return $this->sAppName ?? Config::get('APP_NAME');
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the SEO titles
     *
     * @param array $aTitles The titles to set
     *
     * @return $this
     */
    public function setTitles(array $aTitles): self
    {
        $this->aTitles = $aTitles;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Gets the SEO title
     *
     * @return Service\MetaData\Collection
     */
    public function getTitles(): Service\MetaData\Collection
    {
        return new Service\MetaData\Collection(
            array_filter(
                array_map(
                    'trim',
                    array_merge(
                        $this->aTitles,
                        array_filter([
                            $this->title,
                            $this->isTitleAppendAppName()
                                ? Config::get('APP_NAME')
                                : null,
                        ])
                    )
                )
            ),
            $this->getTitleSeparator()
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Set whether to append the app name to the titles
     *
     * @param bool $bAppend Whether to append the app name to the titles
     *
     * @return $this
     */
    public function setTitleAppendAppName(bool $bAppend): self
    {
        $this->bTitleAppendAppName = $bAppend;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Whether to append the app name to the titles
     *
     * @return bool
     */
    public function isTitleAppendAppName(): bool
    {
        return $this->bTitleAppendAppName;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the separator for titles
     *
     * @param string $sSeparator The separator to use
     *
     * @return $this
     */
    public function setTitleSeparator(string $sSeparator): self
    {
        $this->sTitleSeparator = $sSeparator;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getTitleSeparator(): string
    {
        return $this->sTitleSeparator;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the SEO locale
     *
     * @param Locale $oLocale The locale to set
     *
     * @return $this
     */
    public function setLocale(Locale $oLocale): self
    {
        $this->oLocale = $oLocale;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Gets the SEO locale
     *
     * @return Locale|null
     */
    public function getLocale(): ?Locale
    {
        return $this->oLocale;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the SEO description
     *
     * @param string|null $sDescription The description to set
     *
     * @return $this
     */
    public function setDescription(?string $sDescription): self
    {
        $this->sDescription = $sDescription;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Gets the SEO description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->sDescription;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the canonical URL
     *
     * @param string|null $sCanonicalUrl The canonical URL to set
     *
     * @return $this
     */
    public function setCanonicalUrl(?string $sCanonicalUrl): self
    {
        $this->sCanonicalUrl = $sCanonicalUrl;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Gets the canonical URL
     *
     * @return string|null
     */
    public function getCanonicalUrl(): ?string
    {
        return $this->sCanonicalUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the SEO keywords
     *
     * @param string[] $aKeywords The keywords to set
     *
     * @return $this
     */
    public function setKeywords(array $aKeywords): self
    {
        $this->aKeywords = $aKeywords;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Gets the SEO keywords
     *
     * @return string[]
     */
    public function getKeywords(): array
    {
        return $this->aKeywords;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the Image URL
     *
     * @param string|null $sImageUrl The image URL to set
     *
     * @return $this
     */
    public function setImageUrl(?string $sImageUrl): self
    {
        $this->sImageUrl = $sImageUrl;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Gets the Image URL
     *
     * @return string|null
     */
    public function getImageUrl(): ?string
    {
        return $this->sImageUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the Image width
     *
     * @param int|null $iImageWidth The image width to set
     *
     * @return $this
     */
    public function setImageWidth(?int $iImageWidth): self
    {
        $this->iImageWidth = $iImageWidth;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Gets the Image width
     *
     * @return int|null
     */
    public function getImageWidth(): ?int
    {
        return $this->iImageWidth;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the Image height
     *
     * @param int|null $iImageHeight The image height to set
     *
     * @return $this
     */
    public function setImageHeight(?int $iImageHeight): self
    {
        $this->iImageHeight = $iImageHeight;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Gets the Image height
     *
     * @return int|null
     */
    public function getImageHeight(): ?int
    {
        return $this->iImageHeight;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the theme colour
     *
     * @param string|null $sThemeColour The theme colour to set
     *
     * @return $this
     */
    public function setThemeColour(?string $sThemeColour): self
    {
        $this->sThemeColour = $sThemeColour;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Gets the theme colour
     *
     * @return string|null
     */
    public function getThemeColour(): ?string
    {
        return $this->sThemeColour;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the Twitter handle
     *
     * @param string|null $sTwitterHandle The Twitter handle to set
     *
     * @return $this
     */
    public function setTwitterHandle(?string $sTwitterHandle): self
    {
        $this->sTwitterHandle = $sTwitterHandle;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Gets the Twitter handle
     *
     * @return string|null
     */
    public function getTwitterHandle(): ?string
    {
        return $this->sTwitterHandle;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the HTML classes
     *
     * @param string[] $aHtmlClasses The classes to set
     *
     * @return $this
     */
    public function setHtmlClasses(array $aHtmlClasses): self
    {
        $this->aHtmlClasses = $aHtmlClasses;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Gets the HTML classes
     *
     * @return Service\MetaData\Collection
     */
    public function getHtmlClasses(): Service\MetaData\Collection
    {
        return new Service\MetaData\Collection($this->aHtmlClasses, $this->sClassSeparator);
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the Body classes
     *
     * @param string[] $aBodyClasses The classes to set
     *
     * @return $this
     */
    public function setBodyClasses(array $aBodyClasses): self
    {
        $this->aBodyClasses = $aBodyClasses;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Gets the Body classes
     *
     * @return Service\MetaData\Collection
     */
    public function getBodyClasses(): Service\MetaData\Collection
    {
        return new Service\MetaData\Collection($this->aBodyClasses, $this->sClassSeparator);
    }

    // --------------------------------------------------------------------------

    /**
     * Sets whether the page should be indexed or not
     *
     * @param bool $bNoIndex
     *
     * @return $this
     */
    public function setNoIndex(bool $bNoIndex): self
    {
        $this->bNoIndex = $bNoIndex;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Gets whether the page should be indexed or not
     *
     * @return bool
     */
    public function getNoIndex(): bool
    {
        return $this->bNoIndex;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets whether the page should be followed or not
     *
     * @param bool $bFollow
     *
     * @return $this
     */
    public function setFollow(bool $bFollow): self
    {
        $this->bFollow = $bFollow;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Gets whether the page should be indexed or not
     *
     * @return bool
     */
    public function getFollow(): bool
    {
        return $this->bFollow;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the robots meta tag
     */
    public function getRobots(): string
    {
        return implode(',', array_filter([
            $this->bNoIndex ? 'noindex' : 'index',
            $this->bFollow ? 'follow' : 'nofollow',
        ]));
    }
}
