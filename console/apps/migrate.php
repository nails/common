<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

require_once 'vendor/nailsapp/common/console/apps/_app.php';

//  Define FCPATH so CORE_NAILS_Common doesn't freak out
if (!defined('FCPATH')) {

    define('FCPATH', './');
}

require_once 'vendor/nailsapp/common/core/CORE_NAILS_Common.php';
require_once 'vendor/nailsapp/common/core/CORE_NAILS_Traits.php';

class CORE_NAILS_Migrate extends CORE_NAILS_App
{
    /**
     * Configures the app
     * @return void
     */
    protected function configure()
    {
        $this->setName('migrate');
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
     * @param  InputInterface  $input  The Input Interface provided by Symfony
     * @param  OutputInterface $output The Output Interface provided by Symfony
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>-----------------------------</info>');
        $output->writeln('<info>Nails Database Migration Tool</info>');
        $output->writeln('<info>-----------------------------</info>');

        // --------------------------------------------------------------------------

        //  Load configs
        if (!file_exists('config/deploy.php')) {

            $output->writeln('<error>ERROR:</error> Could not load config/deploy.php.');
            return false;
        }

        require_once 'config/deploy.php';

        if (!defined('ENVIRONMENT')) {

            $output->writeln('<error>ERROR:</error> ENVIRONMENT is not defined.');
            return false;
        }

        // --------------------------------------------------------------------------

        //  Check environment
        if (strtoupper(ENVIRONMENT) == 'PRODUCTION') {

            $output->writeln('');
            $output->writeln('--------------------------------------');
            $output->writeln('| <info>WARNING: The app is in PRODUCTION.</info> |');
            $output->writeln('--------------------------------------');
            $output->writeln('');

            if (!$this->confirm('Continue with migration?', true, $input, $output)) {

                return $this->abort($output, 1);
            }
        }

        // --------------------------------------------------------------------------

        //  Work out the DB credentials to use
        $dbHost = $input->getOption('dbHost');
        if (empty($dbHost)) {

            //  Try the constant
            $dbHost = defined('DEPLOY_DB_HOST') ? DEPLOY_DB_HOST : '';
        }

        $dbUser = $input->getOption('dbUser');
        if (empty($dbUser)) {

            //  Try the constant
            $dbUser = defined('DEPLOY_DB_USERNAME') ? DEPLOY_DB_USERNAME : '';
        }

        $dbPass = $input->getOption('dbPass');
        if (empty($dbPass)) {

            //  Try the constant
            $dbPass = defined('DEPLOY_DB_PASSWORD') ? DEPLOY_DB_PASSWORD : '';
        }

        $dbName = $input->getOption('dbName');
        if (empty($dbName)) {

            //  Try the constant
            $dbName = defined('DEPLOY_DB_DATABASE') ? DEPLOY_DB_DATABASE : '';
        }

        //  Check we have a database to connect to
        if (empty($dbName)) {

            return $this->abort($output, 2);
        }

        //  Connect to the Database
        if (!$this->dbConnect($output, $dbHost, $dbUser, $dbPass, $dbName)) {

            return $this->abort($output, 3);
        }

        //  Test the db
        if (!$this->dbMigrationTest()) {

            $output->writeln('');
            $output->writeln('Database isn\'t ready for migrations.');
            return $this->abort($output, 4);
        }

        // --------------------------------------------------------------------------

        $output->writeln('');

        // --------------------------------------------------------------------------

        //  Work out what Nails is doing, `common` won't be detected as a module
        $output->write('<comment>Determining state of the Nails database... </comment>');

        $nails = $this->determineModuleState('nailsapp/common', 'vendor/nailsapp/common/migrations/');

        if ($nails) {

            $output->writeln('done, requires migration');

        } else {

            $output->writeln('done, no Nails migrations detected');
        }

        // --------------------------------------------------------------------------

        //  Look for enabled modules
        $output->write('<comment>Searching for modules... </comment>');
        $enabledModules = $this->findEnabledModules();

        if ($enabledModules) {

            if (count($enabledModules) == 1) {

                $output->writeln('found <info>1</info> module requiring migration');

            } else {

                $output->writeln('found <info>' . count($enabledModules) . '</info> modules requiring migration');
            }

        } else {

            $output->writeln('no modules found which require migration');
        }

        // --------------------------------------------------------------------------

        //  Work out what the App's doing
        $output->write('<comment>Determining state of the App database... </comment>');

        $app = $this->determineModuleState('APP', 'application/migrations/');

        if ($app) {

            $output->writeln('done, requires migration');

        } else {

            $output->writeln('done, no App migrations detected');
        }

        // --------------------------------------------------------------------------

        //  Anything to migrate?
        if (!$nails && !$enabledModules && !$app) {

            $output->writeln('');
            $output->writeln('Nothing to migrate');
            return $this->abort($output, 0);
        }

        // --------------------------------------------------------------------------

        //  Confirm what's going to happen
        $output->writeln('');
        $output->writeln('OK, here\'s what\'s going to happen:');

        if ($nails) {

            $output->writeln('');
            $start = is_null($nails->start) ? 'The beginning of time' : $nails->start;
            $output->writeln('Nails\' database will be migrated from <info>' . $start . '</info> to <info>#' . $nails->end . '</info>');
        }

        if ($enabledModules) {

            $output->writeln('');
            $output->writeln('The following modules are to be migrated:');

            foreach ($enabledModules as $module) {

                $start = is_null($module->start) ? 'The beginning of time' : $module->start;

                $line  = ' - <comment>' . $module->name . '</comment> from ';
                $line .= '<info>' . $start . '</info> to <info>#' . $module->end . '</info>';

                $output->writeln($line);
            }
        }

        if ($app) {

            $output->writeln('');
            $start = is_null($app->start) ? 'The beginning of time' : $app->start;
            $output->writeln('The App\'s database will be migrated from <info>' . $start . '</info> to <info>#' . $app->end . '</info>');
        }

        $output->writeln('');

        if (!$this->confirm('Continue?', true, $input, $output)) {

            return $this->abort($output, 5);
        }

        // --------------------------------------------------------------------------

        $output->writeln('');
        $output->writeln('<comment>Starting migration...</comment>');

        // --------------------------------------------------------------------------

        //  Start the DB transaction
        $this->dbTransactionStart();

        // --------------------------------------------------------------------------

        //  Start migrating
        $curStep        = 1;
        $numMigrations  = 0;
        $numMigrations += !empty($nails) ? 1 : 0;
        $numMigrations += !empty($enabledModules) ? count($enabledModules) : 0;
        $numMigrations += !empty($app) ? 1 : 0;

        //  Disable foreign key checks
        $result = $this->dbQuery('SHOW Variables WHERE Variable_name=\'FOREIGN_KEY_CHECKS\'')->fetch(\PDO::FETCH_OBJ);
        $oldForeignKeychecks = $result->Value;

        $this->dbQuery('SET FOREIGN_KEY_CHECKS = 0;');

        //  Migrate nails
        if (!empty($nails)) {

            $output->write('[' . $curStep . '/' . $numMigrations . '] Migrating <info>Nails</info>... ');
            $result = $this->doMigration($nails, $output);

            if ($result) {

                $output->writeln('done!');

            } else {

                return $this->abort($output, 6);
            }

            $curStep++;
        }

        //  Migrate the modules
        if (!empty($enabledModules)) {

            foreach ($enabledModules as $module) {

                $output->write('[' . $curStep . '/' . $numMigrations . '] Migrating <info>' . $module->name . '</info>... ');
                $result = $this->doMigration($module, $output);

                if ($result) {

                    $output->writeln('done!');

                } else {

                    return $this->abort($output, 7);
                }

                $curStep++;
            }
        }

        //  Migrate the app
        if (!empty($app)) {

            $output->write('[' . $curStep . '/' . $numMigrations . '] Migrating <info>App</info>... ');
            $result = $this->doMigration($app, $output);

            if ($result) {

                $output->writeln('done!');
                $curStep++;

            } else {

                return $this->abort($output, 8);
            }

            $curStep++;
        }

        // --------------------------------------------------------------------------

        //  Commit the transaction
        $this->dbTransactionCommit();

        // --------------------------------------------------------------------------

        //  Cleaning up
        $output->writeln('<comment>Cleaning up...</comment>');

        //  Restore previous foreign key checks
        $this->dbQuery('SET FOREIGN_KEY_CHECKS = \'' . $oldForeignKeychecks . '\';');

        //  Disconnect from the DB
        $this->dbClose();

        // --------------------------------------------------------------------------

        //  And we're done
        $output->writeln('');
        $output->writeln('Complete!');
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether or not the module needs to migration, and if so between what versions
     * @return mixed stdClass when migration needed, null when not needed
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
            $migrations = array();
            foreach ($dirMap as $dir) {

                $migrations[$dir['path']] = $dir['index'];
            }

            // --------------------------------------------------------------------------

            //  Work out the current version
            $sql = "SELECT `version` FROM `" . NAILS_DB_PREFIX . "migration` WHERE `module` = '$moduleName';";
            $result = $this->dbQuery($sql);

            if ($result->rowCount() === 0) {

                //  Add a row for the module
                $sql = "INSERT INTO `" . NAILS_DB_PREFIX . "migration` (`module`, `version`) VALUES ('$moduleName', NULL);";
                $this->dbQuery($sql);

                $currentVersion = null;

            } else {

                $currentVersion = $result->fetch(\PDO::FETCH_OBJ)->version;
                $currentVersion = is_null($currentVersion) ? null : (int) $currentVersion;
            }

            // --------------------------------------------------------------------------

            //  Define the variable
            $module->start = $currentVersion;
            $module->end   = end($migrations);
        }

        return $module->start === $module->end ? null : $module;
    }

    // --------------------------------------------------------------------------

    /**
     * Looks for enabled Nails modules which support migration
     * @return array
     */
    protected function findEnabledModules()
    {
        //  Unset the global so we get a fresh look at what's available
        if (isset($GLOBALS['NAILS_COMPONENTS'])) {

            unset($GLOBALS['NAILS_COMPONENTS']);
        }

        //  Look for modules
        $modules = _NAILS_GET_MODULES();
        $out     = array();

        foreach ($modules as $module) {

            $out[] = $this->determineModuleState($module->name, $module->path . '/migrations/');
        }

        return array_filter($out);
    }

    // --------------------------------------------------------------------------

    /**
     * Executes a migration
     * @param  string          $module The migration details object
     * @param  OutputInterface $output The Output Interface provided by Symfony
     * @return boolean
     */
    protected function doMigration($module, $output)
    {
        //  Map the directory and fetch only the files we need
        $path       = $module->name == 'APP' ? 'application/migrations/' : 'vendor/' . $module->name . '/migrations/';
        $dirMap     = $this->mapDir($path);

        //  Set the current version to -1 if null so migrations with a zero index are picked up
        $current = is_null($module->start) ? -1 : $module->start;

        //  Go through all the migrations, skip any which have already been executed
        $lastMigration = null;
        foreach ($dirMap as $migration) {

            if ($migration['index'] > $current) {

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

                        if (!empty($line)) {

                            //  We have something!
                            $result = $this->dbQuery($line);

                            if (!$result) {

                                $output->writeln('');
                                $output->writeln('');
                                $output->writeln('<error>ERROR</error>: Query in <info>' . $migration['path'] . '</info> on line <info>' . $lineNumber . '</info> failed:');
                                $output->writeln('');
                                $output->writeln('<comment>' . $line . '</comment>');
                                return false;
                            }
                        }

                        $lineNumber++;
                    }

                } else {

                    // error opening the file.
                    $output->writeln('');
                    $output->writeln('');
                    $output->writeln('<error>ERROR</error>: Failed to open <info>' . $migration['path'] . '</info> for reading.');
                    return false;
                }
            }

            $lastMigration = $migration['index'];
        }

        //  Update the database
        $sql = "UPDATE `" . NAILS_DB_PREFIX . "migration` SET `version` = " . $lastMigration . " WHERE `module` = '" . $module->name . "'";
        $result = $this->dbQuery($sql);

        if (!$result) {

            // Error updating migration record
            $output->writeln('');
            $output->writeln('');
            $output->writeln('<error>ERROR</error>: Failed to update migration record for <info>' . $module->name . '</info>.');
            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Generates an array of files in a directory
     * @param  string $dir The directory to analyse
     * @return array
     */
    private function mapDir($dir)
    {
        if (is_dir($dir)) {

            $out = array();

            foreach (new \DirectoryIterator($dir) as $fileInfo) {

                if ($fileInfo->isDot()) {

                    continue;
                }

                //  In the correct format?
                if ( preg_match('/^(\d)+(.*)\.sql$/', $fileInfo->getFilename(), $matches)) {

                    $out[$matches[1]] = array(
                        'path'  => $fileInfo->getPathname(),
                        'index' => (int) $matches[1]
                    );
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
     * @param  OutputInterface $output   The Output Interface provided by Symfony
     * @param  integer         $exitCode The exit code
     * @return int
     */
    private function abort($output, $exitCode = 0)
    {
        $output->writeln('');

        $colorOpen  = $exitCode === 0 ? '' : '<error>';
        $colorClose = $exitCode === 0 ? '' : '</error>';

        if ($this->dbTransRunning) {

            $output->writeln($colorOpen . 'Rolling back Database' . $colorClose);
            $this->dbTransactionRollback();
        }

        $output->writeln($colorOpen . 'Aborting migration' . $colorClose);
        $output->writeln('');
        return $exitCode;
    }
}
