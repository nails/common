<?php

namespace Tests;

use Nails\Bootstrap;
use PHPUnit\Framework\TestCase;

class BootstrapTest extends TestCase
{
    /**
     * @covers \Nails\Bootstrap::getControllerData
     */
    public function test_controller_data_is_an_array(): void
    {
        $this->assertIsArray(Bootstrap::getControllerData());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Bootstrap::getControllerData
     */
    public function test_can_set_controller_data(): void
    {
        Bootstrap::setControllerData('foo', 'bar');
        $this->assertEquals(['foo' => 'bar'], Bootstrap::getControllerData());
    }
}
