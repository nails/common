<?php

namespace Nails\Components;

use Nails\Common\Factory\Model\Field;

/**
 * Class Setting
 *
 * @package Nails\Components
 */
final class Setting extends Field
{
    /**
     * Whether the field is encrypted or not
     *
     * @var bool
     */
    public $encrypted = false;

    // --------------------------------------------------------------------------

    /**
     * The field's render formatter, formats the setting suitable for a view
     *
     * @var callable|null
     */
    public $renderFormatter = null;

    /**
     * The field's save formatter, formats the setting suitable for saving
     *
     * @var callable|null
     */
    public $saveFormatter = null;

    // --------------------------------------------------------------------------

    /**
     * Set whether the field is encrypted
     *
     * @param bool $bEncrypted
     *
     * @return $this
     */
    public function setEncrypted(bool $bEncrypted): self
    {
        $this->encrypted = $bEncrypted;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether the field is encrypted
     *
     * @return bool
     */
    public function isEncrypted(): bool
    {
        return $this->encrypted;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the field's render formatter
     *
     * @param callable $cFormatter
     *
     * @return $this
     */
    public function setRenderFormatter(callable $cFormatter)
    {
        $this->renderFormatter = $cFormatter;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the field's formatter
     *
     * @return callable|null
     */
    public function getRenderFormatter(): ?callable
    {
        return $this->renderFormatter;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the field's save formatter
     *
     * @param callable $cFormatter
     *
     * @return $this
     */
    public function setSaveFormatter(callable $cFormatter)
    {
        $this->saveFormatter = $cFormatter;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the field's formatter
     *
     * @return callable|null
     */
    public function getSaveFormatter(): ?callable
    {
        return $this->saveFormatter;
    }
}
