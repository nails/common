<?php

namespace Nails\Common\Factory\Service\UserFeedback;

use Nails\Common\Service\UserFeedback;

/**
 * Class Message
 *
 * @package Nails\Common\Factory\Service\UserFeedback
 */
class Message
{
    protected UserFeedback $oUserFeedback;
    protected string       $sType;

    // --------------------------------------------------------------------------

    /**
     * UserFeedback constructor.
     *
     * @param UserFeedback $oUserFeedback An instance of the UserFeedback service
     * @param string       $sType         The type of message to return
     */
    public function __construct(UserFeedback $oUserFeedback, string $sType)
    {
        $this->oUserFeedback = $oUserFeedback;
        $this->sType         = $sType;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the current value of the message
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->oUserFeedback->getValue($this->sType) ?? '';
    }
}
