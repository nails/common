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
        $this
            ->setDatabaseName()
            ->migrateDatabase($sEntryPoint);
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
     * Sets the database name in the config
     *
     * @return $this
     */
    private function setDatabaseName(): self
    {
        Config::set('DB_DATABASE', static::DB_NAME);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Migrate the test database
     *
     * @return $this
     */
    private function migrateDatabase($sEntryPoint): self
    {
        Config::set('DB_DATABASE', static::DB_NAME);
        $oInputInterface = new ArrayInput([
            'command'          => !empty($_ENV['TEST_DB_MODE']) && $_ENV['TEST_DB_MODE'] === 'fresh' ? 'db:rebuild' : 'db:migrate',
            '--dbName'         => static::DB_NAME,
            '--no-interaction' => true,
            '-vvv'             => true,
        ]);

        $oApp = new App();
        $oApp->go($sEntryPoint, $oInputInterface, null, false);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set up the testing environment; called before any tests are run
     *
     * @return $this
     */
    public function setUp(): self
    {
        \App\Tests\Bootstrap::setUp();
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Tear down the testing environment; called after all tests have been run
     *
     * @return $this
     */
    public function tearDown(): self
    {
        \App\Tests\Bootstrap::tearDown();
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Bootstraps Nails for testing, so tests can use the Factory etc
     *
     * @param string $sFile The module's bootstrapper (i.e __FILE__)
     */
    public static function bootstrapModule(string $sFile): void
    {
        Bootstrap::setEntryPoint(dirname($sFile));
        Bootstrap::setBaseDirectory(dirname($sFile));
        Bootstrap::setNailsConstants();
        Bootstrap::setCodeIgniterConstants(
            realpath(dirname($sFile) . '/../vendor/codeigniter/framework/system'),
            realpath(dirname($sFile) . '/../vendor/codeigniter/framework/application')
        );
        Factory::setup();
        Factory::autoload();
    }
}
