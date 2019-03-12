<?php

namespace Nails\Common\Factory\Locale;

/**
 * Class Script
 *
 * @package Nails\Common\Factory
 */
class Script
{
    /**
     * The script's label
     *
     * @var string
     */
    protected $sLabel;

    // --------------------------------------------------------------------------

    /**
     * Script constructor.
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
     * Set the script's label
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
     * Returns the script's label
     *
     * @return string
     */
    public function getLabel(): ?string
    {
        return $this->sLabel;
    }

    // --------------------------------------------------------------------------

    /**
     * Return the string representation of the script
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getLabel();
    }
}
