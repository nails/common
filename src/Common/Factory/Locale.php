<?php

namespace Nails\Common\Factory;

/**
 * Class Locale
 *
 * @package Nails\Common\Factory
 */
class Locale
{
    /**
     * The locale's language
     *
     * @var string
     */
    protected $sLanguage;

    /**
     * The locale's region
     *
     * @var string
     */
    protected $sRegion;

    /**
     * The locale's script
     *
     * @var string
     */
    protected $sScript;

    // --------------------------------------------------------------------------

    /**
     * Locale constructor.
     *
     * @param string $sLanguage The language to set
     */
    public function __construct(string $sLanguage = null, string $sRegion = null, string $sScript = null)
    {
        $this
            ->setLanguage($sLanguage)
            ->setRegion($sRegion)
            ->setScript($sScript);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the locale's language
     *
     * @param string|null $sLanguage The language to set
     *
     * @return $this
     */
    public function setLanguage(string $sLanguage = null): self
    {
        $this->sLanguage = $sLanguage;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the locale's language
     *
     * @return string|null
     */
    public function getLanguage(): ?string
    {
        return $this->sLanguage;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the locale's region
     *
     * @param string|null $sRegion The region to set
     *
     * @return $this
     */
    public function setRegion(string $sRegion = null): self
    {
        $this->sRegion = $sRegion;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the locale's region
     *
     * @return string|null
     */
    public function getRegion(): ?string
    {
        return $this->sRegion;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the locale's script
     *
     * @param string|null $sScript The script to set
     *
     * @return $this
     */
    public function setScript(string $sScript = null): self
    {
        $this->sScript = $sScript;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the locale's script
     *
     * @return string|null
     */
    public function getScript(): ?string
    {
        return $this->sScript;
    }

    // --------------------------------------------------------------------------

    /**
     * Compute the string representation of the locale
     *
     * @return string
     */
    public function getAsString(): string
    {
        return implode(
            '_',
            array_filter([
                $this->getLanguage(),
                $this->getRegion(),
                $this->getScript(),
            ])
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Return the string representation of the locale
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getAsString();
    }
}
