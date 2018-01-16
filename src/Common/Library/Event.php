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

namespace Nails\Common\Library;

class Event
{
    protected $aSubscriptions;

    // --------------------------------------------------------------------------

    /**
     * Construct the class, set up any initial event subscriptions
     */
    public function __construct()
    {
        //  Defaults
        $this->aSubscriptions = [];

        //  Set up initial subscriptions
        $aComponents = _NAILS_GET_COMPONENTS();
        foreach ($aComponents as $oComponent) {
            if (!empty($oComponent->namespace)) {
                $this->autoLoadSubscriptions($oComponent->namespace);
            }
        }

        //  Any subscriptions for the app?
        $this->autoLoadSubscriptions('App\\');
    }

    // --------------------------------------------------------------------------

    /**
     * Looks for a component's event handler and executes the autoload() method if there is one
     *
     * @param  string $sNamespace The namespace to check
     *
     * @return void
     */
    protected function autoLoadSubscriptions($sNamespace)
    {
        $sClassName = '\\' . $sNamespace . 'Events';

        if (class_exists($sClassName)) {

            $oClass = new $sClassName();
            if (is_callable([$oClass, 'autoload'])) {
                $aSubscriptions = $oClass->autoload();
                if (!empty($aSubscriptions)) {
                    foreach ($aSubscriptions as $aListener) {

                        $sEvent     = getFromArray(0, $aListener);
                        $sNamespace = getFromArray(1, $aListener);
                        $mCallback  = getFromArray(2, $aListener);

                        if (!empty($sEvent) && !empty($sNamespace) && !empty($mCallback)) {
                            $this->subscribe($sEvent, $sNamespace, $mCallback);
                        }
                    }
                }
            }
            unset($oClass);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Subscribe to an event
     *
     * @param  string $sEvent     The event to subscribe to
     * @param  string $sNamespace The event's namespace
     * @param  mixed  $mCallback  The callback to execute
     *
     * @return \Nails\Common\Library\Event
     */
    public function subscribe($sEvent, $sNamespace, $mCallback)
    {
        $sEvent     = strtoupper($sEvent);
        $sNamespace = strtoupper($sNamespace);

        if (is_callable($mCallback)) {
            if (!isset($this->aSubscriptions[$sNamespace])) {
                $this->aSubscriptions[$sNamespace] = [];
            }

            if (!isset($this->aSubscriptions[$sNamespace][$sEvent])) {
                $this->aSubscriptions[$sNamespace][$sEvent] = [];
            }

            //  Prevent duplicate subscriptions
            $sHash = md5(serialize($mCallback));
            if (!isset($this->aSubscriptions[$sNamespace][$sEvent][$sHash])) {
                $this->aSubscriptions[$sNamespace][$sEvent][$sHash] = $mCallback;
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Trigger the event and execute all callbacks
     *
     * @param  string $sEvent     The event to trigger
     * @param  string $sNamespace The event's namespace
     * @param  array  $aData      Data to pass to the callbacks
     *
     * @return \Nails\Common\Library\Event
     */
    public function trigger($sEvent, $sNamespace = 'nailsapp/common', $aData = [])
    {
        $sNamespace = strtoupper($sNamespace);

        if (!empty($this->aSubscriptions[$sNamespace][$sEvent])) {
            foreach ($this->aSubscriptions[$sNamespace][$sEvent] as $mCallback) {
                if (is_callable($mCallback)) {
                    call_user_func_array($mCallback, $aData);
                }
            }
        }

        return $this;
    }
}
