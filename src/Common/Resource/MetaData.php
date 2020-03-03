<?php

namespace Nails\Common\Resource;

use Nails\Common\Factory\Locale;
use Nails\Common\Resource;
use Nails\Config;

/**
 * Class MetaData
 *
 * @package Nails\Common\Resource
 */
class MetaData extends Resource
{
    /** @var string[] */
    protected $aTitles = [];

    /** @var Locale */
    protected $oLocale;

    /** @var string */
    protected $sDescription = '';

    /** @var string[] */
    protected $aKeywords = [];

    /** @var string[] */
    protected $aHtmlClasses = [];

    /** @var string[] */
    protected $aBodyClasses = [];

    /** @var string string */
    protected $sTitleSeparator = ' - ';

    /** @var string string */
    protected $sClassSeparator = ' ';

    // --------------------------------------------------------------------------

    /**
     * Kept for backwards compatability
     *
     * @var string
     * @deprecated
     */
    public $title = '';

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
     * @return Resource\MetaData\Collection
     */
    public function getTitles(): Resource\MetaData\Collection
    {
        return new Resource\MetaData\Collection(
            array_filter(
                array_map(
                    'trim',
                    array_merge(
                        $this->aTitles,
                        array_filter([
                            $this->title,
                            Config::get('APP_NAME'),
                        ])
                    )
                )
            ),
            $this->sTitleSeparator
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the SEO locale
     *
     * @param string $oLocale The locale to set
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
     * @return string
     */
    public function getLocale(): Locale
    {
        return $this->oLocale;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the SEO description
     *
     * @param string $sDescription The description to set
     *
     * @return $this
     */
    public function setDescription(string $sDescription): self
    {
        $this->sDescription = $sDescription;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Gets the SEO description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->sDescription;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the SEO keywords
     *
     * @param string[] $aKeywords The keywords to set
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
     * Sets the HTML classes
     *
     * @param string[] $aHtmlClasses The classes to set
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
     * @return Resource\MetaData\Collection
     */
    public function getHtmlClasses(): Resource\MetaData\Collection
    {
        return new Resource\MetaData\Collection($this->aHtmlClasses, $this->sClassSeparator);
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the Body classes
     *
     * @param string[] $aBodyClasses The classes to set
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
     * @return Resource\MetaData\Collection
     */
    public function getBodyClasses(): Resource\MetaData\Collection
    {
        return new Resource\MetaData\Collection($this->aBodyClasses, $this->sClassSeparator);
    }
}
