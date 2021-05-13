<?php

namespace Tests\Commnon\Helper\Timecode;

use Nails\Common\Exception\ValidationException;
use Nails\Common\Helper\Timecode;
use PHPUnit\Framework\TestCase;

class IsValidTest extends TestCase
{
    /**
     * @covers \Nails\Common\Helper\Timecode::isValid()
     */
    public function test_valid_string_returns_true(): void
    {
        $this->assertEquals(
            true,
            Timecode::isValid('00:01:30')
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Timecode::isValid()
     */
    public function test_invalid_string_returns_false_missing_hour_segment(): void
    {
        $this->assertEquals(
            false,
            Timecode::isValid('01:30')
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Timecode::isValid()
     */
    public function test_invalid_string_returns_false_missing_minute_segment(): void
    {
        $this->assertEquals(
            false,
            Timecode::isValid('30')
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Timecode::isValid()
     */
    public function test_invalid_string_returns_false_seconds_out_of_bounds(): void
    {
        $this->assertEquals(
            false,
            Timecode::isValid('00:00:72')
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Timecode::isValid()
     */
    public function test_invalid_string_returns_false_minute_out_of_bounds(): void
    {
        $this->assertEquals(
            false,
            Timecode::isValid('01:61:03')
        );
    }
}
