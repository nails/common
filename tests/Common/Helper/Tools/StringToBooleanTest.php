<?php

namespace Tests\Common\Helper;

use Nails\Common\Helper\Tools;
use PHPUnit\Framework\TestCase;

class StringToBooleanTest extends TestCase
{
    /**
     * Test string: true
     *
     * @var string
     */
    const TEST_TRUE = 'true';

    /**
     * Test string: false
     *
     * @var string
     */
    const TEST_FALSE = 'false';

    /**
     * Test string: 0
     *
     * @var string
     */
    const TEST_ZERO = '0';

    /**
     * Test string: 1
     *
     * @var string
     */
    const TEST_ONE = '1';

    /**
     * Test string: <empty>
     *
     * @var string
     */
    const TEST_EMPTY = '';

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Tools::stringToBoolean()
     */
    public function test_string_true_is_true(): void
    {
        $this->assertTrue(
            Tools::stringToBoolean(static::TEST_TRUE)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Tools::stringToBoolean()
     */
    public function test_string_one_is_true(): void
    {
        $this->assertTrue(
            Tools::stringToBoolean(static::TEST_ONE)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Tools::stringToBoolean()
     */
    public function test_string_false_is_false(): void
    {
        $this->assertFalse(
            Tools::stringToBoolean(static::TEST_FALSE)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Tools::stringToBoolean()
     */
    public function test_string_zero_is_false(): void
    {
        $this->assertFalse(
            Tools::stringToBoolean(static::TEST_ZERO)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Tools::stringToBoolean()
     */
    public function test_string_empty_is_false(): void
    {
        $this->assertFalse(
            Tools::stringToBoolean(static::TEST_EMPTY)
        );
    }
}
