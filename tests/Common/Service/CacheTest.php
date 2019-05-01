<?php

namespace Tests\Common\Service;

use Nails\Common\Service\Cache;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    private static $oCache;

    // --------------------------------------------------------------------------

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$oCache = new Cache(
            Cache::DEFAULT_ROOT,
            Cache::DEFAULT_DIR_PRIVATE,
            Cache::DEFAULT_DIR_PUBLIC,
            Cache::DEFAULT_URL_PUBLIC
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers Cache::dir
     */
    public function testPublicDirIsValid(): void
    {
        $sDir = NAILS_APP_PATH .
            Cache::DEFAULT_ROOT . DIRECTORY_SEPARATOR .
            Cache::DEFAULT_PUBLIC . DIRECTORY_SEPARATOR;

        $this->assertEquals($sDir, static::$oCache->dir(Cache::PUBLIC));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers Cache::dir
     */
    public function testPrivateDirIsValid(): void
    {
        $sDir = NAILS_APP_PATH .
            Cache::ROOT . DIRECTORY_SEPARATOR .
            Cache::PRIVATE . DIRECTORY_SEPARATOR;

        $this->assertEquals($sDir, static::$oCache->dir());
        $this->assertEquals($sDir, static::$oCache->dir(Cache::PRIVATE));
    }
}
