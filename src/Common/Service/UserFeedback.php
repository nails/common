<?php

/**
 * The class provides a centralised, semantic way of giving the user feedback
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 *
 * @todo Add "actions" to messages (i.e. buttons)
 * @todo Match the naming convention of Bootstrap Alerts
 */

namespace Nails\Common\Service;

use Nails\Auth;
use Nails\Factory;

/**
 * Class UserFeedback
 *
 * @package Nails\Common\Service
 */
class UserFeedback
{
    private $sSessionKey;
    private $oSession;
    private $aMessages;

    // --------------------------------------------------------------------------

    /**
     * Construct the class
     */
    public function __construct()
    {
        $this->sSessionKey = 'NailsUserFeedback';
        $this->oSession    = Factory::service('Session', Auth\Constants::MODULE_SLUG);
        $this->aMessages   = $this->oSession->getFlashData($this->sSessionKey) ?: array();
    }

    // --------------------------------------------------------------------------

    /**
     * Set a feedback message
     * @param  string $sType    The type of message to set
     * @param  string $sMessage The message to set
     * @return Object
     */
    public function set($sType, $sMessage)
    {
        $sType    = strtoupper(trim($sType));
        $sMessage = trim($sMessage);

        $this->aMessages[$sType] = $sMessage;

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Persist the messages until the next page load
     * @return object
     */
    public function persist()
    {
        $this->oSession->setFlashData($this->sSessionKey, $this->aMessages);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Return a feedack message
     * @param  string $sType The type of feedback to return
     * @return string
     */
    public function get($sType)
    {
        $sType = strtoupper(trim($sType));

        if (!empty($this->aMessages[$sType])) {

            return $this->aMessages[$sType];

        } else {

            return '';
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Clear feedback messages
     * @param  string $sType The type of feedback to clear
     * @return
     */
    public function clear($sType = '')
    {
        if (empty($sType)) {

            $this->aMessages[$sType] = array();

        } else {

            $this->aMessages[$sType] = '';
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "success" feedback message
     * @param  string $sMessage The message to set
     * @return Object
     */
    public function success($sMessage)
    {
        return $this->set('success', $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "success" feedback message
     * @return string
     */
    public function getSuccess()
    {
        return $this->get('success');
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "positive" feedback message
     * @param  string $sMessage The message to set
     * @return Object
     */
    public function positive($sMessage)
    {
        return $this->set('positive', $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "positive" feedback message
     * @return string
     */
    public function getPositive()
    {
        return $this->get('positive');
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "error" feedback message
     * @param  string $sMessage The message to set
     * @return Object
     */
    public function error($sMessage)
    {
        return $this->set('error', $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "error" feedback message
     * @return string
     */
    public function getError()
    {
        return $this->get('error');
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "negative" feedback message
     * @param  string $sMessage The message to set
     * @return Object
     */
    public function negative($sMessage)
    {
        return $this->set('negative', $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "negative" feedback message
     * @return string
     */
    public function getNegative()
    {
        return $this->get('negative');
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "message" feedback message
     * @deprecated
     * @param  string $sMessage The message to set
     * @return Object
     */
    public function message($sMessage)
    {
        return $this->set('message', $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "message" feedback message
     * @deprecated
     * @return string
     */
    public function getMessage()
    {
        return $this->get('message');
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "warning" feedback message
     * @param  string $sMessage The message to set
     * @return Object
     */
    public function warning($sMessage)
    {
        return $this->set('warning', $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "warning" feedback message
     * @return string
     */
    public function getWarning()
    {
        return $this->get('message');
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "notice" feedback message
     * @deprecated
     * @param  string $sMessage The message to set
     * @return Object
     */
    public function notice($sMessage)
    {
        return $this->set('notice', $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "notice" feedback message
     * @deprecated
     * @return string
     */
    public function getNotice()
    {
        return $this->get('notice');
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "info" feedback message
     * @param  string $sMessage The message to set
     * @return Object
     */
    public function info($sMessage)
    {
        return $this->set('info', $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "notice" feedback message
     * @return string
     */
    public function getInfo()
    {
        return $this->get('info');
    }
}
