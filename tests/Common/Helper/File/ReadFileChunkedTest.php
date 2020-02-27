<?php

namespace Tests\Common\Helper\File;

use PHPUnit\Framework\TestCase;

/**
 * Class ReadFileChunkedTest
 *
 * @package Tests\Common\Helper\File
 */
class ReadFileChunkedTest extends TestCase
{
    /**
     * Set up the test class
     */
    public static function setUpBeforeClass(): void
    {
        require_once dirname(__FILE__) . '/../../../../helpers/file.php';
    }

    // --------------------------------------------------------------------------

    /**
     * @covers ::readFileChunked
     */
    public function test_helper_method_exists()
    {
        $this->assertTrue(function_exists('readFileChunked'));
    }

    // --------------------------------------------------------------------------

    //  @todo (Pablo - 2020-02-27) - Add more tests
}
