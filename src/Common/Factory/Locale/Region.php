<?php

namespace Nails\Common\Factory\Locale;

/**
 * Class Region
 *
 * @package Nails\Common\Factory
 */
class Region
{
    /**
     * The region's label
     *
     * @var string
     */
    protected $sLabel;

    // --------------------------------------------------------------------------

    /**
     * Region constructor.
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
     * Set the region's label
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
     * Returns the region's label
     *
     * @return string
     */
    public function getLabel(): ?string
    {
        return $this->sLabel;
    }

    // --------------------------------------------------------------------------

    /**
     * Return the string representation of the region
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getLabel();
    }
}
