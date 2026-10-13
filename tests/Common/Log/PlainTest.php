<?php

namespace Tests\Common\Log;

use Nails\Common\Log\Plain;
use PHPUnit\Framework\TestCase;

class PlainTest extends TestCase
{
    public function test_ansi_codes_and_banner_rules_are_removed(): void
    {
        self::assertSame('Housekeeping: Run', Plain::line("\x1b[32mHousekeeping: Run\x1b[39m"));
        self::assertSame('', Plain::line('-----------------'));
        self::assertSame('', Plain::line('   '));
    }

    public function test_audit_lines_are_kept(): void
    {
        $sLine = 'DELETE id=42 token=abc expires=2026-01-01 00:00:00';

        self::assertSame($sLine, Plain::line($sLine));
        self::assertSame('UNLINK path=/tmp/a', Plain::line('UNLINK path=/tmp/a'));
        self::assertSame('ARCHIVE file=cron-2026-01-01.php', Plain::line('ARCHIVE file=cron-2026-01-01.php'));
    }
}
