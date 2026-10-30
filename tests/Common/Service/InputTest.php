<?php

namespace Tests\Common\Service;

use Nails\Common\Exception\Input\InvalidEncodingException;
use Nails\Common\Exception\InvalidRequestException;
use Nails\Common\Service\HttpCodes;
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
     * Overlong-encoded lead bytes; not valid UTF-8 under a strict check
     *
     * @var string
     */
    const INVALID_UTF8 = "\xC0\xA7\xC0\xA2";

    // --------------------------------------------------------------------------

    protected function setUp(): void
    {
        parent::setUp();
        $_GET    = [];
        $_POST   = [];
        $_COOKIE = [];
    }

    // --------------------------------------------------------------------------

    protected function tearDown(): void
    {
        $_GET    = [];
        $_POST   = [];
        $_COOKIE = [];
        parent::tearDown();
    }

    // --------------------------------------------------------------------------

    /**
     * @covers Input::hasInvalidEncoding
     */
    public function test_invalid_encoding_in_query_value_is_detected(): void
    {
        $_GET = ['forward_to' => static::INVALID_UTF8];
        $this->assertTrue(Input::hasInvalidEncoding());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers Input::hasInvalidEncoding
     */
    public function test_invalid_encoding_in_query_key_is_detected(): void
    {
        $_GET = [static::INVALID_UTF8 => 'value'];
        $this->assertTrue(Input::hasInvalidEncoding());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers Input::hasInvalidEncoding
     */
    public function test_invalid_encoding_in_post_field_is_detected(): void
    {
        $_POST = ['field' => static::INVALID_UTF8];
        $this->assertTrue(Input::hasInvalidEncoding());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers Input::hasInvalidEncoding
     */
    public function test_invalid_encoding_in_cookie_is_detected(): void
    {
        $_COOKIE = ['session' => static::INVALID_UTF8];
        $this->assertTrue(Input::hasInvalidEncoding());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers Input::hasInvalidEncoding
     */
    public function test_valid_multibyte_string_is_accepted(): void
    {
        $_GET  = ['q' => 'héllo wörld 日本語'];
        $_POST = ['q' => 'héllo wörld 日本語'];
        $this->assertFalse(Input::hasInvalidEncoding());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers Input::hasInvalidEncoding
     */
    public function test_post_field_with_newline_is_accepted(): void
    {
        $_POST = ['message' => "line one\nline two\ttabbed"];
        $this->assertFalse(Input::hasInvalidEncoding());
    }

    // --------------------------------------------------------------------------

    /**
     * CLI requests must never be rejected, even with invalid encoding present.
     * This test runs under the CLI SAPI (as PHPUnit always does), so it
     * exercises the real Input::isCli() short-circuit, not a mock.
     *
     * @covers Input::validateEncoding
     */
    public function test_cli_request_is_not_rejected(): void
    {
        $_GET = ['forward_to' => static::INVALID_UTF8];

        $this->assertTrue(Input::isCli());

        //  Would throw InvalidEncodingException if this ever ran as a
        //  non-CLI request; must return normally here.
        Input::validateEncoding();

        $this->assertTrue(true);
    }

    // --------------------------------------------------------------------------

    /**
     * assertValidEncoding() is the CLI-unaware half of validateEncoding();
     * exercised directly here since isCli() is always true under PHPUnit,
     * which would otherwise mask this behaviour.
     *
     * @covers Input::assertValidEncoding
     * @throws \ReflectionException
     */
    public function test_assert_valid_encoding_throws_for_invalid_cookie(): void
    {
        $_COOKIE = ['session' => static::INVALID_UTF8];

        $this->expectException(InvalidEncodingException::class);
        $this->expectExceptionCode(HttpCodes::STATUS_BAD_REQUEST);

        static::executePrivateMethod(
            '\Nails\Common\Service\Input',
            'assertValidEncoding',
            []
        );
    }

    // --------------------------------------------------------------------------

    /**
     * InvalidEncodingException must stay part of the InvalidRequestException
     * family, as that's what ErrorHandler::handleException() switches on to
     * halt early instead of reporting a generic fatal error.
     */
    public function test_invalid_encoding_exception_is_an_invalid_request_exception(): void
    {
        $this->assertInstanceOf(
            InvalidRequestException::class,
            new InvalidEncodingException()
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers Input::assertValidEncoding
     * @throws \ReflectionException
     */
    public function test_assert_valid_encoding_accepts_valid_input(): void
    {
        $_GET = ['q' => 'héllo wörld 日本語'];

        static::executePrivateMethod(
            '\Nails\Common\Service\Input',
            'assertValidEncoding',
            []
        );

        $this->assertTrue(true);
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
