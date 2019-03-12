<?php

namespace Nails\Common\Factory\Locale;

/**
 * Class Language
 *
 * @package Nails\Common\Factory
 */
class Language
{
    /**
     * The language's label
     *
     * @var string
     */
    protected $sLabel;

    // --------------------------------------------------------------------------

    /**
     * Language constructor.
     *
     * @param string $sLabel The label to set
     */
    public function __construct(string $sLabel = '')
    {
        $this
            ->setLabel($sLabel);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the language's label
     *
     * @param string $sLabel The label to set
     *
     * @return $this
     */
    public function setLabel(string $sLabel = ''): self
    {
        $this->sLabel = $sLabel;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the language's label
     *
     * @return string
     */
    public function getLabel(): ?string
    {
        return $this->sLabel;
    }

    // --------------------------------------------------------------------------

    /**
     * Return the string representation of the language
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getLabel();
    }
}
