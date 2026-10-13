<?php

namespace Nails\Common\Log;

class Plain
{
    /**
     * Strip terminal decoration so a log entry is one readable line.
     * An empty result means the line was only decoration and should not be written.
     */
    public static function line(string $sMessage): string
    {
        $sMessage = preg_replace("/\x1b\[[0-9;?]*[A-Za-z]/", '', $sMessage) ?? $sMessage;
        $sMessage = trim($sMessage);

        if ($sMessage === '' || preg_match('/^-+$/', $sMessage) === 1) {
            return '';
        }

        return $sMessage;
    }
}
