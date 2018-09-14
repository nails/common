<?php

namespace Nails\Common\Console\Command\Database;

use Nails\Console\Command\Base;
use Nails\Environment;
use Nails\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
     * @var Object
     */
    private $oDb;

    // --------------------------------------------------------------------------

    /**
     * Configures the app
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('db:migrate');
        $this->setDescription('Runs database migration across all enabled modules');

        $this->addOption(
            'dbHost',
            null,
            InputOption::VALUE_OPTIONAL,
            'Database Host'
        );

        $this->addOption(
            'dbUser',
            null,
            InputOption::VALUE_OPTIONAL,
            'Database User'
        );

        $this->addOption(
            'dbPass',
            null,
            InputOption::VALUE_OPTIONAL,
            'Database Password'
        );

        $this->addOption(
            'dbName',
            null,
            InputOption::VALUE_OPTIONAL,
            'Database Name'
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the app
     *
     * @param  InputInterface  $oInput  The Input Interface provided by Symfony
     * @param  OutputInterface $oOutput The Output Interface provided by Symfony
     *
     * @return int
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        parent::execute($oInput, $oOutput);

        $oOutput->writeln('');
        $oOutput->writeln('<info>-----------------------------</info>');
        $oOutput->writeln('<info>Nails Database Migration Tool</info>');
        $oOutput->writeln('<info>-----------------------------</info>');

        // --------------------------------------------------------------------------

        //  Check environment
        if (Environment::is('PRODUCTION')) {

            $oOutput->writeln('');
            $oOutput->writeln('--------------------------------------');
            $oOutput->writeln('| <info>WARNING: The app is in PRODUCTION.</info> |');
            $oOutput->writeln('--------------------------------------');
            $oOutput->writeln('');

            if (!$this->confirm('Continue with migration?', true)) {
                return $this->abort();
            }
        }

        // --------------------------------------------------------------------------

        //  Work out the DB credentials to use
        $sDbHost = $oInput->getOption('dbHost') ?: (defined('DEPLOY_DB_HOST') ? DEPLOY_DB_HOST : '');
        $sDbUser = $oInput->getOption('dbUser') ?: (defined('DEPLOY_DB_USERNAME') ? DEPLOY_DB_USERNAME : '');
        $sDbPass = $oInput->getOption('dbPass') ?: (defined('DEPLOY_DB_PASSWORD') ? DEPLOY_DB_PASSWORD : '');
        $sDbName = $oInput->getOption('dbName') ?: (defined('DEPLOY_DB_DATABASE') ? DEPLOY_DB_DATABASE : '');

        //  Check we have a database to connect to
        if (empty($sDbName)) {
            return $this->abort(static::EXIT_CODE_NO_DB);
        }

        //  Get the DB object
        $this->oDb = Factory::service('PDODatabase');
        $this->oDb->connect($sDbHost, $sDbUser, $sDbPass, $sDbName);

        if (!defined('NAILS_DB_PREFIX')) {
            define('NAILS_DB_PREFIX', 'nails_');
        }

        //  Test the db
        $iResult = $this->oDb->query('SHOW Tables LIKE \'' . NAILS_DB_PREFIX . 'migration\'')->rowCount();
        if (!$iResult) {

            //  Create the migrations table
            $sSql = "CREATE TABLE `" . NAILS_DB_PREFIX . "migration` (
              `module` VARCHAR(100) NOT NULL DEFAULT '',
              `version` INT(11) UNSIGNED DEFAULT NULL,
              PRIMARY KEY (`module`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

            if (!(bool) $this->oDb->query($sSql)) {

                $oOutput->writeln('');
                $oOutput->writeln('Database isn\'t ready for migrations.');

                return $this->abort(static::EXIT_CODE_DB_NOT_READY);
            }
        }

        // --------------------------------------------------------------------------

        $oOutput->writeln('');

        // --------------------------------------------------------------------------

        $aEnabledModules = $this->findEnabledModules();
        $oApp            = $this->determineModuleState('APP', 'application/migrations/');

        // --------------------------------------------------------------------------

        //  Anything to migrate?
        if (!$aEnabledModules && !$oApp) {
            $oOutput->writeln('');
            $oOutput->writeln('Nothing to migrate');
            $oOutput->writeln('');

            return self::EXIT_CODE_SUCCESS;
        }

        // --------------------------------------------------------------------------

        //  Confirm what's going to happen
        $oOutput->writeln('');
        $oOutput->writeln('OK, here\'s what\'s going to happen:');

        if ($aEnabledModules || $oApp) {

            $oOutput->writeln('');

            foreach ($aEnabledModules as $oModule) {

                $sStart = is_null($oModule->start) ? 'The beginning of time' : $oModule->start;
                $sLine  = ' - <comment>' . $oModule->name . '</comment> from ';
                $sLine  .= '<info>#' . $sStart . '</info> to <info>#' . $oModule->end . '</info>';

                $oOutput->writeln($sLine);
            }

            if ($oApp) {
                $sStart = is_null($oApp->start) ? 'The beginning of time' : $oApp->start;
                $sLine  = ' - <comment>app</comment> from ';
                $sLine  .= '<info>#' . $sStart . '</info> to <info>#' . $oApp->end . '</info>';
                $oOutput->writeln($sLine);
            }
        }

        $oOutput->writeln('');
        $oOutput->writeln('Routes will be rewritten');
        $oOutput->writeln('');

        if (!$this->confirm('Continue?', true)) {
            return $this->abort(static::EXIT_CODE_SUCCESS);
        }

        // --------------------------------------------------------------------------

        $oOutput->writeln('');
        $oOutput->writeln('<comment>Starting migration...</comment>');

        // --------------------------------------------------------------------------

        //  Start the DB transaction
        $this->oDb->transactionStart();

        // --------------------------------------------------------------------------

        //  Start migrating
        $iCurStep       = 1;
        $iNumMigrations = 0;
        $iNumMigrations += !empty($aEnabledModules) ? count($aEnabledModules) : 0;
        $iNumMigrations += !empty($oApp) ? 1 : 0;

        //  Disable foreign key checks
        $oResult              = $this->oDb->query('SHOW Variables WHERE Variable_name=\'FOREIGN_KEY_CHECKS\'')->fetch(\PDO::FETCH_OBJ);
        $sOldForeignKeyChecks = $oResult->Value;

        $this->oDb->query('SET FOREIGN_KEY_CHECKS = 0;');

        //  Migrate the modules
        if (!empty($aEnabledModules)) {
            foreach ($aEnabledModules as $oModule) {

                $oOutput->write('[' . $iCurStep . '/' . $iNumMigrations . '] Migrating <info>' . $oModule->name . '</info>... ');
                if ($this->doMigration($oModule)) {
                    $oOutput->writeln('done!');
                } else {
                    return $this->abort(static::EXIT_CODE_MODULE_MIGRATION_FAILED);
                }

                $iCurStep++;
            }
        }

        //  Migrate the app
        if (!empty($oApp)) {

            $oOutput->write('[' . $iCurStep . '/' . $iNumMigrations . '] Migrating <info>App</info>... ');
            if ($this->doMigration($oApp)) {
                $oOutput->writeln('done!');
            } else {
                return $this->abort(static::EXIT_CODE_APP_MIGRATION_FAILED);
            }
        }

        // --------------------------------------------------------------------------

        //  Commit the transaction
        $this->oDb->transactionCommit();

        // --------------------------------------------------------------------------

        //  Rewrite Routes
        $oOutput->writeln('<comment>Rewriting routes...</comment>');
        $this->callCommand('routes:rewrite', [], false, true);

        // --------------------------------------------------------------------------

        //  Cleaning up
        $oOutput->writeln('<comment>Cleaning up...</comment>');

        //  Restore previous foreign key checks
        $this->oDb->query('SET FOREIGN_KEY_CHECKS = \'' . $sOldForeignKeyChecks . '\';');

        // --------------------------------------------------------------------------

        //  And we're done
        $oOutput->writeln('');
        $oOutput->writeln('Complete!');

        return self::EXIT_CODE_SUCCESS;
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether or not the module needs to migration, and if so between what versions
     *
     * @param string $sModuleName     The module's name
     * @param string $sMigrationsPath The module's path
     *
     * @return null|\stdClass stdClass when migration needed, null when not needed
     */
    protected function determineModuleState($sModuleName, $sMigrationsPath)
    {
        $oModule        = new \stdClass();
        $oModule->name  = $sModuleName;
        $oModule->start = null;
        $oModule->end   = null;

        // --------------------------------------------------------------------------

        //  Work out if the module needs migrated and if so between what and what
        $aDirMap = $this->mapDir($sMigrationsPath);

        if (!empty($aDirMap)) {

            //  Work out all the files we have and get their index
            $migrations = [];
            foreach ($aDirMap as $dir) {
                $migrations[$dir['path']] = [
                    'index' => $dir['index'],
                ];
            }

            // --------------------------------------------------------------------------

            //  Work out the current version
            $sql    = "SELECT `version` FROM `" . NAILS_DB_PREFIX . "migration` WHERE `module` = '$sModuleName';";
            $result = $this->oDb->query($sql);

            if ($result->rowCount() === 0) {

                //  Add a row for the module
                $sql = "INSERT INTO `" . NAILS_DB_PREFIX . "migration` (`module`, `version`) VALUES ('$sModuleName', NULL);";
                $this->oDb->query($sql);

                $currentVersion = null;

            } else {

                $currentVersion = $result->fetch(\PDO::FETCH_OBJ)->version;
                $currentVersion = is_null($currentVersion) ? null : (int) $currentVersion;
            }

            // --------------------------------------------------------------------------

            //  Define the variable
            $lastMigration  = end($migrations);
            $oModule->start = $currentVersion;
            $oModule->end   = $lastMigration['index'];
        }

        return $oModule->start === $oModule->end ? null : $oModule;
    }

    // --------------------------------------------------------------------------

    /**
     * Looks for enabled Nails modules which support migration
     *
     * @return array
     */
    protected function findEnabledModules()
    {
        //  Look for components
        $modules = _NAILS_GET_COMPONENTS(false);
        $out     = [];

        foreach ($modules as $module) {
            $out[] = $this->determineModuleState($module->slug, $module->path . 'migrations/');
        }

        return array_filter($out);
    }

    // --------------------------------------------------------------------------

    /**
     * Executes a migration
     *
     * @param  \stdClass $oModule The migration details object
     *
     * @return boolean
     */
    protected function doMigration($oModule)
    {
        $oOutput = $this->oOutput;

        // --------------------------------------------------------------------------

        //  Map the directory and fetch only the files we need
        $sPath   = $oModule->name == 'APP' ? 'application/migrations/' : 'vendor/' . $oModule->name . '/migrations/';
        $aDirMap = $this->mapDir($sPath);

        //  Set the current version to -1 if null so migrations with a zero index are picked up
        $current = is_null($oModule->start) ? -1 : $oModule->start;

        //  Go through all the migrations, skip any which have already been executed
        $lastMigration = null;
        foreach ($aDirMap as $migration) {
            if ($migration['index'] > $current) {
                if (!$this->executeMigration($oModule, $migration)) {
                    return false;
                }
            }

            $lastMigration = $migration['index'];
        }

        //  Update the database
        $sql    = "UPDATE `" . NAILS_DB_PREFIX . "migration` SET `version` = " . $lastMigration . " WHERE `module` = '" . $oModule->name . "'";
        $result = $this->oDb->query($sql);

        if (!$result) {

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
     * @param  object $oModule    The module being migrated
     * @param  array  $aMigration The migration details
     *
     * @return boolean
     */
    private function executeMigration($oModule, $aMigration)
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
     * @param  string $sDir The directory to analyse
     *
     * @return array|boolean
     */
    private function mapDir($sDir)
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
            return false;
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
    protected function replaceConstants($sString)
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
     * Performs the abort functionality and returns the exit code
     *
     * @param  array   $aMessages The error message
     * @param  integer $iExitCode The exit code
     *
     * @return int
     */
    protected function abort($iExitCode = self::EXIT_CODE_FAILURE, $aMessages = [])
    {
        $aMessages[] = 'Aborting database migration';
        if (!empty($this->oDb) && $this->oDb->isTransactionRunning()) {
            $aMessages[] = 'Rolling back database';
            $this->oDb->transactionRollback();
        }

        return parent::abort($iExitCode, $aMessages);
    }
}
