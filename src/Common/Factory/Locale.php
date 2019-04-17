<?php

namespace Nails\Common\Factory;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Factory\Locale\Language;
use Nails\Common\Factory\Locale\Region;
use Nails\Common\Factory\Locale\Script;
use Nails\Factory;

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
     * @var Language
     */
    protected $oLanguage;

    /**
     * The locale's region
     *
     * @var Region
     */
    protected $oRegion;

    /**
     * The locale's script
     *
     * @var Script
     */
    protected $oScript;

    // --------------------------------------------------------------------------

    /**
     * Locale constructor.
     *
     * @param string $oLanguage The language to set
     */
    public function __construct(Language $oLanguage = null, Region $oRegion = null, Script $oScript = null)
    {
        $this
            ->setLanguage($oLanguage)
            ->setRegion($oRegion)
            ->setScript($oScript);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the locale's language
     *
     * @param Language|null $oLanguage The language to set
     *
     * @return $this
     */
    public function setLanguage(Language $oLanguage = null): self
    {
        $this->oLanguage = $oLanguage;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the locale's language
     *
     * @return Language|null
     */
    public function getLanguage(): ?Language
    {
        return $this->oLanguage;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the locale's region
     *
     * @param Region|null $oRegion The region to set
     *
     * @return $this
     */
    public function setRegion(Region $oRegion = null): self
    {
        $this->oRegion = $oRegion;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the locale's region
     *
     * @return Region|null
     */
    public function getRegion(): ?Region
    {
        return $this->oRegion;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the locale's script
     *
     * @param Script|null $oScript The script to set
     *
     * @return $this
     */
    public function setScript(Script $oScript = null): self
    {
        $this->oScript = $oScript;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the locale's script
     *
     * @return Script|null
     */
    public function getScript(): ?Script
    {
        return $this->oScript;
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
                (string) $this->getLanguage(),
                (string) $this->getRegion(),
                (string) $this->getScript(),
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

    // --------------------------------------------------------------------------

    /**
     * Returns the locale's display language
     *
     * @return string
     */
    public function getDisplayLanguage(): string
    {
        return \Locale::getDisplayLanguage($this->getAsString()) . ' (' . $this->getRegion() . ')';
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the flag emoji for this locale
     *
     * @return string
     * @throws FactoryException
     */
    public function getFlagEmoji(): string
    {
        return Factory::service('Locale')::flagEmoji($this);
    }
}
