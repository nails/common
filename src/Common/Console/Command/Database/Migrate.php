<?php

namespace Nails\Common\Console\Command\Database;

use Nails\Common\Factory\Component;
use Nails\Common\Service\Database;
use Nails\Common\Service\Routes;
use Nails\Config;
use Nails\Components;
use Nails\Console\Command\Base;
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
    const VALID_MIGRATION_PATTERN           = '/^(\d+)(.*)\.php$/';
    const EXIT_CODE_NO_DB                   = 2;
    const EXIT_CODE_DB_NOT_READY            = 4;
    const EXIT_CODE_MIGRATION_FAILED        = 6;
    const EXIT_CODE_MODULE_MIGRATION_FAILED = 7;
    const EXIT_CODE_APP_MIGRATION_FAILED    = 8;

    // --------------------------------------------------------------------------

    /**
     * The database instance
     *
     * @var Database
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

        //  Check environment
        if (Environment::is(Environment::ENV_PROD)) {
            $this->banner('WARNING: The app is in PRODUCTION', 'error');
            if (!$this->confirm('Continue with migration?', true)) {
                return $this->abort();
            }
        }

        // --------------------------------------------------------------------------

        //  Work out the DB credentials to use
        $sDbHost = $oInput->getOption('dbHost') ?: Config::get('DB_HOST');
        $sDbUser = $oInput->getOption('dbUser') ?: Config::get('DB_USERNAME');
        $sDbPass = $oInput->getOption('dbPass') ?: Config::get('DB_PASSWORD');
        $sDbName = $oInput->getOption('dbName') ?: Config::get('DB_DATABASE');
        $iDbPort = $oInput->getOption('dbPort') ?: Config::get('DB_PORT');

        //  Check we have a database to connect to
        if (empty($sDbName)) {
            $oOutput->writeln('<error>No database defined</error>');
            return $this->abort(static::EXIT_CODE_NO_DB);
        }

        //  Get the DB object
        $this->oDb = Factory::service('PDODatabase');
        $this->oDb->connect($sDbHost, $sDbUser, $sDbPass, $sDbName, $iDbPort);

        //  Test the db
        $iResult = $this->oDb->query('SHOW Tables LIKE \'' . Config::get('NAILS_DB_PREFIX') . 'migration\'')->rowCount();
        if (!$iResult) {

            //  Create the migrations table
            $sSql = "CREATE TABLE `" . Config::get('NAILS_DB_PREFIX') . "migration` (
              `module` VARCHAR(100) NOT NULL DEFAULT '',
              `version` INT(11) UNSIGNED DEFAULT NULL,
              PRIMARY KEY (`module`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

            if (!(bool) $this->oDb->query($sSql)) {

                $oOutput->writeln('');
                $oOutput->writeln('<error>Database isn\'t ready for migrations.</error>');

                return $this->abort(static::EXIT_CODE_DB_NOT_READY);
            }
        }

        // --------------------------------------------------------------------------

        //  Backwards compatability - ensure that the vendor prefix is correct
        //  @todo (Pablo 2021-03-25) - Remove this
        $this->oDb->query(
            'UPDATE `' . Config::get('NAILS_DB_PREFIX') . 'migration` SET `module` = REPLACE(`module`, "nailsapp/", "nails/");'
        );

        // --------------------------------------------------------------------------

        $aEnabledModules = $this->findEnabledModules();

        // --------------------------------------------------------------------------

        //  Anything to migrate?
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
                $sLine  = ' - <comment>' . $oModule->name . '</comment> from ';
                $sLine  .= '<info>' . $sStart . '</info> to <info>#' . $oModule->end . '</info>';

                $oOutput->writeln($sLine);
            }
        }

        $oOutput->writeln('');
        $oOutput->writeln('Routes will be rewritten');
        $oOutput->writeln('Setting defaults will be set');
        $oOutput->writeln('');

        if (!$this->confirm('Continue?', true)) {
            return $this->abort(static::EXIT_CODE_SUCCESS);
        }

        // --------------------------------------------------------------------------

        $oOutput->writeln('');
        $oOutput->writeln('<comment>Starting migration</comment>...');

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
        $oResult              = $this->oDb->query('SHOW Variables WHERE Variable_name=\'FOREIGN_KEY_CHECKS\'')->fetch(\PDO::FETCH_OBJ);
        $sOldForeignKeyChecks = $oResult->Value;

        $this->oDb->query('SET FOREIGN_KEY_CHECKS = 0;');

        //  Migrate the modules
        if (!empty($aEnabledModules)) {
            foreach ($aEnabledModules as $oModule) {

                $oOutput->write('[' . $iCurStep . '/' . $iNumMigrations . '] Migrating <info>' . $oModule->name . '</info>... ');
                if ($this->doMigration($oModule)) {
                    $oOutput->writeln('<info>done</info>');
                } else {
                    return $this->abort(static::EXIT_CODE_MODULE_MIGRATION_FAILED);
                }

                $iCurStep++;
            }
        }

        // --------------------------------------------------------------------------

        //  Restore previous foreign key checks
        $this->oDb->query('SET FOREIGN_KEY_CHECKS = \'' . $sOldForeignKeyChecks . '\';');

        // --------------------------------------------------------------------------

        return $this->complete();
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether or not a component needs migrated, and if so between what versions
     *
     * @param Component $oComponent The component being analysed
     *
     * @return \stdClass|null stdClass when migration needed, null when not needed
     */
    protected function determineModuleState(Component $oComponent): ?\stdClass
    {
        $oState = (object) [
            'name'  => $oComponent->slug,
            'type'  => null,
            'start' => null,
            'end'   => null,
        ];

        // --------------------------------------------------------------------------

        if ($oComponent->slug === Components::$sAppSlug) {
            $sMigrationsPath = $oComponent->path . 'application/migrations';
        } else {
            $sMigrationsPath = $oComponent->path . 'migrations';
        }

        // --------------------------------------------------------------------------

        //  Work out if the module needs migrated and if so between what and what
        $aDirMap = $this->mapDir($sMigrationsPath);

        if (!empty($aDirMap)) {

            //  Work out all the files we have and get their index
            $aMigrations = [];
            foreach ($aDirMap as $dir) {
                $aMigrations[$dir['path']] = [
                    'index' => $dir['index'],
                ];
            }

            // --------------------------------------------------------------------------

            //  Work out the current version
            $sSql    = "SELECT `version` FROM `" . Config::get('NAILS_DB_PREFIX') . "migration` WHERE `module` = '$oComponent->slug';";
            $oResult = $this->oDb->query($sSql);

            if ($oResult->rowCount() === 0) {

                //  Add a row for the module
                $sSql = "INSERT INTO `" . Config::get('NAILS_DB_PREFIX') . "migration` (`module`, `version`) VALUES ('$oComponent->slug', NULL);";
                $this->oDb->query($sSql);

                $iCurrentVersion = null;

            } else {
                $iCurrentVersion = $oResult->fetch(\PDO::FETCH_OBJ)->version;
                $iCurrentVersion = is_null($iCurrentVersion) ? null : (int) $iCurrentVersion;
            }

            // --------------------------------------------------------------------------

            //  Define the variable
            $aLastMigration = end($aMigrations);
            $oState->start  = $iCurrentVersion;
            $oState->end    = $aLastMigration['index'];
        }

        return $oState->start === $oState->end ? null : $oState;
    }

    // --------------------------------------------------------------------------

    /**
     * Looks for enabled Nails modules which support migration
     *
     * @return array
     */
    protected function findEnabledModules(): array
    {
        $aModules = Components::available(false);
        $aOut     = [];

        foreach ($aModules as $oModule) {
            $aOut[] = $this->determineModuleState($oModule);
        }

        $aOut = array_filter($aOut);
        $aOut = array_values($aOut);

        //  Shift the app migrations onto the end so they are executed last
        if (!empty($aOut)) {
            $oFirst = reset($aOut);
            if ($oFirst->name === Components::$sAppSlug) {
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
     * Executes a migration
     *
     * @param \stdClass $oModule The migration details object
     *
     * @return boolean
     */
    protected function doMigration($oModule): bool
    {
        $oOutput = $this->oOutput;

        // --------------------------------------------------------------------------

        //  Map the directory and fetch only the files we need
        $sPath   = $oModule->name == Components::$sAppSlug ? 'application/migrations/' : 'vendor/' . $oModule->name . '/migrations/';
        $aDirMap = $this->mapDir($sPath);

        //  Set the current version to -1 if null so migrations with a zero index are picked up
        $iCurrent = is_null($oModule->start) ? -1 : $oModule->start;

        //  Go through all the migrations, skip any which have already been executed
        foreach ($aDirMap as $aMigration) {
            if ($aMigration['index'] > $iCurrent) {
                if (!$this->executeMigration($oModule, $aMigration)) {
                    return false;
                }
            }

            //  Mark this migration as complete
            $sSql    = "UPDATE `" . Config::get('NAILS_DB_PREFIX') . "migration` SET `version` = " . $aMigration['index'] . " WHERE `module` = '" . $oModule->name . "'";
            $oResult = $this->oDb->query($sSql);
        }

        if (!$oResult) {

            // Error updating migration record
            $oOutput->writeln('');
            $oOutput->writeln('');
            $oOutput->writeln('<error>ERROR</error>: Failed to update migration record for <info>' . $oModule->name . '</info>.');

            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Executes an individual migration
     *
     * @param object $oModule    The module being migrated
     * @param array  $aMigration The migration details
     *
     * @return boolean
     */
    private function executeMigration($oModule, $aMigration): bool
    {
        $oOutput = $this->oOutput;

        require_once $aMigration['path'];

        //  Generate the expected class name, i.e., "vendor-name/package-name" -> VendorName\PackageName"
        $sPattern    = '[^a-zA-Z0-9' . preg_quote(DIRECTORY_SEPARATOR, '/\-') . ']';
        $sModuleName = strtolower($oModule->name);
        $sModuleName = preg_replace('/' . $sPattern . '/', ' ', $sModuleName);
        $sModuleName = str_replace(DIRECTORY_SEPARATOR, ' ' . DIRECTORY_SEPARATOR . ' ', $sModuleName);
        $sModuleName = ucwords($sModuleName);
        $sModuleName = str_replace(' ', '', $sModuleName);
        $sModuleName = str_replace(DIRECTORY_SEPARATOR, '\\', $sModuleName);

        $sClassName = 'Nails\Database\Migration\\' . $sModuleName . '\Migration' . $aMigration['index'];

        if (class_exists($sClassName)) {

            $oMigration = new $sClassName();

            if (is_subclass_of($oMigration, 'Nails\Common\Console\Migrate\Base')) {

                try {
                    $oMigration->execute();
                } catch (\Exception $e) {
                    $oOutput->writeln('');
                    $oOutput->writeln('');
                    $oOutput->writeln('<error>ERROR</error>: Migration at "' . $aMigration['path'] . '" failed:');
                    $oOutput->writeln('<error>ERROR</error>: #' . $e->getCode() . ' - ' . $e->getMessage());
                    $oOutput->writeln('<error>ERROR</error>: Failed Query: #' . $oMigration->getQueryCount());
                    $oOutput->writeln('<error>ERROR</error>: Failed Query: ' . $oMigration->getLastQuery());
                    return false;
                }

            } else {

                $oOutput->writeln('');
                $oOutput->writeln('');
                $oOutput->writeln('<error>ERROR</error>: Migration at "' . $aMigration['path'] . '" is badly configured.');
                $oOutput->writeln('<error>ERROR</error>: Should be a sub-class of "Nails\Common\Console\Migrate\Base".');
                return false;
            }

            unset($oMigration);

        } else {

            $oOutput->writeln('');
            $oOutput->writeln('');
            $oOutput->writeln('<error>ERROR</error>: Class "' . $sClassName . '" does not exist.');
            $oOutput->writeln('<error>ERROR</error>: Migration at "' . $aMigration['path'] . '" is badly configured.');
            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Generates an array of files in a directory
     *
     * @param string $sDir The directory to analyse
     *
     * @return array|null
     */
    private function mapDir($sDir): ?array
    {
        if (is_dir($sDir)) {

            $aOut = [];

            foreach (new \DirectoryIterator($sDir) as $oFileInfo) {

                if ($oFileInfo->isDot()) {
                    continue;
                }

                //  In the correct format?
                if (preg_match(static::VALID_MIGRATION_PATTERN, $oFileInfo->getFilename(), $aMatches)) {
                    $aOut[$aMatches[1]] = [
                        'path'  => $oFileInfo->getPathname(),
                        'index' => (int) $aMatches[1],
                    ];
                }
            }

            ksort($aOut);
            return $aOut;

        } else {
            return null;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Replaces {{CONSTANT}} with the value of constant, CONSTANT
     *
     * @param string $sString The string to search on
     *
     * @return string
     */
    protected function replaceConstants($sString): string
    {
        return preg_replace_callback(
            '/{{(.+)}}/',
            function ($aMatches) {
                if (defined($aMatches[1])) {
                    return constant($aMatches[1]);
                }

                return $aMatches[0];
            },
            $sString
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Completes the migration, running post-processing tasks
     *
     * @return int
     * @throws \Exception
     */
    protected function complete(): int
    {
        //  Set default settings
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
        return parent::abort($iExitCode, ['Aborting database migration']);
    }
}
