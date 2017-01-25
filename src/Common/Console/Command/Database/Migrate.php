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
     * @param  InputInterface $oInput The Input Interface provided by Symfony
     * @param  OutputInterface $oOutput The Output Interface provided by Symfony
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

        //  Setup Factory - config files are required prior to set up
        Factory::setup();

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
        $dbHost = $oInput->getOption('dbHost') ?: (defined('DEPLOY_DB_HOST') ? DEPLOY_DB_HOST : '');
        $dbUser = $oInput->getOption('dbUser') ?: (defined('DEPLOY_DB_USERNAME') ? DEPLOY_DB_USERNAME : '');
        $dbPass = $oInput->getOption('dbPass') ?: (defined('DEPLOY_DB_PASSWORD') ? DEPLOY_DB_PASSWORD : '');
        $dbName = $oInput->getOption('dbName') ?: (defined('DEPLOY_DB_DATABASE') ? DEPLOY_DB_DATABASE : '');

        //  Check we have a database to connect to
        if (empty($dbName)) {
            return $this->abort(static::EXIT_CODE_NO_DB);
        }

        //  Get the DB object
        $this->oDb = Factory::service('ConsoleDatabase', 'nailsapp/module-console');
        $this->oDb->connect($dbHost, $dbUser, $dbPass, $dbName);

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

        //  Work out what Nails is doing, `common` won't be detected as a module
        $oOutput->write('<comment>Determining state of the Nails tables... </comment>');

        $nails = $this->determineModuleState('nailsapp/common', 'vendor/nailsapp/common/migrations/');

        if ($nails) {
            $oOutput->writeln('done, requires migration');
        } else {
            $oOutput->writeln('done, no Nails migrations detected');
        }

        // --------------------------------------------------------------------------

        //  Look for enabled modules
        $oOutput->write('<comment>Determining state of the module tables... </comment>');
        $enabledModules = $this->findEnabledModules();

        if ($enabledModules) {

            if (count($enabledModules) == 1) {
                $oOutput->writeln('found <info>1</info> module requiring migration');
            } else {
                $oOutput->writeln('found <info>' . count($enabledModules) . '</info> modules requiring migration');
            }

        } else {

            $oOutput->writeln('no modules found which require migration');
        }

        // --------------------------------------------------------------------------

        //  Work out what the App's doing
        $oOutput->write('<comment>Determining state of the App tables... </comment>');

        $app = $this->determineModuleState('APP', 'application/migrations/');

        if ($app) {
            $oOutput->writeln('done, requires migration');
        } else {
            $oOutput->writeln('done, no App migrations detected');
        }

        // --------------------------------------------------------------------------

        //  Anything to migrate?
        if (!$nails && !$enabledModules && !$app) {
            $oOutput->writeln('');
            $oOutput->writeln('Nothing to migrate');
            $oOutput->writeln('');

            return self::EXIT_CODE_SUCCESS;
        }

        // --------------------------------------------------------------------------

        //  Confirm what's going to happen
        $oOutput->writeln('');
        $oOutput->writeln('OK, here\'s what\'s going to happen:');

        if ($nails) {
            $oOutput->writeln('');
            $start = is_null($nails->start) ? 'The beginning of time' : $nails->start;
            $oOutput->writeln('Nails\' database will be migrated from <info>#' . $start . '</info> to <info>#' . $nails->end . '</info>');
        }

        if ($enabledModules) {

            $oOutput->writeln('');
            $oOutput->writeln('The following modules are to be migrated:');

            foreach ($enabledModules as $module) {

                $start = is_null($module->start) ? 'The beginning of time' : $module->start;

                $line = ' - <comment>' . $module->name . '</comment> from ';
                $line .= '<info>#' . $start . '</info> to <info>#' . $module->end . '</info>';

                $oOutput->writeln($line);
            }
        }

        if ($app) {

            $oOutput->writeln('');
            $start = is_null($app->start) ? 'The beginning of time' : $app->start;
            $oOutput->writeln('The App\'s database will be migrated from <info>#' . $start . '</info> to <info>#' . $app->end . '</info>');
        }

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
        $curStep       = 1;
        $numMigrations = 0;
        $numMigrations += !empty($nails) ? 1 : 0;
        $numMigrations += !empty($enabledModules) ? count($enabledModules) : 0;
        $numMigrations += !empty($app) ? 1 : 0;

        //  Disable foreign key checks
        $result              = $this->oDb->query('SHOW Variables WHERE Variable_name=\'FOREIGN_KEY_CHECKS\'')->fetch(\PDO::FETCH_OBJ);
        $oldForeignKeychecks = $result->Value;

        $this->oDb->query('SET FOREIGN_KEY_CHECKS = 0;');

        //  Migrate nails
        if (!empty($nails)) {

            $oOutput->write('[' . $curStep . '/' . $numMigrations . '] Migrating <info>Nails</info>... ');
            $result = $this->doMigration($nails);

            if ($result) {
                $oOutput->writeln('done!');
            } else {
                return $this->abort(static::EXIT_CODE_MIGRATION_FAILED);
            }

            $curStep++;
        }

        //  Migrate the modules
        if (!empty($enabledModules)) {

            foreach ($enabledModules as $module) {

                $oOutput->write('[' . $curStep . '/' . $numMigrations . '] Migrating <info>' . $module->name . '</info>... ');
                $result = $this->doMigration($module);

                if ($result) {

                    $oOutput->writeln('done!');

                } else {

                    return $this->abort(static::EXIT_CODE_MODULE_MIGRATION_FAILED);
                }

                $curStep++;
            }
        }

        //  Migrate the app
        if (!empty($app)) {

            $oOutput->write('[' . $curStep . '/' . $numMigrations . '] Migrating <info>App</info>... ');
            $result = $this->doMigration($app);

            if ($result) {

                $oOutput->writeln('done!');

            } else {

                return $this->abort(static::EXIT_CODE_APP_MIGRATION_FAILED);
            }
        }

        // --------------------------------------------------------------------------

        //  Commit the transaction
        $this->oDb->transactionCommit();

        // --------------------------------------------------------------------------

        //  Cleaning up
        $oOutput->writeln('<comment>Cleaning up...</comment>');

        //  Restore previous foreign key checks
        $this->oDb->query('SET FOREIGN_KEY_CHECKS = \'' . $oldForeignKeychecks . '\';');

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
     * @param string $moduleName The module's name
     * @param string $migrationsPath The module's path
     * @return null|\stdClass stdClass when migration needed, null when not needed
     */
    protected function determineModuleState($moduleName, $migrationsPath)
    {
        $module        = new \stdClass();
        $module->name  = $moduleName;
        $module->start = null;
        $module->end   = null;

        // --------------------------------------------------------------------------

        //  Work out if the module needs migrated and if so between what and what
        $dirMap = $this->mapDir($migrationsPath);

        if (!empty($dirMap)) {

            //  Work out all the files we have and get their index
            $migrations = [];
            foreach ($dirMap as $dir) {
                $migrations[$dir['path']] = [
                    'index' => $dir['index'],
                    'type'  => $dir['type'],
                ];
            }

            // --------------------------------------------------------------------------

            //  Work out the current version
            $sql    = "SELECT `version` FROM `" . NAILS_DB_PREFIX . "migration` WHERE `module` = '$moduleName';";
            $result = $this->oDb->query($sql);

            if ($result->rowCount() === 0) {

                //  Add a row for the module
                $sql = "INSERT INTO `" . NAILS_DB_PREFIX . "migration` (`module`, `version`) VALUES ('$moduleName', NULL);";
                $this->oDb->query($sql);

                $currentVersion = null;

            } else {

                $currentVersion = $result->fetch(\PDO::FETCH_OBJ)->version;
                $currentVersion = is_null($currentVersion) ? null : (int) $currentVersion;
            }

            // --------------------------------------------------------------------------

            //  Define the variable
            $lastMigration = end($migrations);
            $module->start = $currentVersion;
            $module->end   = $lastMigration['index'];
        }

        return $module->start === $module->end ? null : $module;
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
     * @param  \stdClass $module The migration details object
     * @return boolean
     */
    protected function doMigration($module)
    {
        $oOutput = $this->oOutput;
        
        // --------------------------------------------------------------------------
        
        //  Map the directory and fetch only the files we need
        $path   = $module->name == 'APP' ? 'application/migrations/' : 'vendor/' . $module->name . '/migrations/';
        $dirMap = $this->mapDir($path);

        //  Set the current version to -1 if null so migrations with a zero index are picked up
        $current = is_null($module->start) ? -1 : $module->start;

        //  Go through all the migrations, skip any which have already been executed
        $lastMigration = null;
        foreach ($dirMap as $migration) {

            if ($migration['index'] > $current) {

                switch ($migration['type']) {

                    case 'SQL':
                        if (!$this->migrateSql($module, $migration)) {
                            return false;
                        }
                        break;

                    case 'PHP':
                        if (!$this->migratePhp($module, $migration)) {
                            return false;
                        }
                        break;
                }
            }

            $lastMigration = $migration['index'];
        }

        //  Update the database
        $sql    = "UPDATE `" . NAILS_DB_PREFIX . "migration` SET `version` = " . $lastMigration . " WHERE `module` = '" . $module->name . "'";
        $result = $this->oDb->query($sql);

        if (!$result) {

            // Error updating migration record
            $oOutput->writeln('');
            $oOutput->writeln('');
            $oOutput->writeln('<error>ERROR</error>: Failed to update migration record for <info>' . $module->name . '</info>.');

            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Executes migrations on a SQL File where each line is a query
     *
     * @param  object $module The module being migrated
     * @param  array $migration The migration details
     * @return boolean
     */
    private function migrateSql($module, $migration)
    {
        $oOutput = $this->oOutput;

        //  Go through the file and execute each line.
        $handle = fopen($migration['path'], 'r');

        if ($handle) {

            $lineNumber = 1;
            while (($line = fgets($handle)) !== false) {

                //  Remove comments
                $line = trim($line);
                $line = preg_replace('#^//.+$#', '', $line);
                $line = preg_replace('/^#.+$/', '', $line);
                $line = preg_replace('/^--.+$/', '', $line);
                $line = preg_replace('#/\*.*\*/#', '', $line);
                $line = trim($line);

                //  Replace {{NAILS_DB_PREFIX}} with the constant
                $line = str_replace('{{NAILS_DB_PREFIX}}', NAILS_DB_PREFIX, $line);
                if (defined('APP_DB_PREFIX')) {
                    $line = str_replace('{{APP_DB_PREFIX}}', APP_DB_PREFIX, $line);
                }

                if (!empty($line)) {

                    //  We have something!
                    try {
                        $this->oDb->query($line);
                    } catch (\Exception $e) {
                        $oOutput->writeln('<error>ERROR</error>: Migration at "' . $migration['path'] . '" failed:');
                        $oOutput->writeln('<error>ERROR</error>: #' . $e->getCode() . ' - ' . $e->getMessage());
                        $oOutput->writeln('<error>ERROR</error>: Failed Query: #' . $lineNumber);
                        $oOutput->writeln('<error>ERROR</error>: Failed Query: ' . $line);

                        return false;
                    }
                }

                $lineNumber++;
            }

            return true;

        } else {

            // error opening the file.
            $oOutput->writeln('');
            $oOutput->writeln('');
            $oOutput->writeln('<error>ERROR</error>: Failed to open <info>' . $migration['path'] . '</info> for reading.');

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Executes migrations on a PHP which extends Nails\Common\Migration\Base
     *
     * @param  object $module The module being migrated
     * @param  array $migration The migration details
     * @return boolean
     */
    private function migratePhp($module, $migration)
    {
        $oOutput = $this->oOutput;

        require_once $migration['path'];

        //  Generate the expected class name, i.e., "vendor-name/package-name" -> VendorName\PackageName"
        $sPattern    = '[^a-zA-Z0-9' . preg_quote(DIRECTORY_SEPARATOR, '/\-') . ']';
        $sModuleName = strtolower($module->name);
        $sModuleName = preg_replace('/' . $sPattern . '/', ' ', $sModuleName);
        $sModuleName = str_replace(DIRECTORY_SEPARATOR, ' ' . DIRECTORY_SEPARATOR . ' ', $sModuleName);
        $sModuleName = ucwords($sModuleName);
        $sModuleName = str_replace(' ', '', $sModuleName);
        $sModuleName = str_replace(DIRECTORY_SEPARATOR, '\\', $sModuleName);

        $sClassName = 'Nails\Database\Migration\\' . $sModuleName . '\Migration' . $migration['index'];

        if (class_exists($sClassName)) {

            $oMigration = new $sClassName();

            if (is_subclass_of($oMigration, 'Nails\Common\Console\Migrate\Base')) {

                try {

                    $oMigration->execute();

                } catch (\Exception $e) {

                    $oOutput->writeln('');
                    $oOutput->writeln('');
                    $oOutput->writeln('<error>ERROR</error>: Migration at "' . $migration['path'] . '" failed:');
                    $oOutput->writeln('<error>ERROR</error>: #' . $e->getCode() . ' - ' . $e->getMessage());
                    $oOutput->writeln('<error>ERROR</error>: Failed Query: #' . $oMigration->getQueryCount());
                    $oOutput->writeln('<error>ERROR</error>: Failed Query: ' . $oMigration->getLastQuery());

                    return false;
                }

            } else {

                $oOutput->writeln('');
                $oOutput->writeln('');
                $oOutput->writeln('<error>ERROR</error>: Migration at "' . $migration['path'] . '" is badly configured.');
                $oOutput->writeln('<error>ERROR</error>: Should be a sub-class of "Nails\Common\Console\Migrate\Base".');

                return false;
            }

            unset($oMigration);

        } else {

            $oOutput->writeln('');
            $oOutput->writeln('');
            $oOutput->writeln('<error>ERROR</error>: Class "' . $sClassName . '" does not exist.');
            $oOutput->writeln('<error>ERROR</error>: Migration at "' . $migration['path'] . '" is badly configured.');

            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Generates an array of files in a directory
     *
     * @param  string $dir The directory to analyse
     * @return array
     */
    private function mapDir($dir)
    {
        if (is_dir($dir)) {

            $out = [];

            foreach (new \DirectoryIterator($dir) as $fileInfo) {

                if ($fileInfo->isDot()) {
                    continue;
                }

                //  In the correct format?
                if (preg_match('/^(\d+)(.*)\.(sql|php)$/', $fileInfo->getFilename(), $matches)) {

                    $out[$matches[1]] = [
                        'path'  => $fileInfo->getPathname(),
                        'index' => (int) $matches[1],
                        'type'  => strtoupper($fileInfo->getExtension()),
                    ];
                }
            }

            ksort($out);

            return $out;

        } else {

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Performs the abort functionality and returns the exit code
     *
     * @param  array $aMessages The error message
     * @param  integer $iExitCode The exit code
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
