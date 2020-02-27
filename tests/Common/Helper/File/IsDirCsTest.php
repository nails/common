<?php

namespace Tests\Common\Helper\File;

use Nails\Common\Helper\File;
use PHPUnit\Framework\TestCase;

/**
 * Class IsDirCsTest
 *
 * @package Tests\Common\Helper\File
 */
class IsDirCsTest extends TestCase
{
    /**
     * Set up the test class
     */
    public static function setUpBeforeClass(): void
    {
        //  Load the helper
        require_once dirname(__FILE__) . '/../../../../helpers/file.php';

        //  Create a known directory listing
        FileExistsCsTest::setUpBeforeClass();
    }

    // --------------------------------------------------------------------------

    /**
     * Tear down the test class
     */
    public static function tearDownAfterClass(): void
    {
        FileExistsCsTest::tearDownAfterClass();
    }

    // --------------------------------------------------------------------------

    /**
     * @covers ::isDirCS
     */
    public function test_helper_method_exists()
    {
        $this->assertTrue(function_exists('isDirCS'));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers File::isDirCS
     */
    public function test_returns_false_for_empty_filename()
    {
        $this->assertFalse(File::isDirCS(''));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers File::isDirCS
     */
    public function test_returns_true_for_valid_dir()
    {
        $this->assertTrue(File::isDirCS(FileExistsCsTest::$sDir1));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers File::isDirCS
     */
    public function test_returns_false_for_valid_dir_incorrect_case()
    {
        $this->assertFalse(File::isDirCS(strtoupper(FileExistsCsTest::$sDir1)));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers File::isDirCS
     */
    public function test_returns_false_for_invalid_dir()
    {
        $this->assertFalse(File::isDirCS(FileExistsCsTest::$sDir1 . 'Invalid Dir'));
    }
}
