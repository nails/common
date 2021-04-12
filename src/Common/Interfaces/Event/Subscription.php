<?php

/**
 * The class provides an interface for Event Subscriptions
 *
 * @package     Nails
 * @subpackage  module-cms
 * @category    Events
 * @author      Nails Dev Team
 */

namespace Nails\Common\Interfaces\Event;

interface Subscription
{
    /**
     * Set the subscription event name
     *
     * @param string|array $mEvent The subscription event name, or array of event names
     *
     * @return $this
     */
    public function setEvent($mEvent): self;

    // --------------------------------------------------------------------------

    /**
     * Get the subscription event name
     *
     * @return mixed
     */
    public function getEvent();

    // --------------------------------------------------------------------------

    /**
     * Set the subscription namespace
     *
     * @param string $sNamespace The subscription namespace
     *
     * @return $this
     */
    public function setNamespace(string $sNamespace): self;

    // --------------------------------------------------------------------------

    /**
     * Get the subscription event namespace
     *
     * @return string
     */
    public function getNamespace(): string;

    // --------------------------------------------------------------------------

    /**
     * Set the subscription callback
     *
     * @param callable $cCallback The subscription callback
     *
     * @return $this
     */
    public function setCallback($cCallback): self;

    // --------------------------------------------------------------------------

    /**
     * Get the subscription event callback
     *
     * @return mixed
     */
    public function getCallback();

    // --------------------------------------------------------------------------

    /**
     * Set the subscription callback
     *
     * @param bool $bOnce Whether the subscription should only be fired once
     *
     * @return $this
     */
    public function setOnce(bool $bOnce = true): self;

    // --------------------------------------------------------------------------

    /**
     * Returns whether the subscription should be fired more than once
     *
     * @return bool
     */
    public function isOnce(): bool;

    // --------------------------------------------------------------------------

    /**
     * Returns whether the subscription should be fired more than once
     *
     * @return bool
     */
    public function isAutoloaded(): bool;
}
