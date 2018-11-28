<?php

namespace Nails\Common\Helper;

use Nails\Bootstrap;
use PHPUnit\Framework\TestCase;

class BootstrapTest extends TestCase
{
    public function test_controller_data_is_an_array()
    {
        $this->assertInternalType('array', Bootstrap::getControllerData());
    }

    // --------------------------------------------------------------------------

    public function test_can_set_controller_data()
    {
        Bootstrap::setControllerData('foo', 'bar');
        $this->assertEquals(['foo' => 'bar'], Bootstrap::getControllerData());
    }
}
