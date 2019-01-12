<?php

/**
 * Nails testing environment
 *
 * @package     Nails
 * @subpackage  common
 * @category    core
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

    /**
     * The header which will contain the user ID to immitate
     *
     * @var string
     */
    const TEST_HEADER_USER_NAME = 'X-Testing-As-User';

    // --------------------------------------------------------------------------

    /**
     * Testing constructor.
     *
     * @param $sEntryPoint
     */
    public function __construct($sEntryPoint)
    {
        $this->migrateDatabase($sEntryPoint);
    }

    // --------------------------------------------------------------------------

    /**
     * Testing destructor.
     */
    public function __destruct()
    {
        $this->tearDown();
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

    // --------------------------------------------------------------------------

    /**
     * Set up the testing environment; called before any tests are run
     */
    public function setUp()
    {
        \App\Tests\Bootstrap::setUp();
    }

    // --------------------------------------------------------------------------

    /**
     * Tear down the testing environment; called after all tests have been run
     */
    public function tearDown()
    {
        \App\Tests\Bootstrap::tearDown();
    }
}
