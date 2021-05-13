<?php

namespace Tests\Commnon\Helper\Timecode;

use Nails\Common\Exception\ValidationException;
use Nails\Common\Helper\Timecode;
use PHPUnit\Framework\TestCase;

class ToTimecodeTest extends TestCase
{
    /**
     * @covers \Nails\Common\Helper\Timecode::toTimecode()
     */
    public function test_positive_integer_returns_timecode_seconds(): void
    {
        $this->assertEquals(
            '00:00:03',
            Timecode::toTimecode(3)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Timecode::toTimecode()
     */
    public function test_positive_integer_returns_timecode_minutes(): void
    {
        $this->assertEquals(
            '00:02:03',
            Timecode::toTimecode(123)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Timecode::toTimecode()
     */
    public function test_positive_integer_returns_timecode_hours(): void
    {
        $this->assertEquals(
            '01:02:03',
            Timecode::toTimecode(3723)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Timecode::toTimecode()
     */
    public function test_custom_separator_can_be_used(): void
    {
        $this->assertEquals(
            '01-02-03',
            Timecode::toTimecode(3723, '-')
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Timecode::toTimecode()
     */
    public function test_negative_integer_throws_exception(): void
    {
        $this->expectException(ValidationException::class);
        Timecode::toSeconds(-1);
    }
}
