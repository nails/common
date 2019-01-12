<?php

namespace Tests\Functions;

use Nails\Functions;
use PHPUnit\Framework\TestCase;

class GetFromArrayTest extends TestCase
{
    private $aTestArray = [
        'foo'  => 'bar',
        'fizz' => 'buzz',
    ];

    // --------------------------------------------------------------------------

    public function test_getfromarray_valid_key()
    {
        $this->assertEquals(
            $this->aTestArray['foo'],
            Functions::getFromArray('foo', $this->aTestArray)
        );
    }

    // --------------------------------------------------------------------------

    public function test_getfromarray_invalid_key()
    {
        $this->assertEquals(
            null,
            Functions::getFromArray('invalid', $this->aTestArray)
        );
    }

    // --------------------------------------------------------------------------

    public function test_getfromarray_cascading_keys()
    {
        $this->assertEquals(
            $this->aTestArray['fizz'],
            Functions::getFromArray(['invalid', 'fizz'], $this->aTestArray)
        );
    }

    // --------------------------------------------------------------------------

    public function test_getfromarray_default_value()
    {
        $this->assertEquals(
            'default',
            Functions::getFromArray('invalid', $this->aTestArray, 'default')
        );
    }
}
