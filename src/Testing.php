<?php

/**
 * Nails testing environment
 *
 * @package     Nails
 * @subpackage  common
 * @category    Factory
 * @author      Nails Dev Team
 */

namespace Nails;

use Nails\Console\App;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class Testing
{
    /**
     * The relative path to the database file
     *
     * @var string
     */
    const DB_NAME = 'testing';

    /**
     * The header which will be sent used when testing
     *
     * @var string
     */
    const TEST_HEADER_NAME = 'X-Testing';

    /**
     * The value of the test header
     *
     * @var string
     */
    const TEST_HEADER_VALUE = 'enabled';

    // --------------------------------------------------------------------------

    /**
     * Testing constructor.
     *
     * @param $sEntryPoint
     */
    public function __construct($sEntryPoint)
    {
        $this->migrateDatabase($sEntryPoint);
        \App\Tests\Bootstrap::setUp();
    }

    // --------------------------------------------------------------------------

    /**
     * Testing destructor.
     */
    public function __destruct()
    {
        \App\Tests\Bootstrap::tearDown();
        $this->destroyDatabase();
    }

    // --------------------------------------------------------------------------

    /**
     * Migrate the test database
     */
    private function migrateDatabase($sEntryPoint)
    {
        $oApp = new App();
        $oApp->go(
            $sEntryPoint,
            new ArrayInput([
                'command'  => 'db:migrate',
                '--dbName' => static::DB_NAME,
            ]),
            new NullOutput(),
            false
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Destroy the in-memory database
     */
    private function destroyDatabase()
    {
    }
}
