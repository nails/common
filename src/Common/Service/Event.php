<?php

/**
 * The class provides an interface for triggering and subscribing to events
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

use Nails\Common\Interfaces\Event\Subscription;
use Nails\Components;
use stdClass;

/**
 * Class Event
 *
 * @package Nails\Common\Service
 */
class Event
{
    /**
     * The event subscriptions
     *
     * @var array
     */
    protected $aSubscriptions = [];

    // --------------------------------------------------------------------------

    /**
     * Tracks events which have been triggered
     *
     * @var array
     */
    protected $aHistory = [];

    // --------------------------------------------------------------------------

    /**
     * Event constructor.
     */
    public function __construct()
    {
        foreach (Components::available() as $oComponent) {
            $this->autoLoadSubscriptions($oComponent);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Looks for a component's event handler and executes the autoload() method if there is one
     *
     * @param \Nails\Common\Factory\Component $oComponent The component to check
     *
     * @return $this
     */
    protected function autoLoadSubscriptions($oComponent): self
    {
        $aClasses = $oComponent
            ->findClasses('Event\\Listener')
            ->whichImplement(Subscription::class)
            ->whichCanBeInstantiated();

        foreach ($aClasses as $sClass) {

            /** @var Subscription $oSubscription */
            $oSubscription = new $sClass();
            if ($oSubscription->isAutoloaded()) {

                $aEvent     = (array) $oSubscription->getEvent();
                $sNamespace = $oSubscription->getNamespace();
                $mCallback  = $oSubscription->getCallback();
                $bOnce      = $oSubscription->isOnce();

                if (!empty($aEvent) && !empty($sNamespace) && !empty($mCallback)) {
                    foreach ($aEvent as $sEvent) {
                        $this->subscribe($sEvent, $sNamespace, $mCallback, $bOnce);
                    }
                }
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Subscribe to an event
     *
     * @param string $sEvent     The event to subscribe to
     * @param string $sNamespace The event's namespace
     * @param mixed  $mCallback  The callback to execute
     * @param bool   $bOnce      Whether the subscription should only fire once
     *
     * @return $this
     */
    public function subscribe($sEvent, $sNamespace, $mCallback, $bOnce = false): self
    {
        if (is_callable($mCallback)) {
            if (!isset($this->aSubscriptions[$sNamespace])) {
                $this->aSubscriptions[$sNamespace] = [];
            }

            if (!isset($this->aSubscriptions[$sNamespace][$sEvent])) {
                $this->aSubscriptions[$sNamespace][$sEvent] = [];
            }

            //  Prevent duplicate subscriptions
            try {
                $sHash = md5(serialize($mCallback));
            } catch (\Exception $e) {
                $sHash = md5(print_r($mCallback, true));
            }

            if (!isset($this->aSubscriptions[$sNamespace][$sEvent][$sHash])) {
                $this->aSubscriptions[$sNamespace][$sEvent][$sHash] = (object) [
                    'is_once'  => $bOnce,
                    'callback' => $mCallback,
                ];
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Trigger the event and execute all callbacks
     *
     * @param string $sEvent     The event to trigger
     * @param string $sNamespace The event's namespace
     * @param array  $aData      Data to pass to the callbacks
     *
     * @return $this
     */
    public function trigger($sEvent, $sNamespace = \Nails\Common\Constants::MODULE_SLUG, $aData = []): self
    {
        $this->addHistory($sEvent, $sNamespace);

        if (!empty($this->aSubscriptions[$sNamespace][$sEvent])) {
            foreach ($this->aSubscriptions[$sNamespace][$sEvent] as $sSubscriptionHash => $oSubscription) {
                if (is_callable($oSubscription->callback)) {
                    call_user_func_array($oSubscription->callback, $aData);
                }

                if ($oSubscription->is_once) {
                    unset($this->aSubscriptions[$sNamespace][$sEvent][$sSubscriptionHash]);
                }
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Adds a history item to the history array
     *
     * @param string $sEvent     The event name
     * @param string $sNamespace The event namespace
     *
     * @return $this
     */
    protected function addHistory($sEvent, $sNamespace = \Nails\Common\Constants::MODULE_SLUG): self
    {
        if (!array_key_exists($sNamespace, $this->aHistory)) {
            $this->aHistory[$sNamespace] = [];
        }
        if (!array_key_exists($sEvent, $this->aHistory[$sNamespace])) {
            $this->aHistory[$sNamespace][$sEvent] = (object) [
                'count'      => 0,
                'timestamps' => [],
            ];
        }

        $this->aHistory[$sNamespace][$sEvent]->count++;
        $this->aHistory[$sNamespace][$sEvent]->timestamps[] = microtime(true);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Retrieve a history item
     *
     * @param string $sNamespace The event namespace
     * @param string $sEvent     The event name
     *
     * @return array|stdClass|null
     */
    public function getHistory($sNamespace = null, $sEvent = null)
    {
        if (empty($sNamespace) && empty($sEvent)) {
            return $this->aHistory;

        } elseif (
            empty($sEvent) &&
            !empty($sNamespace) &&
            array_key_exists($sNamespace, $this->aHistory)
        ) {
            return $this->aHistory[$sNamespace];

        } elseif (
            !empty($sNamespace) &&
            array_key_exists($sNamespace, $this->aHistory) &&
            !empty($sEvent) &&
            array_key_exists($sEvent, $this->aHistory[$sNamespace])
        ) {
            return $this->aHistory[$sNamespace][$sEvent];
        }

        return null;
    }

    // --------------------------------------------------------------------------

    /**
     * Clear history item(s)
     *
     * @param string $sNamespace The event namespace
     * @param string $sEvent     The event name
     *
     * @return $this
     */
    public function clearHistory($sNamespace = null, $sEvent = null): self
    {
        if (empty($sNamespace)) {
            $this->aHistory = [];

        } elseif (empty($sEvent) && array_key_exists($sNamespace, $this->aHistory)) {
            $this->aHistory[$sNamespace] = [];

        } elseif (
            array_key_exists($sNamespace, $this->aHistory) &&
            array_key_exists($sEvent, $this->aHistory[$sNamespace])
        ) {
            unset($this->aHistory[$sNamespace][$sEvent]);
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Determine whether a particular item has been triggered
     *
     * @param string $sEvent     The event name
     * @param string $sNamespace The event namespace
     *
     * @return bool
     */
    public function hasBeenTriggered($sEvent, $sNamespace = \Nails\Common\Constants::MODULE_SLUG): bool
    {
        return (bool) $this->getHistory($sNamespace, $sEvent);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the currently registered subscriptions
     *
     * @return array
     */
    public function getSubscriptions(): array
    {
        return $this->aSubscriptions;
    }
}
