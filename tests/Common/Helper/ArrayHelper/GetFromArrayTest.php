<?php

namespace Tests\Commnon\Helper\ArrayHelper;

use Nails\Common\Helper\ArrayHelper;
use PHPUnit\Framework\TestCase;

class GetFromArrayTest extends TestCase
{
    /**
     * Test data
     *
     * @var array
     */
    private $aTestArray = [
        'foo'  => 'bar',
        'fizz' => 'buzz',
    ];

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\ArrayHelper::get()
     */
    public function test_getfromarray_valid_key(): void
    {
        $this->assertEquals(
            $this->aTestArray['foo'],
            ArrayHelper::get('foo', $this->aTestArray)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\ArrayHelper::get()
     */
    public function test_getfromarray_invalid_key(): void
    {
        $this->assertEquals(
            null,
            ArrayHelper::get('invalid', $this->aTestArray)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\ArrayHelper::get()
     */
    public function test_getfromarray_cascading_keys(): void
    {
        $this->assertEquals(
            $this->aTestArray['fizz'],
            ArrayHelper::get(['invalid', 'fizz'], $this->aTestArray)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\ArrayHelper::get()
     */
    public function test_getfromarray_default_value(): void
    {
        $this->assertEquals(
            'default',
            ArrayHelper::get('invalid', $this->aTestArray, 'default')
        );
    }
}
