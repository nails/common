<?php

/**
 * The class provides a centralsied, semantic way of giving the user feedback
 *
 * @package     Nails
 * @subpackage  module-asset
 * @category    Library
 * @author      Nails Dev Team
 * @link
 *
 * @todo Add "actions" to messages (i.e. buttons)
 * @todo Match the naming convention of Bootstrap Alerts
 */

namespace Nails\Common\Library;

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
        $this->oSession    = \Nails\Factory::service('Session');
        $this->aMessages   = $this->oSession->flashdata($this->sSessionKey) ?: array();
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
        $this->oSession->set_flashdata($this->sSessionKey, $this->aMessages);
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
     * Set a "message" feedback message
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
     * @return string
     */
    public function getMessage()
    {
        return $this->get('message');
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "notice" feedback message
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
     * @return string
     */
    public function getNotice()
    {
        return $this->get('notice');
    }
}
