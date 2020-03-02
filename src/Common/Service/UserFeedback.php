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
 * @todo        Add "actions" to messages (i.e. buttons)
 * @todo        Match the naming convention of Bootstrap Alerts
 */

namespace Nails\Common\Service;

use Nails\Common\Exception\FactoryException;
use Nails\Factory;

/**
 * Class UserFeedback
 *
 * @package Nails\Common\Service
 */
class UserFeedback
{
    /** @var string */
    private $sSessionKey;

    /** @var Session */
    private $oSession;

    /** @var array */
    private $aMessages;

    // --------------------------------------------------------------------------

    /**
     * UserFeedback constructor.
     *
     * @throws FactoryException
     */
    public function __construct()
    {
        $this->sSessionKey = 'NailsUserFeedback';
        $this->oSession    = Factory::service('Session');
        $this->aMessages   = $this->oSession->getFlashData($this->sSessionKey) ?: [];
    }

    // --------------------------------------------------------------------------

    /**
     * Set a feedback message
     *
     * @param string $sType    The type of message to set
     * @param string $sMessage The message to set
     *
     * @return $this
     */
    public function set($sType, $sMessage): self
    {
        $sType    = strtoupper(trim($sType));
        $sMessage = trim($sMessage);

        $this->aMessages[$sType] = $sMessage;

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Persist the messages until the next page load
     *
     * @return $this
     */
    public function persist(): self
    {
        $this->oSession->setFlashData(
            $this->sSessionKey,
            $this->aMessages
        );
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Return a feedack message
     *
     * @param string $sType The type of feedback to return
     *
     * @return string
     */
    public function get($sType): string
    {
        $sType = strtoupper(trim($sType));
        return $this->aMessages[$sType] ?? '';
    }

    // --------------------------------------------------------------------------

    /**
     * Clear feedback messages
     *
     * @param string $sType The type of feedback to clear
     *
     * @return $this
     */
    public function clear($sType = ''): self
    {
        if (empty($sType)) {
            $this->aMessages[$sType] = [];
        } else {
            $this->aMessages[$sType] = '';
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "success" feedback message
     *
     * @param string $sMessage The message to set
     *
     * @return $this
     */
    public function success($sMessage): self
    {
        return $this->set('success', $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "success" feedback message
     *
     * @return string
     */
    public function getSuccess(): string
    {
        return $this->get('success');
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "positive" feedback message
     *
     * @param string $sMessage The message to set
     *
     * @return $this
     */
    public function positive($sMessage): self
    {
        return $this->set('positive', $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "positive" feedback message
     *
     * @return string
     */
    public function getPositive(): string
    {
        return $this->get('positive');
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "error" feedback message
     *
     * @param string $sMessage The message to set
     *
     * @return $this
     */
    public function error($sMessage): self
    {
        return $this->set('error', $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "error" feedback message
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->get('error');
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "negative" feedback message
     *
     * @param string $sMessage The message to set
     *
     * @return $this
     */
    public function negative($sMessage): self
    {
        return $this->set('negative', $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "negative" feedback message
     *
     * @return string
     */
    public function getNegative(): string
    {
        return $this->get('negative');
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "message" feedback message
     *
     * @param string $sMessage The message to set
     *
     * @return $this
     * @deprecated
     */
    public function message($sMessage): self
    {
        return $this->set('message', $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "message" feedback message
     *
     * @return string
     * @deprecated
     */
    public function getMessage(): string
    {
        return $this->get('message');
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "warning" feedback message
     *
     * @param string $sMessage The message to set
     *
     * @return $this
     */
    public function warning($sMessage): self
    {
        return $this->set('warning', $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "warning" feedback message
     *
     * @return string
     */
    public function getWarning(): string
    {
        return $this->get('message');
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "notice" feedback message
     *
     * @param string $sMessage The message to set
     *
     * @return $this
     * @deprecated
     */
    public function notice($sMessage): self
    {
        return $this->set('notice', $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "notice" feedback message
     *
     * @return string
     * @deprecated
     */
    public function getNotice(): string
    {
        return $this->get('notice');
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "info" feedback message
     *
     * @param string $sMessage The message to set
     *
     * @return $this
     */
    public function info($sMessage): self
    {
        return $this->set('info', $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "notice" feedback message
     *
     * @return string
     */
    public function getInfo(): string
    {
        return $this->get('info');
    }
}
