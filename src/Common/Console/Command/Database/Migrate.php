<?php

namespace Nails\Common\Console\Command\Database;

use Nails\Common\Factory\Component;
use Nails\Common\Interfaces;
use Nails\Common\Service\PDODatabase;
use Nails\Common\Service\Routes;
use Nails\Config;
use Nails\Components;
use Nails\Console\Command\Base;
use Nails\Console\Exception\ConsoleException;
use Nails\Environment;
use Nails\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Migrate
 *
 * @package Nails\Common\Console\Command\Database
 */
class Migrate extends Base
{
    /**
     * Exit codes
     */
    const EXIT_CODE_NO_DB            = 2;
    const EXIT_CODE_DB_NOT_READY     = 3;
    const EXIT_CODE_MIGRATION_FAILED = 4;

    // --------------------------------------------------------------------------

    /**
     * The database instance
     *
     * @var PDODatabase
     */
    private $oDb;

    // --------------------------------------------------------------------------

    /**
     * Configures the command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('db:migrate')
            ->setDescription('Runs database migration across all enabled modules')
            ->addOption(
                'dbHost',
                null,
                InputOption::VALUE_OPTIONAL,
                'Database Host'
            )
            ->addOption(
                'dbUser',
                null,
                InputOption::VALUE_OPTIONAL,
                'Database User'
            )
            ->addOption(
                'dbPass',
                null,
                InputOption::VALUE_OPTIONAL,
                'Database Password'
            )
            ->addOption(
                'dbName',
                null,
                InputOption::VALUE_OPTIONAL,
                'Database Name'
            )
            ->addOption(
                'dbPort',
                null,
                InputOption::VALUE_OPTIONAL,
                'Database Port'
            );
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the app
     *
     * @param InputInterface  $oInput  The Input Interface provided by Symfony
     * @param OutputInterface $oOutput The Output Interface provided by Symfony
     *
     * @return int
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput): int
    {
        parent::execute($oInput, $oOutput);

        $this->banner('Database: Migrate');

        // --------------------------------------------------------------------------

        try {

            $this
                ->checkEnvironment()
                ->connectToDb()
                ->testDb();

            // --------------------------------------------------------------------------

            //  Backwards compatability - ensure that the vendor prefix is correct
            //  @todo (Pablo 2021-03-25) - Remove this
            $this->oDb->query(
                'UPDATE `' . Config::get('NAILS_DB_PREFIX') . 'migration` SET `module` = REPLACE(`module`, "nailsapp/", "nails/");'
            );

            // --------------------------------------------------------------------------

            $aEnabledModules = $this->findComponentsWhichNeedMigrated();
            if (!$aEnabledModules) {
                $oOutput->writeln('Nothing to migrate');
                $oOutput->writeln('');

                return $this->complete();
            }

            // --------------------------------------------------------------------------

            //  Confirm what's going to happen
            $oOutput->writeln('');
            $oOutput->writeln('OK, here\'s what\'s going to happen:');

            if ($aEnabledModules) {

                $oOutput->writeln('');

                foreach ($aEnabledModules as $oModule) {

                    $sStart = is_null($oModule->start) ? 'The beginning of time' : '#' . $oModule->start;
                    $sLine  = ' - <comment>' . $oModule->slug . '</comment> from ';
                    $sLine  .= '<info>' . $sStart . '</info> to <info>#' . $oModule->end . '</info>';

                    $oOutput->writeln($sLine);
                }
            }

            $oOutput->writeln('');
            $oOutput->writeln('Routes will be rewritten');
            $oOutput->writeln('Setting defaults will be set');
            $oOutput->writeln('');

            if (!$this->confirm('Continue?', true)) {
                throw new ConsoleException('', static::EXIT_CODE_SUCCESS);
            }

            // --------------------------------------------------------------------------

            $oOutput->writeln('');
            $oOutput->writeln('<comment>Starting migration...</comment>');

            /**
             * Ignore route rewriting until the whole migration is complete. Some actions
             * might internally trigger a rewrite which could cause things to fall over as
             * the code will have expected the migration to finish. We rewrite the routes
             * afterwards anyway so any internal request will [eventually] be honoured.
             */
            /** @var Routes $oRoutes */
            $oRoutes = Factory::service('Routes');
            $oRoutes->ignoreRewriteRequests(true);

            // --------------------------------------------------------------------------

            //  Start migrating
            $iCurStep       = 1;
            $iNumMigrations = 0;
            $iNumMigrations += !empty($aEnabledModules) ? count($aEnabledModules) : 0;

            //  Disable foreign key checks
            $oResult = $this->oDb->query('SHOW Variables WHERE Variable_name=\'FOREIGN_KEY_CHECKS\'')
                ->fetch(\PDO::FETCH_OBJ);

            $sOldForeignKeyChecks = $oResult->Value;

            $this->oDb->query('SET FOREIGN_KEY_CHECKS = 0;');

            //  Migrate the modules
            if (!empty($aEnabledModules)) {
                foreach ($aEnabledModules as $oModule) {
                    $oOutput->write('[' . $iCurStep . '/' . $iNumMigrations . '] Migrating <info>' . $oModule->slug . '</info>... ');
                    if ($this->doMigration($oModule)) {
                        $oOutput->writeln('<info>done</info>');
                    } else {
                        throw new ConsoleException('', static::EXIT_CODE_MIGRATION_FAILED);
                    }

                    $iCurStep++;
                }
            }

            // --------------------------------------------------------------------------

            //  Restore previous foreign key checks
            $this->oDb->query('SET FOREIGN_KEY_CHECKS = \'' . $sOldForeignKeyChecks . '\';');

            // --------------------------------------------------------------------------

            return $this->complete();

        } catch (\Exception $e) {
            return $this->abort(
                $e->getCode(),
                array_filter([$e->getMessage()])
            );
        }
    }

    // --------------------------------------------------------------------------

    /**
     * If on prod, seek confirmation
     *
     * @return $this
     * @throws ConsoleException
     */
    private function checkEnvironment(): self
    {
        if (Environment::is(Environment::ENV_PROD)) {
            $this->banner('WARNING: The app is in PRODUCTION', 'error');
            if (!$this->confirm('Continue with migration?', true)) {
                throw new ConsoleException();
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Connect to the database
     *
     * @return $this
     * @throws ConsoleException
     * @throws \Nails\Common\Exception\FactoryException
     */
    private function connectToDb(): self
    {
        //  Work out the DB credentials to use
        $sDbHost = $this->oInput->getOption('dbHost') ?: Config::get('DB_HOST');
        $sDbUser = $this->oInput->getOption('dbUser') ?: Config::get('DB_USERNAME');
        $sDbPass = $this->oInput->getOption('dbPass') ?: Config::get('DB_PASSWORD');
        $sDbName = $this->oInput->getOption('dbName') ?: Config::get('DB_DATABASE');
        $iDbPort = $this->oInput->getOption('dbPort') ?: Config::get('DB_PORT');

        //  Check we have a database to connect to
        if (empty($sDbName)) {
            throw new ConsoleException('No database defined', static::EXIT_CODE_NO_DB);
        }

        //  Get the DB object
        $this->oDb = Factory::service('PDODatabase');
        $this->oDb->connect($sDbHost, $sDbUser, $sDbPass, $sDbName, $iDbPort);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Test the database
     *
     * @return $this
     * @throws ConsoleException
     */
    private function testDb(): self
    {
        //  Test the db
        $iResult = $this->oDb->query('SHOW Tables LIKE \'' . Config::get('NAILS_DB_PREFIX') . 'migration\'')->rowCount();
        if (!$iResult) {

            //  Create the migrations table
            $sTable = Config::get('NAILS_DB_PREFIX') . 'migration';
            $sSql   = <<<EOT

            CREATE TABLE `$sTable` (
                `module` VARCHAR(100) NOT NULL DEFAULT '',
                `version` INT(11) UNSIGNED DEFAULT NULL,
                PRIMARY KEY (`module`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            EOT;

            if (!(bool) $this->oDb->query($sSql)) {
                throw new ConsoleException('Database isn\'t ready for migrations.', static::EXIT_CODE_DB_NOT_READY);
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Looks for enabled Nails modules which require migration
     *
     * @return \stdClass[]
     */
    private function findComponentsWhichNeedMigrated(): array
    {
        $aModules = Components::available(false);
        $aOut     = [];

        foreach ($aModules as $oModule) {
            $oState = $this->determineModuleState($oModule);
            if ($oState->start !== $oState->end) {
                $aOut[] = $oState;
            }
        }

        //  Shift the app migrations onto the end so they are executed last
        if (!empty($aOut)) {
            $oFirst = reset($aOut);
            if ($oFirst->slug === Components::$sAppSlug) {
                $oApp = array_shift($aOut);
                $aOut = array_merge($aOut, [$oApp]);
                $aOut = array_filter($aOut);
                $aOut = array_values($aOut);
            }
        }

        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether or not a component needs migrated, and if so between what versions
     *
     * @param Component $oComponent The component being analysed
     *
     * @return \stdClass
     */
    private function determineModuleState(Component $oComponent): \stdClass
    {
        $oState = (object) [
            'slug'       => $oComponent->slug,
            'migrations' => $this->getMigrationsForComponent($oComponent),
            'start'      => null,
            'end'        => null,
        ];

        if (!empty($oState->migrations)) {
            $oState->start = $this->getCurrentMigrationIndex($oComponent);
            $oState->end   = end($oState->migrations)->getPriority();
        }

        return $oState;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all migrations for a given component
     *
     * @param Component $oComponent
     *
     * @return Interfaces\Database\Migration[]
     */
    private function getMigrationsForComponent(Component $oComponent): array
    {
        $aClasses = $oComponent
            ->findClasses('Database\\Migration')
            ->whichImplement(Interfaces\Database\Migration::class)
            ->whichCanBeInstantiated();

        $aMigrations = [];
        foreach ($aClasses as $sClass) {
            $aMigrations[] = new $sClass($this->oDb);
        }

        usort($aMigrations, function (Interfaces\Database\Migration $a, Interfaces\Database\Migration $b) {
            return $a->getPriority() <=> $b->getPriority();
        });

        return $aMigrations;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the current migration index for a given component
     *
     * @param Component $oComponent
     *
     * @return int|null
     */
    private function getCurrentMigrationIndex(Component $oComponent): ?int
    {
        $oResult = $this->oDb->query(sprintf(
            'SELECT `version` FROM `%s` WHERE `module` = " % s";',
            Config::get('NAILS_DB_PREFIX') . 'migration',
            $oComponent->slug
        ));

        if ($oResult->rowCount() === 0) {

            $oResult = $this->oDb->query(sprintf(
                'INSERT INTO `%s` (`module`, `version`) VALUES (" % s", NULL);',
                Config::get('NAILS_DB_PREFIX') . 'migration',
                $oComponent->slug
            ));

            return null;
        }

        $iCurrentVersion = $oResult->fetch(\PDO::FETCH_OBJ)->version;
        return is_null($iCurrentVersion) ? null : (int) $iCurrentVersion;
    }

    // --------------------------------------------------------------------------

    /**
     * Executes a migration
     *
     * @param \stdClass $oModule The migration details object
     *
     * @return bool
     */
    private function doMigration($oModule): bool
    {
        if ($oModule->slug == Components::$sAppSlug) {
            /**
             * Set all component settings ahead of migrating the app.
             * This ensures that the app has a sane foundation and all components
             * should work as expected, at least with default values.
             */
            $this->callCommand('install:settings', [], false, true);
        }

        // --------------------------------------------------------------------------

        /** @var Interfaces\Database\Migration $oMigration */
        foreach ($oModule->migrations as $oMigration) {

            $iPriority = $oMigration->getPriority();

            if ($iPriority > $oModule->start && $iPriority <= $oModule->end) {
                try {

                    $oMigration->execute();

                    //  Mark this migration as complete
                    $oResult = $this->oDb->query(sprintf(
                        'UPDATE `%s` SET `version` = %s WHERE `module` = " % s"',
                        Config::get('NAILS_DB_PREFIX') . 'migration',
                        $iPriority,
                        $oModule->slug
                    ));

                } catch (\Exception $e) {
                    $this->oOutput->writeln('');
                    $this->oOutput->writeln('');
                    $this->oOutput->writeln('<error>ERROR</error>: Migration "' . get_class($oMigration) . '" failed:');
                    $this->oOutput->writeln('<error>ERROR</error>: #' . $e->getCode() . ' - ' . $e->getMessage());
                    $this->oOutput->writeln('<error>ERROR</error>: Failed Query: #' . $oMigration->getQueryCount());
                    $this->oOutput->writeln('<error>ERROR</error>: Failed Query: ' . $oMigration->getLastQuery());
                    return false;
                }
            }
        }

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Completes the migration, running post-processing tasks
     *
     * @return int
     * @throws \Exception
     */
    private function complete(): int
    {
        //  Set default settings... again
        $this->oOutput->write('<comment>Setting defaults</comment>... ');
        $this->callCommand('install:settings', [], false, true);
        $this->oOutput->writeln('<info>done</info>');

        // --------------------------------------------------------------------------

        //  Rewrite Routes
        /** @var Routes $oRoutes */
        $oRoutes = Factory::service('Routes');
        $oRoutes->ignoreRewriteRequests(false);
        $this->oOutput->write('<comment>Rewriting routes</comment>... ');
        $this->callCommand('routes:rewrite', [], false, true);
        $this->oOutput->writeln('<info>done</info>');

        // --------------------------------------------------------------------------

        return static::EXIT_CODE_SUCCESS;
    }

    // --------------------------------------------------------------------------

    /**
     * Performs the abort functionality and returns the exit code
     *
     * @param integer $iExitCode The exit code
     * @param array   $aMessages The error message
     *
     * @return int
     */
    protected function abort($iExitCode = self::EXIT_CODE_FAILURE, array $aMessages = []): int
    {
        return parent::abort(
            $iExitCode,
            !empty($aMessages) ? $aMessages : ['Aborting database migration']
        );
    }
}
