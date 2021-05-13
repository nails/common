<?php

namespace Tests\Commnon\Helper\Timecode;

use Nails\Common\Exception\ValidationException;
use Nails\Common\Helper\Timecode;
use PHPUnit\Framework\TestCase;

class ToSecondsTest extends TestCase
{
    /**
     * @covers \Nails\Common\Helper\Timecode::toSeconds()
     */
    public function test_valid_string_returns_seconds(): void
    {
        $this->assertEquals(
            90,
            Timecode::toSeconds('00:01:30')
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Timecode::toSeconds()
     */
    public function test_whitespace_is_ignored(): void
    {
        $this->assertEquals(
            Timecode::toSeconds('00:01:30'),
            Timecode::toSeconds(' 00:01:30 ')
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Timecode::toSeconds()
     */
    public function test_invalid_string_throws_exception_missing_hour_segment(): void
    {
        $this->expectException(ValidationException::class);
        Timecode::toSeconds('01:30');
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Timecode::toSeconds()
     */
    public function test_invalid_string_throws_exception_missing_minute_segment(): void
    {
        $this->expectException(ValidationException::class);
        Timecode::toSeconds('30');
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Timecode::toSeconds()
     */
    public function test_invalid_string_throws_exception_seconds_out_of_bounds(): void
    {
        $this->expectException(ValidationException::class);
        Timecode::toSeconds('00:00:72');
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Timecode::toSeconds()
     */
    public function test_invalid_string_throws_exception_minute_out_of_bounds(): void
    {
        $this->expectException(ValidationException::class);
        Timecode::toSeconds('01:61:03');
    }
}
