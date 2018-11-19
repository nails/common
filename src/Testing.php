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
    }

    // --------------------------------------------------------------------------

    /**
     * Migrate the test database
     */
    private function migrateDatabase($sEntryPoint)
    {
        $oInputInterface = new ArrayInput([
            'command'          => !empty($_ENV['TEST_DB_MODE']) && $_ENV['TEST_DB_MODE'] === 'fresh' ? 'db:rebuild' : 'db:migrate',
            '--dbName'         => static::DB_NAME,
            '--no-interaction' => true,
        ]);

        $oApp = new App();
        $oApp->go($sEntryPoint, $oInputInterface, null, false);
    }
}
