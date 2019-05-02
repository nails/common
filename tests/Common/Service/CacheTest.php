<?php

namespace Tests\Common\Service;

use Nails\Common\Exception;
use Nails\Common\Helper\Directory;
use Nails\Common\Resource\Cache\Item;
use Nails\Common\Service\Cache;
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
     * @var \Nails\Common\Interfaces\Service\Cache
     */
    protected static $oCachePrivate;

    /**
     * @var \Nails\Common\Interfaces\Service\Cache
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

    public function testPrivateCacheDirIsValid()
    {
        $this->assertEquals(
            static::$sDirPrivate,
            static::$oCache->getDir()
        );
    }

    // --------------------------------------------------------------------------

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

//    public function testCanReadFromPrivateCache()
//    {
//        //  @todo (Pablo - 2019-05-02) - test the private cache can be read
//    }

    // --------------------------------------------------------------------------

//    public function testCanDeleteFromPrivateCache()
//    {
//        //  @todo (Pablo - 2019-05-02) - test items can be deleted from the private cache
//    }

    // --------------------------------------------------------------------------

//    public function testCheckValidItemExistsInPrivateCache()
//    {
//        //  @todo (Pablo - 2019-05-02) - test that a valid item exists in the private cache
//    }

    // --------------------------------------------------------------------------

//    public function testCheckInvalidItemExistsInPrivateCache()
//    {
//        //  @todo (Pablo - 2019-05-02) - test that an item does not exist in the private cache
//    }

    // --------------------------------------------------------------------------

    public function testPublicCacheIsAccessible()
    {
        $this->assertSame(
            static::$oCachePublic,
            static::$oCache->public()
        );
    }

    // --------------------------------------------------------------------------

    public function testPublicCacheDirIsValid()
    {
        $this->assertEquals(
            static::$sDirPublic,
            static::$oCache->public()->getDir()
        );
    }

    // --------------------------------------------------------------------------

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

//    public function testCanReadFromPublicCache()
//    {
//        //  @todo (Pablo - 2019-05-02) - test the public cache can be read
//    }

    // --------------------------------------------------------------------------

//    public function testCanDeleteFromPublicCache()
//    {
//        //  @todo (Pablo - 2019-05-02) - test items can be deleted from the public cache
//    }

    // --------------------------------------------------------------------------

//    public function testCheckValidItemExistsInPublicCache()
//    {
//        //  @todo (Pablo - 2019-05-02) - test that a valid item exists in the public cache
//    }

    // --------------------------------------------------------------------------

//    public function testCheckInvalidItemExistsInPublicCache()
//    {
//        //  @todo (Pablo - 2019-05-02) - test that an item does not exist in the public cache
//    }

    // --------------------------------------------------------------------------

//    public function testPublicCacheReturnsValidUrl()
//    {
//        //  @todo (Pablo - 2019-05-02) - Test the public cache returns a valid URL
//    }
}
