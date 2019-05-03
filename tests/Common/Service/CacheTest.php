<?php

namespace Tests\Common\Service;

use Nails\Common\Exception;
use Nails\Common\Helper\Directory;
use Nails\Common\Interfaces\Service\Cache\CachePrivate;
use Nails\Common\Interfaces\Service\Cache\CachePublic;
use Nails\Common\Resource\Cache\Item;
use Nails\Common\Service\Cache;
use Nails\Common\Service\Config;
use PHPUnit\Framework\TestCase;

/**
 * Class CacheTest
 *
 * @package Tests\Common\Service
 */
class CacheTest extends TestCase
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
     * @var CachePrivate
     */
    protected static $oCachePrivate;

    /**
     * @var CachePublic
     */
    protected static $oCachePublic;

    /**
     * @var Cache
     */
    protected static $oCache;

    // --------------------------------------------------------------------------

    /**
     * @throws Exception\CacheException
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

        static::$oCachePrivate = new Cache\CachePrivate(
            static::$sDirPrivate
        );
        static::$oCachePublic  = new Cache\CachePublic(
            static::$sDirPublic
        );

        static::$oCache = new Cache(
            static::$oCachePrivate,
            static::$oCachePublic
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\Cache\CachePrivate::getDir
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
     * @covers \Nails\Common\Service\Cache\CachePrivate::write
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
     * @covers \Nails\Common\Service\Cache\CachePrivate::read
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
     * @covers \Nails\Common\Service\Cache\CachePrivate::exists
     */
    public function testCheckValidItemExistsInPrivateCache()
    {
        $this->assertTrue(static::$oCache->exists('existing-file.txt'));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\Cache\CachePrivate::exists
     */
    public function testCheckInvalidItemExistsInPrivateCache()
    {
        $this->assertFalse(static::$oCache->exists('non-existing-file.txt'));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\Cache\CachePrivate::delete
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
     * @covers \Nails\Common\Service\Cache\CachePrivate::delete
     */
    public function testCanDeleteInvalidItemFromPrivateCache()
    {
        $this->assertFileNotExists(static::$sDirPrivate . 'non-existing-file.txt');
        $bResult = static::$oCache->delete('non-existing-file.txt');
        $this->assertFalse($bResult);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\Cache::public
     */
    public function testPublicCacheIsAccessible()
    {
        $this->assertSame(
            static::$oCachePublic,
            static::$oCache->public()
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\Cache\CachePublic::getDir
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
     * @covers \Nails\Common\Service\Cache\CachePublic::write
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
     * @covers \Nails\Common\Service\Cache\CachePublic::read
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
     * @covers \Nails\Common\Service\Cache\CachePublic::exists
     */
    public function testCheckValidItemExistsInPublicCache()
    {
        $this->assertTrue(static::$oCache->public()->exists('existing-file.txt'));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\Cache\CachePublic::exists
     */
    public function testCheckInvalidItemExistsInPublicCache()
    {
        $this->assertFalse(static::$oCache->public()->exists('non-existing-file.txt'));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\Cache\CachePublic::delete
     */
    public function testCanDeleteValidItemFromPublicCache()
    {
        $this->assertFileExists(static::$sDirPublic . 'existing-file.txt');
        static::$oCache->public()->delete('existing-file.txt');
        $this->assertFileNotExists(static::$sDirPublic . 'existing-file.txt');
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Service\Cache\CachePublic::delete
     */
    public function testCanDeleteInvalidItemFromPublicCache()
    {
        $this->assertFileNotExists(static::$sDirPublic . 'non-existing-file.txt');
        $bResult = static::$oCache->public()->delete('non-existing-file.txt');
        $this->assertFalse($bResult);
    }

    // --------------------------------------------------------------------------


    /**
     * @covers \Nails\Common\Service\Cache\CachePublic::getUrl
     */
    public function testPublicCacheReturnsValidUrl()
    {
        if (function_exists('get_instance')) {
            $this->assertEquals(
                Config::siteUrl('cache/public'),
                static::$oCache->public()->getUrl()
            );
            $this->assertEquals(
                Config::siteUrl('cache/public/existing-file.txt'),
                static::$oCache->public()->getUrl('existing-file.txt')
            );
        } else {
            $this->markTestSkipped('Test cannot run as CodeIgniter is not available');
        }
    }
}
