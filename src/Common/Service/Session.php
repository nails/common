<?php

/**
 * The class handles the user's session
 *
 * //  @todo (Pablo - 2020-03-02) - Properly handle CLI behaviour
 *
 * @package     Nails
 * @subpackage  common
 * @category    Service
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

use Nails\Common\Exception\NailsException;
use Nails\Factory;
use Symfony\Component\HttpFoundation;

/**
 * Class Session
 *
 * @package Nails\Common\Service
 */
class Session
{
    /** @var HttpFoundation\Session\Session */
    protected $oSession;

    // --------------------------------------------------------------------------

    /**
     * Session constructor.
     */
    public function __construct()
    {
        $this->oSession = new HttpFoundation\Session\Session(
            new HttpFoundation\Session\Storage\NativeSessionStorage(),
            new HttpFoundation\Session\Attribute\AttributeBag(),
            new HttpFoundation\Session\Flash\AutoExpireFlashBag()
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the session ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->oSession->getId();
    }

    // --------------------------------------------------------------------------

    /**
     * Sets session flashdata
     *
     * @param string|array $mKey   The key to set, or an associative array of key=>value pairs
     * @param mixed        $mValue The value to store
     *
     * @return $this
     */
    public function setFlashData($mKey, $mValue = null): Session
    {
        if (is_array($mKey)) {
            foreach ($mKey as $sKey => $mValue) {
                $this->oSession->getFlashBag()->set($sKey, $mValue);
            }
        } else {
            $this->oSession->getFlashBag()->set($mKey, $mValue);
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Retrieves flash data from the session
     *
     * @param string $sKey The key to retrieve
     *
     * @return mixed
     */
    public function getFlashData($sKey = null)
    {
        if (empty($sKey)) {
            return array_map(
                function ($aValues) {
                    return count($aValues) ? end($aValues) : null;
                },
                $this->oSession->getFlashBag()->peekAll()
            );
        } else {
            $aValues = $this->oSession->getFlashBag()->peek($sKey);
            return count($aValues) ? end($aValues) : null;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Keeps existing flashdata available to next request.
     *
     * @param string|array $mKey The key to keep, null will retain all flashdata
     *
     * @return $this
     **/
    public function keepFlashData($mKey = null): Session
    {
        //  @todo (Pablo - 2020-02-18) - Complete this
        if (is_null($mKey)) {
            $aKeys = $this->oSession->getFlashBag()->keys();
        } elseif (is_array($mKey)) {
            $aKeys = $mKey;
        } else {
            $aKeys = [$mKey];
        }

        foreach ($aKeys as $sKey) {
            $this->setFlashData($sKey, $this->getFlashData($sKey));
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Writes data to the user's session
     *
     * @param string|array $mKey   The key to set, or an associative array of key=>value pairs
     * @param mixed        $mValue The value to store
     *
     * @return $this
     */
    public function setUserData($mKey, $mValue = null): Session
    {
        if (is_array($mKey)) {
            foreach ($mKey as $sKey => $mValue) {
                $this->oSession->set($sKey, $mValue);
            }
        } else {
            $this->oSession->set($mKey, $mValue);
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Retrieves data from the session
     *
     * @param string $sKey The key to retrieve
     *
     * @return mixed
     */
    public function getUserData(string $sKey = null)
    {
        if (empty($sKey)) {
            return $this->oSession->all();
        } else {
            return $this->oSession->get($sKey);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Removes data from the session
     *
     * @param string|array $mKey The key to set, or an associative array of key=>value pairs
     *
     * @return $this
     */
    public function unsetUserData($mKey): Session
    {
        if (is_array($mKey)) {
            foreach ($mKey as $sKey => $mValue) {
                $this->oSession->remove($sKey);
            }
        } else {
            $this->oSession->remove($mKey);
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Destroy the user's session
     *
     * @return $this
     */
    public function destroy(): Session
    {
        $this->oSession->clear();
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Regenerate the user's session
     *
     * @param bool $bDestroy Whether to destroy the old session
     *
     * @return $this
     */
    public function regenerate($bDestroy = false): Session
    {
        $this->oSession->migrate($bDestroy);
        return $this;
    }
}
