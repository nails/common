<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Helper\Strings;
use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;

/**
 * Rule: `valid_emails` — a list of email addresses (separated by commas, semicolons or new lines)
 */
class ValidEmails extends AbstractRule
{
    public const NAME            = 'valid_emails';
    public const DEFAULT_MESSAGE = 'The {field} field must contain only valid email addresses.';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        $sValue = $this->asString($mValue);
        if ($sValue === null) {
            return false;
        }

        foreach (Strings::toArray($sValue) as $sEmail) {
            if (!filter_var($sEmail, FILTER_VALIDATE_EMAIL)) {
                return false;
            }
        }

        return true;
    }
}
