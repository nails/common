<?php

/**
 * The class provides an interface for Event Subscriptions
 *
 * @package     Nails
 * @subpackage  module-cms
 * @category    Events
 * @author      Nails Dev Team
 */

namespace Nails\Common\Events;

class Subscription
{
    /**
     * The name of the event being subscribed to
     *
     * @var string|array
     */
    protected $mEvent;

    /**
     * The namespace of the event being subscribed to
     *
     * @var string
     */
    protected $sNamespace = 'nails/common';

    /**
     * The callback to execute when triggered
     *
     * @var callable
     */
    protected $cCallback;

    /**
     * Whether the subscription should only be fired once
     *
     * @var bool
     */
    protected $bOnce;

    // --------------------------------------------------------------------------

    /**
     * Set the subscription event name
     *
     * @param string|array $mEvent The subscription event name, or array of event names
     *
     * @return $this
     */
    public function setEvent($mEvent): self
    {
        $this->mEvent = $mEvent;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the subscription event name
     *
     * @return mixed
     */
    public function getEvent()
    {
        return $this->mEvent;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the subscription namespace
     *
     * @param string $sNamespace The subscription namespace
     *
     * @return $this
     */
    public function setNamespace(string $sNamespace): self
    {
        $this->sNamespace = $sNamespace;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the subscription event namespace
     *
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->sNamespace;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the subscription callback
     *
     * @param callable $cCallback The subscription callback
     *
     * @return $this
     */
    public function setCallback($cCallback): self
    {
        $this->cCallback = $cCallback;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the subscription event callback
     *
     * @return mixed
     */
    public function getCallback()
    {
        return $this->cCallback;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the subscription callback
     *
     * @param bool $bOnce Whether the subscription should only be fired once
     *
     * @return $this
     */
    public function setOnce(bool $bOnce = true): self
    {
        $this->bOnce = $bOnce;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether the subscription should be fired more than once
     *
     * @return bool
     */
    public function isOnce(): bool
    {
        return (bool) $this->bOnce;
    }
}
