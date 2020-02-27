<?php

namespace Tests\Common\Helper\File;

use Nails\Common\Helper\File;
use PHPUnit\Framework\TestCase;

/**
 * Class FileExistsCsTest
 *
 * @package Tests\Common\Helper\File
 */
class FileExistsCsTest extends TestCase
{
    /**
     * The temporary directory to use
     *
     * @var string
     */
    public static $sRoot;
    public static $sDir1;
    public static $sDir1File1;
    public static $sDir1Child1;
    public static $sDir1Child1File1;
    public static $sDir1Child1File2;
    public static $sDir2;
    public static $sDir2File1;

    // --------------------------------------------------------------------------

    /**
     * Set up the test class
     */
    public static function setUpBeforeClass(): void
    {
        //  Load the helper
        require_once dirname(__FILE__) . '/../../../../helpers/file.php';

        //  Create a known directory listing
        static::$sRoot            = sys_get_temp_dir() . DIRECTORY_SEPARATOR . (int) microtime(true);
        static::$sDir1            = implode(DIRECTORY_SEPARATOR, [static::$sRoot, 'Dir 1']);
        static::$sDir1File1       = implode(DIRECTORY_SEPARATOR, [static::$sDir1, 'File1.txt']);
        static::$sDir1Child1      = implode(DIRECTORY_SEPARATOR, [static::$sDir1, 'Child Dir 1']);
        static::$sDir1Child1File1 = implode(DIRECTORY_SEPARATOR, [static::$sDir1Child1, 'File1.txt']);

        mkdir(static::$sRoot);
        mkdir(static::$sDir1);
        file_put_contents(static::$sDir1File1, time());
        mkdir(static::$sDir1Child1);
        file_put_contents(static::$sDir1Child1File1, time());
    }

    // --------------------------------------------------------------------------

    /**
     * Tear down the test class
     */
    public static function tearDownAfterClass(): void
    {
        unlink(static::$sDir1File1);
        unlink(static::$sDir1Child1File1);
        rmdir(static::$sDir1Child1);
        rmdir(static::$sDir1);
        rmdir(static::$sRoot);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers ::fileExistsCS
     */
    public function test_helper_method_exists()
    {
        $this->assertTrue(function_exists('fileExistsCS'));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers File::fileExistsCS
     */
    public function test_returns_false_for_empty_filename()
    {
        $this->assertFalse(File::fileExistsCS(''));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers File::fileExistsCS
     */
    public function test_returns_true_for_valid_file()
    {
        $this->assertTrue(File::fileExistsCS(static::$sDir1File1));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers File::fileExistsCS
     */
    public function test_returns_false_for_invalid_file()
    {
        $this->assertFalse(File::fileExistsCS(static::$sDir1 . DIRECTORY_SEPARATOR . 'invalid_file.txt'));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers File::fileExistsCS
     */
    public function test_returns_false_for_valid_file_incorrect_case()
    {
        $this->assertFalse(File::fileExistsCS(strtoupper(static::$sDir1File1)));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers File::fileExistsCS
     */
    public function test_returns_true_for_valid_directory()
    {
        $this->assertTrue(File::fileExistsCS(static::$sDir1));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers File::fileExistsCS
     */
    public function test_returns_false_for_valid_directory_incorrect_case()
    {
        $this->assertFalse(File::fileExistsCS(strtoupper(static::$sDir1)));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers File::fileExistsCS
     */
    public function test_returns_false_for_invalid_directory()
    {
        $this->assertFalse(File::fileExistsCS(static::$sDir1 . DIRECTORY_SEPARATOR . 'Invalid Dir'));
    }
}
