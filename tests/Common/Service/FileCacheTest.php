<?php

namespace Tests\Common\Service;

use Nails\Common\Exception;
use Nails\Common\Helper\Directory;
use Nails\Common\Resource\FileCache\Item;
use Nails\Common\Service\FileCache;
use Nails\Common\Service\FileCache\Driver;
use PHPUnit\Framework\TestCase;

/**
 * Class FileCacheTest
 *
 * @package Tests\Common\Service
 */
class FileCacheTest extends TestCase
{
    /**
     * @var string
     */
    protected static $sDirPrivate;

    /**
     * @var string
     */
    protected static $sDirPublic;


    /**
     * @var FileCache\Driver
     */
    protected static $oCachePrivate;

    /**
     * @var FileCache\Driver\AccessibleByUrl
     */
    protected static $oCachePublic;

    /**
     * @var FileCache
     */
    protected static $oCache;

    // --------------------------------------------------------------------------

    /**
     * @throws Exception\Directory\DirectoryDoesNotExistException
     * @throws Exception\Directory\DirectoryIsNotWritableException
     * @throws Exception\Directory\DirectoryNameException
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::$sDirPrivate = Directory::tempdir();
        static::$sDirPublic  = Directory::tempdir();

        //  Place a existing-file file in the cache
        file_put_contents(static::$sDirPrivate . 'existing-file.txt', 'Some data');
        file_put_contents(static::$sDirPublic . 'existing-file.txt', 'Some data');

        static::$oCachePrivate = new Driver(
            static::$sDirPrivate
        );
        static::$oCachePublic  = new Driver\AccessibleByUrl(
            static::$sDirPublic
        );

        static::$oCache = new FileCache(
            static::$oCachePrivate,
            static::$oCachePublic
        );
    }

    // --------------------------------------------------------------------------

    public function testCacheThrowsExceptionOnInvalidMethod()
    {
        $this->expectException(
            Exception\FileCacheException::class
        );

        static::$oCache->invalidMethod();
    }

    // --------------------------------------------------------------------------

    public function testPrivateCacheThrowsExceptionOnInvalidDirectory()
    {
        $this->expectException(
            Exception\Directory\DirectoryDoesNotExistException::class
        );

        $sDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5(microtime(true));
        $this->assertDirectoryNotExists($sDir);

        new Driver($sDir);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\FileCache\Driver::getDir
     */
    public function testPrivateCacheDirIsValid()
    {
        $this->assertEquals(
            static::$sDirPrivate,
            static::$oCache->getDir()
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\FileCache\Driver::write
     */
    public function testCanWriteToPrivateCache()
    {
        $sData = 'Some test data';
        $sKey  = 'cache.txt';

        $oItem = static::$oCache->write($sData, $sKey);

        $this->assertInstanceOf(Item::class, $oItem);
        $this->assertEquals($sKey, $oItem->getKey());
        $this->assertEquals($sData, (string) $oItem);
        $this->assertFileExists(static::$sDirPrivate . $sKey);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\FileCache\Driver::write
     */
    public function testCanWriteToPrivateCacheWithoutKey()
    {
        $sData = 'Some test data';

        $oItem = static::$oCache->write($sData);

        $this->assertInstanceOf(Item::class, $oItem);
        $this->assertEquals($sData, (string) $oItem);
        $this->assertFileExists(static::$sDirPrivate . $oItem->getKey());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\FileCache\Driver::read
     */
    public function testCanReadFromPrivateCache()
    {
        $oItem = static::$oCache->read('existing-file.txt');

        $this->assertInstanceOf(Item::class, $oItem);
        $this->assertEquals('existing-file.txt', $oItem->getKey());
        $this->assertEquals(static::$sDirPrivate . 'existing-file.txt', $oItem->getPath());
        $this->assertEquals('Some data', (string) $oItem);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\FileCache\Driver::read
     */
    public function testPrivateCacheReadReturnsNullOnInvalidItem()
    {
        $oItem = static::$oCache->read('non-existing-file.txt');
        $this->assertNull($oItem);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\FileCache\Driver::exists
     */
    public function testCheckValidItemExistsInPrivateCache()
    {
        $this->assertTrue(static::$oCache->exists('existing-file.txt'));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\FileCache\Driver::exists
     */
    public function testCheckInvalidItemExistsInPrivateCache()
    {
        $this->assertFalse(static::$oCache->exists('non-existing-file.txt'));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\FileCache\Driver::delete
     */
    public function testCanDeleteValidItemFromPrivateCache()
    {
        $this->assertFileExists(static::$sDirPrivate . 'existing-file.txt');
        $bResult = static::$oCache->delete('existing-file.txt');
        $this->assertTrue($bResult);
        $this->assertFileNotExists(static::$sDirPrivate . 'existing-file.txt');
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\FileCache\Driver::delete
     */
    public function testCanDeleteInvalidItemFromPrivateCache()
    {
        $this->assertFileNotExists(static::$sDirPrivate . 'non-existing-file.txt');
        $bResult = static::$oCache->delete('non-existing-file.txt');
        $this->assertFalse($bResult);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\FileCache::public
     */
    public function testPublicCacheIsAccessible()
    {
        $this->assertSame(
            static::$oCachePublic,
            static::$oCache->public()
        );
    }

    // --------------------------------------------------------------------------

    public function testPublicCacheThrowsExceptionOnInvalidDirectory()
    {
        $this->expectException(
            Exception\Directory\DirectoryDoesNotExistException::class
        );

        $sDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5(microtime(true));
        $this->assertDirectoryNotExists($sDir);

        new Driver\AccessibleByUrl($sDir);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\FileCache\Driver\AccessibleByUrl::getDir
     */
    public function testPublicCacheDirIsValid()
    {
        $this->assertEquals(
            static::$sDirPublic,
            static::$oCache->public()->getDir()
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\FileCache\Driver\AccessibleByUrl::write
     */
    public function testCanWriteToPublicCache()
    {
        $sData = 'Some test data';
        $sKey  = 'cache.txt';

        $oItem = static::$oCache->public()->write($sData, $sKey);

        $this->assertInstanceOf(Item::class, $oItem);
        $this->assertEquals($sKey, $oItem->getKey());
        $this->assertEquals($sData, (string) $oItem);
        $this->assertFileExists(static::$sDirPublic . $sKey);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\FileCache\Driver\AccessibleByUrl::write
     */
    public function testCanWriteToPublicCacheWithoutKey()
    {
        $sData = 'Some test data';

        $oItem = static::$oCache->public()->write($sData);

        $this->assertInstanceOf(Item::class, $oItem);
        $this->assertEquals($sData, (string) $oItem);
        $this->assertFileExists(static::$sDirPublic . $oItem->getKey());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\FileCache\Driver\AccessibleByUrl::read
     */
    public function testCanReadFromPublicCache()
    {
        $oItem = static::$oCache->public()->read('existing-file.txt');

        $this->assertInstanceOf(Item::class, $oItem);
        $this->assertEquals('existing-file.txt', $oItem->getKey());
        $this->assertEquals(static::$sDirPublic . 'existing-file.txt', $oItem->getPath());
        $this->assertEquals('Some data', (string) $oItem);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\FileCache\Driver\AccessibleByUrl::read
     */
    public function testPublicCacheReadReturnsNullOnInvalidItem()
    {
        $oItem = static::$oCache->public()->read('non-existing-file.txt');
        $this->assertNull($oItem);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\FileCache\Driver\AccessibleByUrl::exists
     */
    public function testCheckValidItemExistsInPublicCache()
    {
        $this->assertTrue(static::$oCache->public()->exists('existing-file.txt'));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\FileCache\Driver\AccessibleByUrl::exists
     */
    public function testCheckInvalidItemExistsInPublicCache()
    {
        $this->assertFalse(static::$oCache->public()->exists('non-existing-file.txt'));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\FileCache\Driver\AccessibleByUrl::delete
     */
    public function testCanDeleteValidItemFromPublicCache()
    {
        $this->assertFileExists(static::$sDirPublic . 'existing-file.txt');
        static::$oCache->public()->delete('existing-file.txt');
        $this->assertFileNotExists(static::$sDirPublic . 'existing-file.txt');
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\FileCache\Driver\AccessibleByUrl::delete
     */
    public function testCanDeleteInvalidItemFromPublicCache()
    {
        $this->assertFileNotExists(static::$sDirPublic . 'non-existing-file.txt');
        $bResult = static::$oCache->public()->delete('non-existing-file.txt');
        $this->assertFalse($bResult);
    }

    // --------------------------------------------------------------------------


    /**
     * @covers \Nails\Common\Service\FileCache\Driver\AccessibleByUrl::getUrl
     */
    public function testPublicCacheReturnsValidUrl()
    {
        $this->assertEquals(
            BASE_URL . 'cache/public',
            static::$oCache->public()->getUrl()
        );
        $this->assertEquals(
            BASE_URL . 'cache/public/existing-file.txt',
            static::$oCache->public()->getUrl('existing-file.txt')
        );
    }
}
