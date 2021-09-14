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
use Nails\Common\Factory\Service\UserFeedback\Message;
use Nails\Factory;

/**
 * Class UserFeedback
 *
 * @package Nails\Common\Service
 */
class UserFeedback
{
    const SESSION_KEY = 'NailsUserFeedback';

    const TYPE_SUCCESS = 'SUCCESS';
    const TYPE_ERROR   = 'ERROR';
    const TYPE_WARNING = 'WARNING';
    const TYPE_INFO    = 'INFO';

    //  Deprecated
    const TYPE_POSITIVE = 'POSITIVE';
    const TYPE_NEGATIVE = 'NEGATIVE';
    const TYPE_MESSAGE  = 'MESSAGE';
    const TYPE_NOTICE   = 'NOTICE';

    // --------------------------------------------------------------------------

    /** @var string */
    private $sSessionKey;

    /** @var Session */
    private $oSession;

    /** @var string[]|null[] */
    private $aMessages;

    // --------------------------------------------------------------------------

    /**
     * UserFeedback constructor.
     *
     * @param Session|null $oSession The session service
     *
     * @throws FactoryException
     */
    public function __construct(Session $oSession = null)
    {
        $this->oSession  = $oSession ?? Factory::service('Session');
        $this->aMessages = json_decode($this->oSession->getFlashData(static::SESSION_KEY), true);

        if (!is_array($this->aMessages)) {
            $this->aMessages = [];
        }

        //  Pre-populate with flashdata
        //  @todo (Pablo - 2021-05-27) - Remove the fallback to the session
        foreach ($this->getTypes() as $sType) {
            $sValue = $this->oSession->getFlashData($sType);
            try {
                if (!empty($sValue)) {
                    $this->set($sType, $sValue);
                }
            } catch (\Exception $e) {
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Set a feedback message
     *
     * @param string $sType    The type of message to set
     * @param string $sMessage The message to set
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function set(string $sType, string $sMessage): self
    {
        $sType    = strtoupper(trim($sType));
        $sMessage = trim($sMessage);

        $this->validateType($sType);

        $this->aMessages[$sType] = $sMessage;

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Validates a given type
     *
     * @param string $sType The type to validate
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    protected function validateType(string $sType): self
    {
        if (!in_array($sType, $this->getTypes())) {
            throw new \InvalidArgumentException(sprintf(
                '`type` must be one of: %s. "%s" received.',
                implode(', ', $this->getTypes()),
                $sType
            ));
        }

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
            static::SESSION_KEY,
            json_encode($this->aMessages)
        );

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Return a feedack message
     *
     * @param string $sType The type of feedback to return
     *
     * @return Message
     * @throws \InvalidArgumentException
     */
    public function get(string $sType): Message
    {
        $sType = strtoupper(trim($sType));
        $this->validateType($sType);

        /** @var Message $oMessage */
        $oMessage = Factory::factory('UserFeedbackMessage', null, $this, $sType);

        return $oMessage;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the current value for a type
     *
     * @param string $sType
     *
     * @return string|null
     * @throws \InvalidArgumentException
     */
    public function getValue(string $sType): ?string
    {
        $sType = strtoupper(trim($sType));
        $this->validateType($sType);

        return $this->aMessages[$sType] ?? null;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the valid types of feeback
     *
     * @return string[]
     */
    public function getTypes(): array
    {
        return [
            static::TYPE_SUCCESS,
            static::TYPE_ERROR,
            static::TYPE_WARNING,
            static::TYPE_INFO,
            static::TYPE_POSITIVE,
            static::TYPE_NEGATIVE,
            static::TYPE_MESSAGE,
            static::TYPE_NOTICE,
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the stored messages
     *
     * @return string[]
     */
    public function getAll(): array
    {
        return $this->aMessages;
    }

    // --------------------------------------------------------------------------

    /**
     * Clear feedback messages
     *
     * @param string|null $sType The type of feedback to clear, clears everything if empty
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function clear(string $sType = null): self
    {
        if (empty($sType)) {
            $this->aMessages = [];
        } else {
            $this->validateType($sType);
            $this->aMessages[$sType] = null;
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
     * @throws \InvalidArgumentException
     */
    public function success(string $sMessage): self
    {
        return $this->set(static::TYPE_SUCCESS, $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "success" feedback message
     *
     * @return Message
     * @throws \InvalidArgumentException
     */
    public function getSuccess(): Message
    {
        return $this->get(static::TYPE_SUCCESS);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "error" feedback message
     *
     * @param string $sMessage The message to set
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function error(string $sMessage): self
    {
        return $this->set(static::TYPE_ERROR, $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "error" feedback message
     *
     * @return Message
     * @throws \InvalidArgumentException
     */
    public function getError(): Message
    {
        return $this->get(static::TYPE_ERROR);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "warning" feedback message
     *
     * @param string $sMessage The message to set
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function warning(string $sMessage): self
    {
        return $this->set(static::TYPE_WARNING, $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "warning" feedback message
     *
     * @return Message
     * @throws \InvalidArgumentException
     */
    public function getWarning(): Message
    {
        return $this->get(static::TYPE_WARNING);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "info" feedback message
     *
     * @param string $sMessage The message to set
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function info(string $sMessage): self
    {
        return $this->set(static::TYPE_INFO, $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "info" feedback message
     *
     * @return Message
     * @throws \InvalidArgumentException
     */
    public function getInfo(): Message
    {
        return $this->get(static::TYPE_INFO);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "positive" feedback message
     *
     * @param string $sMessage The message to set
     *
     * @return $this
     * @throws \InvalidArgumentException
     * @deprecated
     */
    public function positive(string $sMessage): self
    {
        return $this->set(static::TYPE_POSITIVE, $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "positive" feedback message
     *
     * @return Message
     * @throws \InvalidArgumentException
     * @deprecated
     */
    public function getPositive(): Message
    {
        return $this->get(static::TYPE_POSITIVE);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "negative" feedback message
     *
     * @param string $sMessage The message to set
     *
     * @return $this
     * @throws \InvalidArgumentException
     * @deprecated
     */
    public function negative(string $sMessage): self
    {
        return $this->set(static::TYPE_NEGATIVE, $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "negative" feedback message
     *
     * @return Message
     * @throws \InvalidArgumentException
     * @deprecated
     */
    public function getNegative(): Message
    {
        return $this->get(static::TYPE_NEGATIVE);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "message" feedback message
     *
     * @param string $sMessage The message to set
     *
     * @return $this
     * @throws \InvalidArgumentException
     * @deprecated
     */
    public function message(string $sMessage): self
    {
        return $this->set(static::TYPE_MESSAGE, $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "message" feedback message
     *
     * @return Message
     * @throws \InvalidArgumentException
     * @deprecated
     */
    public function getMessage(): Message
    {
        return $this->get(static::TYPE_MESSAGE);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a "notice" feedback message
     *
     * @param string $sMessage The message to set
     *
     * @return $this
     * @throws \InvalidArgumentException
     * @deprecated
     */
    public function notice(string $sMessage): self
    {
        return $this->set(static::TYPE_NOTICE, $sMessage);
    }

    // --------------------------------------------------------------------------

    /**
     * Return the "notice" feedback message
     *
     * @return Message
     * @throws \InvalidArgumentException
     * @deprecated
     */
    public function getNotice(): Message
    {
        return $this->get(static::TYPE_NOTICE);
    }
}
