<?php

namespace Tests\Common\Service;

use Nails\Common\Traits\TestHelper;
use PHPUnit\Framework\TestCase;
use Nails\Common\Service\Input;

//  @todo (Pablo - 2019-01-31) - Write test: test_fetch_from_array_xss_cleans

class InputTest extends TestCase
{
    use TestHelper;

    // --------------------------------------------------------------------------

    /**
     * The array to use for testing
     *
     * @var array
     */
    const TEST_ARRAY = [
        'foo'      => 'bar',
        'fizz'     => 'buzz',
        'FizzBuzz' => 'FooBar',
    ];

    // --------------------------------------------------------------------------

    /**
     * @covers Input::getItemsFromArray
     * @throws \ReflectionException
     */
    public function test_can_fetch_single_from_array(): void
    {
        $this->assertEquals(
            'bar',
            $this->getItemsFromArray('foo')
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers Input::getItemsFromArray
     * @throws \ReflectionException
     */
    public function test_can_fetch_multiple_from_array(): void
    {
        $this->assertEquals(
            ['foo' => 'bar', 'fizz' => 'buzz'],
            $this->getItemsFromArray(['foo', 'fizz'])
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers Input::getItemsFromArray
     * @throws \ReflectionException
     */
    public function test_fetch_from_array_maintains_key_casing(): void
    {
        $mResult = $this->getItemsFromArray(['foo', 'FizzBuzz']);
        $this->assertEquals(true, array_key_exists('foo', $mResult));
        $this->assertEquals(true, array_key_exists('FizzBuzz', $mResult));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers Input::getItemsFromArray
     * @throws \ReflectionException
     */
    public function test_fetch_from_array_is_case_insensitive(): void
    {
        $mResult = $this->getItemsFromArray(['foo', 'fizzbuzz']);
        $this->assertEquals(true, array_key_exists('foo', $mResult));
        $this->assertEquals(true, array_key_exists('FizzBuzz', $mResult));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers Input::getItemsFromArray
     * @throws \ReflectionException
     */
    public function test_fetch_from_array_only_returns_valid_keys(): void
    {
        $this->assertEquals(
            ['foo' => 'bar', 'fizz' => 'buzz'],
            $this->getItemsFromArray(['foo', 'fizz', 'invalid'])
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Call Input::getItemsFromArray
     *
     * @param string|array $mKeys The key(s) to fetch
     *
     * @return array|mixed
     * @throws \ReflectionException
     */
    private function getItemsFromArray($mKeys)
    {
        return static::executePrivateMethod(
            '\Nails\Common\Service\Input',
            'getItemsFromArray',
            [$mKeys, false, static::TEST_ARRAY,]
        );
    }
}
