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
     * @covers \Nails\Common\Helper\ArrayHelper::getFromArray()
     */
    public function test_getfromarray_valid_key()
    {
        $this->assertEquals(
            $this->aTestArray['foo'],
            ArrayHelper::getFromArray('foo', $this->aTestArray)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\ArrayHelper::getFromArray()
     */
    public function test_getfromarray_invalid_key()
    {
        $this->assertEquals(
            null,
            ArrayHelper::getFromArray('invalid', $this->aTestArray)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\ArrayHelper::getFromArray()
     */
    public function test_getfromarray_cascading_keys()
    {
        $this->assertEquals(
            $this->aTestArray['fizz'],
            ArrayHelper::getFromArray(['invalid', 'fizz'], $this->aTestArray)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\ArrayHelper::getFromArray()
     */
    public function test_getfromarray_default_value()
    {
        $this->assertEquals(
            'default',
            ArrayHelper::getFromArray('invalid', $this->aTestArray, 'default')
        );
    }
}
