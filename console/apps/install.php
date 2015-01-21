<?php

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

require_once 'vendor/nailsapp/common/console/apps/_app.php';

//  Define the nails path so CORE_NAILS_Common doesn't freak out
if (!defined('NAILS_PATH')) {

    define('NAILS_PATH', 'vendor/nailsapp/');
}

require_once 'vendor/nailsapp/common/core/CORE_NAILS_Common.php';

/**
 * Load the password model, so we can use it's static methods to generate
 * the user's passwords. We will have to immitate CI's Model Classthough.
 */

class CI_Model {}

class CORE_NAILS_Install extends CORE_NAILS_App
{
    /**
     * Configures the app
     * @return void
     */
    protected function configure()
    {
        $this->setName('install');
        $this->setDescription('Configures or reconfigures a Nails site');

        $this->addArgument(
            'moduleName',
            InputArgument::OPTIONAL,
            'If a module name is provided it will be added to composer.json if valid'
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
        $module = $input->getArgument('moduleName');

        if (!empty($module)) {

            return $this->executeModuleInstaller($module, $input, $output);

        } else {

            return $this->executeInstaller($input, $output);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the Nails Installer
     * @param  InputInterface  $input  The Input Interface provided by Symfony
     * @param  OutputInterface $output The Output Interface provided by Symfony
     * @return void
     */
    protected function executeInstaller(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>---------------</info>');
        $output->writeln('<info>Nails Installer</info>');
        $output->writeln('<info>---------------</info>');
        $output->writeln('Beginning...');

        // --------------------------------------------------------------------------

        //  Load configs
        if (file_exists('config/app.php')) {

            $output->writeln('Found <comment>config/app.php</comment> will use values for defaults');
            require_once 'config/app.php';
        }

        if (file_exists('config/deploy.php')) {

            $output->writeln('Found <comment>config/deploy.php</comment> will use values for defaults');
            require_once 'config/deploy.php';
        }

        // --------------------------------------------------------------------------

        //  Define app & deploy vars
        $appVars    = $this->defineAppVars();
        $deployVars = $this->defineDeployVars();

        // --------------------------------------------------------------------------

        //  Pre-Install tests
        $preTestErrors = $this->preInstallTests();

        // --------------------------------------------------------------------------

        if (empty($preTestErrors)) {

            $output->writeln('');
            $output->writeln('<info>App Settings</info>');
            $this->setVars($appVars, $input, $output);

            // --------------------------------------------------------------------------

            $output->writeln('');
            $output->writeln('<info>Deploy Settings</info>');
            $this->setVars($deployVars, $input, $output);

            // --------------------------------------------------------------------------

            //  Can we install modules? We need exec() and composer to be available
            $execAvailable     = function_exists('exec');
            $composerAvailable = (bool) $this->detectComposerBin();

            if ($execAvailable && $composerAvailable) {

                $output->writeln('');
                $output->writeln('<info>Modules</info>');

                $question = 'Would you like to define modules to enable now?';
                $installModules = $this->confirm($question, false, $input, $output);

                $installTheseModules = array();

                while ($installModules) {

                    $module = $this->requestModule('', $input, $output);
                    $installTheseModules[$module[0]] = $module[1];

                    $output->writeln('');
                    $question = 'Would you like to enable another module?';
                    $installModules = $this->confirm($question, false, $input, $output);
                }
            }

            // --------------------------------------------------------------------------

            $output->writeln('');
            $output->writeln('<info>Users</info>');

            $question   = 'Would you like to create some users?';
            $createUser = $this->confirm($question, false, $input, $output);
            $users      = array();

            $userFields = array();
            $userField['first_name'] = 'First Name';
            $userField['last_name']  = 'Surname';
            $userField['email']      = 'Email Address';
            $userField['username']   = 'Username';
            $userField['password']   = 'Password';

            if ($createUser) {

                do {

                    $temp       = array();
                    $userCount  = count($users) + 1;

                    $output->writeln('');
                    $output->writeln('User #' . $userCount);

                    foreach ($userField as $key => $label) {

                        do {

                            $temp[$key] = $this->ask($label, '', $input, $output);

                        } while (empty($temp[$key]));
                    }

                    $users[] = $temp;

                    $output->writeln('');

                    $question = 'Create another user?';
                    $createUser = $this->confirm($question, false, $input, $output);

                    if (!$createUser) {

                        $output->writeln('');
                    }

                } while ($createUser);
            }

            // --------------------------------------------------------------------------

            //  Tell user what's about to happen
            $output->writeln('<info>I\'m about to do the following:</info>');

            //  app.php
            $output->writeln('');
            $output->writeln('Write <info>config/app.php</info>');

            foreach ($appVars as &$v) {

                if (is_string($v)) {

                    continue;
                }

                $output->writeln(' - Set <comment>' . $v['label'] . '</comment> to <comment>' . $v['value'] . '</comment>');
            }

            //  deploy.php
            $output->writeln('');
            $output->writeln('Write <info>config/deploy.php</info>');

            foreach ($deployVars as &$v) {

                if (is_string($v)) {

                    continue;
                }

                $output->writeln(' - Set <comment>' . $v['label'] . '</comment> to <comment>' . $v['value'] . '</comment>');
            }

            //  Install modules
            if (!empty($installTheseModules)) {

                $output->writeln('');

                if (count($installTheseModules) > 1) {

                    $output->writeln('The following modules will be installed:');

                } else {

                    $output->writeln('The following module will be installed:');
                }

                foreach ($installTheseModules as $moduleName => $moduleVersion) {

                    $output->writeln(' - <comment>' . $moduleName . ':' . $moduleVersion . '</comment>');
                }
            }

            //  Migrate databases
            $output->writeln('');
            $output->writeln('Migrate the database');

            //  Add users
            if ($users) {

                $output->writeln('');

                if (count($users) == 1) {

                    $output->writeln('Add user <info>' . $users[0]['first_name'] . ' ' . $users[0]['last_name'] . '</info>');

                } else {

                    $output->writeln('Add <info>' . count($users) . '</info> users');
                }
            }

            $output->writeln('');
            $question = 'Does this look OK?';
            $doInstall = $this->confirm($question, true, $input, $output);

            if ($doInstall) {


                $curStep   = 1;
                $numSteps  = 1; //  app.php
                $numSteps += 1; //  deploy.php
                $numSteps += !empty($installTheseModules)? 1 : 0;
                $numSteps += 1; //  migrate DB.php
                $numSteps += !empty($users)? 1 : 0;

                $output->writeln('');
                $output->writeln('<info>Installing...</info>');

                //  Write app.php
                $output->write('<comment>[' . $curStep . '/' . $numSteps . ']</comment> Writing <info>config/app.php</info>... ');
                if ($this->writeFile($appVars, 'config/app.php')) {

                    $output->writeln('<info>DONE</info>');

                } else {

                    $output->writeln('<error>FAILED</error>');
                }
                $curStep++;

                //  Write deploy.php
                $output->write('<comment>[' . $curStep . '/' . $numSteps . ']</comment> Writing <info>config/deploy.php</info>... ');
                if ($this->writeFile($deployVars, 'config/deploy.php')) {

                    $output->writeln('<info>DONE</info>');

                } else {

                    $output->writeln('<error>FAILED</error>');
                }
                $curStep++;

                //  Install Modules
                if (!empty($installTheseModules)) {

                    $output->writeln('<comment>[' . $curStep . '/' . $numSteps . ']</comment> Installing modules</info>...');

                    foreach ($installTheseModules as $moduleName => $moduleVersion) {

                        $output->write(' - <comment>' . $moduleName . ':' . $moduleVersion . '</comment>... ');
                        $this->installModule($moduleName, $moduleVersion, $output);
                    }
                    $curStep++;
                }

                //  Migrate DB
                $output->write('<comment>[' . $curStep . '/' . $numSteps . ']</comment> Migrating database... ');
                $this->migrateDb($output);
                $curStep++;

                //  Add Uers
                if (!empty($users)) {

                    $output->writeln('<comment>[' . $curStep . '/' . $numSteps . ']</comment> Creating Users</info>...');

                    if ($this->dbConnect($output)) {

                        foreach ($users as $user) {

                            $output->write(' - <comment>' . $user['first_name'] . ' ' . $user['last_name'] . '</comment>... ');
                            $result = $this->createUser($user, $appVars, $deployVars, $output);

                            if ($result === true) {

                                $output->writeln('<info>DONE</info>');

                            } else {

                                $output->writeln('<error>FAILED: ' . $result . '</error>');
                            }
                        }

                        $this->dbClose();

                    } else {

                        $output->writeln('<error>FAILED</error>');
                    }
                    $curStep++;
                }

                // --------------------------------------------------------------------------

                //  Cleaning up
                $output->writeln('');
                $output->writeln('<comment>Cleaning up...</comment>');

                //  And we're done!
                $output->writeln('');
                $output->writeln('Complete!');

            } else {

                return $this->abort($output);
            }

        } else {

            $exitCode = $this->abort($output);

            foreach ($preTestErrors as $error) {

                $output->writeln(' - ' . $error);
            }

            return $exitCode;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the Nails Module Installer
     * @param  InputInterface  $input  The Input Interface provided by Symfony
     * @param  OutputInterface $output The Output Interface provided by Symfony
     * @return void
     */
    protected function executeModuleInstaller($module, InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>----------------------</info>');
        $output->writeln('<info>Nails Module Installer</info>');
        $output->writeln('<info>----------------------</info>');

        // --------------------------------------------------------------------------

        //  Get the Module
        $module = $this->requestModule($module, $input, $output);

        //  confirm with user
        $output->writeln('');
        $output->writeln('<info>I\'m about to do the following:</info>');
        $output->writeln(' - Install <info>' . $module[0] . ':' . $module[1] . '</info>');
        $output->writeln(' - Migrate the database');
        $output->writeln('');

        $question = 'Continue?';
        $doInstall = $this->confirm($question, true, $input, $output);

        if ($doInstall) {

            //  Attempt to install
            $output->writeln('');
            $output->write('<comment>[1/2]</comment> Installing <info>' . $module[0] . ':' . $module[1] . '</info>... ');
            if (!$this->installModule($module[0], $module[1], $output)) {

                return $this->abort($output, 3);
            }

            //  Migrate DB
            $output->write('<comment>[2/2]</comment> Migrating database... ');
            if (!$this->migrateDb($output)) {

                return $this->abort($output, 4);
            }

            //  Cleaning up
            $output->writeln('');
            $output->writeln('<comment>Cleaning up...</comment>');

            //  And we're done!
            $output->writeln('');
            $output->writeln('Complete!');

        } else {

            return $this->abort($output, 0);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Defines all the App vars and their defaults
     * @return array
     */
    private function defineAppVars()
    {
        $vars   = array();
        $vars[] = '// App Constants';
        $vars[] = array(
                    'key'       => 'APP_NAME',
                    'label'     => 'App Name',
                    'value'     => defined('APP_NAME') ? APP_NAME : 'My App',
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'APP_DEFAULT_TIMEZONE',
                    'label'     => 'App Timezone',
                    'value'     => defined('APP_DEFAULT_TIMEZONE') ? APP_DEFAULT_TIMEZONE : date_default_timezone_get(),
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'APP_PRIVATE_KEY',
                    'label'     => 'App Private Key',
                    'value'     => defined('APP_PRIVATE_KEY') ? APP_PRIVATE_KEY : md5(rand(0, 1000) . microtime(true)),
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'APP_DEVELOPER_EMAIL',
                    'label'     => 'Developer Email',
                    'value'     => defined('APP_DEVELOPER_EMAIL') ? APP_DEVELOPER_EMAIL : '',
                    'options'   => array()
                );

        // --------------------------------------------------------------------------

        //  Any constants defined by the modules?
        $moduleVars = $this->getConstantsFromModules('APP', $vars);
        $vars = array_merge($vars, $moduleVars);

        // --------------------------------------------------------------------------

        //  Any other constants defined in app.php?
        $appFile = $this->getConstantsFromFile('config/app.php', $vars);
        $vars = array_merge($vars, $appFile);

        // --------------------------------------------------------------------------

        return $vars;
    }

    // --------------------------------------------------------------------------

    /**
     * Defines all the Deploy vars and their defaults
     * @return array
     */
    private function defineDeployVars()
    {
        $vars   = array();
        $vars[] = array(
                    'key'       => 'ENVIRONMENT',
                    'label'     => 'Environment',
                    'value'     => defined('ENVIRONMENT') ? ENVIRONMENT : 'PRODUCTION',
                    'options'   => array('DEVELOPMENT', 'STAGING', 'PRODUCTION')
                );

        $vars[] = array(
                    'key'       => 'BASE_URL',
                    'label'     => 'Base URL',
                    'value'     => defined('BASE_URL') ? BASE_URL : '',
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'DEPLOY_PRIVATE_KEY',
                    'label'     => 'Deployment Private Key',
                    'value'     => defined('DEPLOY_PRIVATE_KEY') ? DEPLOY_PRIVATE_KEY : md5(rand(0, 1000) . microtime(true)),
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'DEPLOY_DB_HOST',
                    'label'     => 'Database Host',
                    'value'     => defined('DEPLOY_DB_HOST') ? DEPLOY_DB_HOST : 'localhost',
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'DEPLOY_DB_USERNAME',
                    'label'     => 'Database User',
                    'value'     => defined('DEPLOY_DB_USERNAME') ? DEPLOY_DB_USERNAME : '',
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'DEPLOY_DB_PASSWORD',
                    'label'     => 'Database Password',
                    'value'     => defined('DEPLOY_DB_PASSWORD') ? DEPLOY_DB_PASSWORD : '',
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'DEPLOY_DB_DATABASE',
                    'label'     => 'Database Name',
                    'value'     => defined('DEPLOY_DB_DATABASE') ? DEPLOY_DB_DATABASE : '',
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'DEPLOY_EMAIL_HOST',
                    'label'     => 'Email Host',
                    'value'     => defined('DEPLOY_EMAIL_HOST') ? DEPLOY_EMAIL_HOST : 'localhost',
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'DEPLOY_EMAIL_USER',
                    'label'     => 'Email Username',
                    'value'     => defined('DEPLOY_EMAIL_USER') ? DEPLOY_EMAIL_USER : '',
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'DEPLOY_EMAIL_PASS',
                    'label'     => 'Email Password',
                    'value'     => defined('DEPLOY_EMAIL_PASS') ? DEPLOY_EMAIL_PASS : '',
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'DEPLOY_EMAIL_PORT',
                    'label'     => 'Email Port',
                    'value'     => defined('DEPLOY_EMAIL_PORT') ? DEPLOY_EMAIL_PORT : '25',
                    'options'   => array()
                );

        // --------------------------------------------------------------------------

        //  Any constants defined by the modules?
        $moduleVars = $this->getConstantsFromModules('DEPLOY', $vars);
        $vars = array_merge($vars, array_filter($moduleVars));

        // --------------------------------------------------------------------------

        //  Any other constants defined in deploy.php?
        $appFile = $this->getConstantsFromFile('config/deploy.php', $vars);
        $vars = array_merge($vars, array_filter($appFile));

        // --------------------------------------------------------------------------

        return $vars;
    }

    // --------------------------------------------------------------------------

    /**
     * Finds all constants defined in a particular file
     * @param  string $path The path to analyse
     * @param  array  $vars The existing variables to check against (so only new variables are returned)
     * @return array
     */
    private function getConstantsFromFile($path, $vars = array())
    {
        $out = array();

        if (file_exists($path)) {

            $appFile = file_get_contents($path);
            $pattern = '/define\([\'|"](.+?)[\'|"]\,.*[\'|"](.*?)[\'|"]\)/';
            $appVars = preg_match_all($pattern, $appFile, $matches);

            if (!empty($matches[0])) {

                for ($i = 0; $i < count($matches[0]); $i++) {

                    //  Check to see if it's already been requested
                    $exists = false;
                    foreach ($vars as $existing) {

                        if (!is_string($existing) && $existing['key'] == $matches[1][$i]) {

                            $exists = true;
                        }
                    }

                    if (!$exists) {

                        $name = str_replace('_', ' ', $matches[1][$i]);
                        $name = strtolower($name);
                        $name = ucwords($name);

                        $out[] = array(
                            'key'       => $matches[1][$i],
                            'label'     => $name,
                            'value'     => defined($matches[1][$i]) ? constant($matches[1][$i]) : '',
                            'options'   => array()
                        );
                    }
                }
            }
        }

        return $out;
    }

    // --------------------------------------------------------------------------

    /**
     * Finds all constants defined by the enabled modules for either app.php or deploy.php
     * @param  string $type The type of constant (either APP or DEPLOY)
     * @param  array  $vars The existing variables to check against (so only new variables are returned)
     * @return array
     */
    private function getConstantsFromModules($type, $vars = array())
    {
        //  @TODO: Look for modules
        return array();
    }

    // --------------------------------------------------------------------------

    /**
     * Finds constants for a particular module
     * @param  string $module The name of the module to look at
     * @param  string $type   The type of constant (either APP or DEPLOY)
     * @return array
     */
    private function getConstantsFromModule($module, $type)
    {
        //  @TODO: Analyse module
        return array();
    }

    // --------------------------------------------------------------------------

    /**
     * Checks for the existance and writeability of the config files and directory
     * @return array
     */
    private function preInstallTests()
    {
        $preTestErrors      = array();
        $appConfigExists    = file_exists('config/app.php');
        $deployConfigExists = file_exists('config/deploy.php');

        //  If config/app.php is there is it writeable?
        if ($appConfigExists) {

            if (!is_writable('config/app.php')) {

                $preTestErrors[] = '<comment>config/app.php</comment> exists, but is not writeable.';
            }
        }

        //  If config/deploy.php is there, is it writeable?
        if ($deployConfigExists) {

            if (!is_writable('config/deploy.php')) {

                $preTestErrors[] = '<comment>config/app.php</comment> exists, but is not writeable.';
            }
        }

        //  If a file is missing we need to be able to write to the directory.
        if (!$appConfigExists || !$deployConfigExists) {

            if (!is_writable('config/')) {

                $preTestErrors[] = '<comment>config/</comment> is not writeable.';
            }
        }

        return $preTestErrors;
    }

    // --------------------------------------------------------------------------

    /**
     * Requests the user to confirm all the variables
     * @param array           &$vars  An array of the variables to set
     * @param InputInterface  $input  The Input Interface provided by Symfony
     * @param OutputInterface $output The Output Interface provided by Symfony
     */
    private function setVars(&$vars, $input, $output)
    {
        foreach ($vars as &$v) {

            if (is_array($v)) {

                $question  = 'What should "' . $v['label'] . '" be set to?';
                $question .= !empty($v['options']) ? ' (' . implode('|', $v['options']) . ')' : '';

                $v['value'] = $this->ask($question, $v['value'], $input, $output);
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Writes the supplied variables to the config file
     * @param  array  $vars The variables to write
     * @param  string $file The file to write to
     * @return boolean
     */
    private function writeFile($vars, $file)
    {
        $fp = fopen($file, 'w');

        fwrite($fp, "<?php\n");
        foreach ($vars as $v) {

            if (is_string($v)) {

                fwrite($fp, $v . "\n");

            } else {

                fwrite($fp, "define('" . $v['key'] . "', '" . str_replace("'", "\'", $v['value']) . "');\n");
            }
        }

        fclose($fp);

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Installs a particular module
     * @param  string          $moduleName    The name of the module to install
     * @param  string          $moduleVersion The version of the module to install
     * @param  OutputInterface $output        The Output Interface provided by Symfony
     * @return boolean
     */
    private function installModule($moduleName, $moduleVersion, $output)
    {
        $composerBin = $this->detectComposerBin();

        exec($composerBin . ' require ' . $moduleName . ':' . $moduleVersion, $execOutput, $execReturn);

        if ($execReturn !== 0) {

            $output->writeln('<error>FAILED</error>');
            $output->writeln('Composer failed with exit code "' . $execReturn . '"');
            return false;
        }

        $output->writeln('<info>DONE</info>');
        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Migrates the DB for a fresh install
     * @param  OutputInterface $output The Output Interface provided by Symfony
     * @return boolean
     */
    private function migrateDb($output)
    {
        //  Execute the migrate command, silently
        $cmd = $this->getApplication()->find('migrate');

        $cmdInput  = new ArrayInput(array('command' => 'migrate'));
        $cmdInput->setInteractive(false);

        $cmdOutput = new NullOutput();

        $exitCode = $cmd->run($cmdInput, $cmdOutput);

        if ($exitCode == 0) {

            $output->writeln('<info>DONE</info>');
            return true;

        } else {

            $output->writeln('<error>WARNING</error>');
            $output->writeln('');
            $output->writeln('<error>The Migration tool encoutered issues and aborted the migration.</error>');
            $output->writeln('<error>You should run it manually and investigate any issues.</error>');
            $output->writeln('<error>The exit code was ' . $exitCode . '</error>');
            return false;
        }
    }

    // --------------------------------------------------------------------------

    public function createUser($user, $appVars, $deployVars, $output)
    {
        //  @TODO: Test username/email for duplicates

        //  Load the password model if not loaded
        require_once 'vendor/nailsapp/module-auth/auth/models/user_password_model.php';

        //  Correctly encode the password
        if (!defined('APP_PRIVATE_KEY')) {

            foreach ($appVars as $var) {

                if (is_array($var) && $var['key'] == 'APP_PRIVATE_KEY') {

                    define('APP_PRIVATE_KEY', $var['value']);
                    break;
                }
            }
        }

        if (!defined('APP_PRIVATE_KEY')) {

            foreach ($deployVars as $var) {

                if (is_array($var) && $var['key'] == 'DEPLOY_PRIVATE_KEY') {

                    define('DEPLOY_PRIVATE_KEY', $var['value']);
                    break;
                }
            }
        }

        $password = NAILS_User_password_model::generateHashObject($user['password']);

        // --------------------------------------------------------------------------

        //  Create the main record
        $sql = "INSERT INTO `" . NAILS_DB_PREFIX . "user`
        (
            `group_id`,
            `ip_address`,
            `last_ip`,
            `username`,
            `password`,
            `password_md5`,
            `password_engine`,
            `salt`,
            `created`,
            `first_name`,
            `last_name`
        )
        VALUES
        (
            1,
            '127.0.0.1',
            '127.0.0.1',
            " . $this->dbEscape(strtolower(trim($user['username']))) . ",
            '" . $password->password . "',
            '" . $password->password_md5 . "',
            '" . $password->engine . "',
            '" . $password->salt . "',
            NOW(),
            " . $this->dbEscape($user['first_name']) . ",
            " . $this->dbEscape($user['last_name']) . "
        );";

        $result = $this->dbQuery($sql);
        if (!$result) {

            return 'Could not create main user record.';
        }

        // --------------------------------------------------------------------------

        //  Get the user's ID
        $userId = $this->dbInsertId();

        // --------------------------------------------------------------------------

        //  Update the main record's id_md5 value
        $sql = "UPDATE `" . NAILS_DB_PREFIX . "user` SET `id_md5` = MD5(`id`) WHERE `id` = " . $userId . ";";
        $result = $this->dbQuery($sql);
        if (!$result) {

            $this->dbQuery("DELETE FROM `" . NAILS_DB_PREFIX . "user` WHERE `id` = " . $userId);
            return 'Could not set MD5 ID on main user record.';
        }

        // --------------------------------------------------------------------------

        //  Create the user meta record
        $sql = "INSERT INTO `" . NAILS_DB_PREFIX . "user_meta` (`user_id`) VALUES (" . $userId . ");";
        $result = $this->dbQuery($sql);
        if (!$result) {

            $this->dbQuery("DELETE FROM `" . NAILS_DB_PREFIX . "user` WHERE `id` = " . $userId);
            return 'Could not create user_meta record.';
        }

        // --------------------------------------------------------------------------

        //  Create the email record
        $emailCode = NAILS_User_password_model::salt();
        $sql = "INSERT INTO `" . NAILS_DB_PREFIX . "user_email`
        (
            `user_id`,
            `email`,
            `code`,
            `is_verified`,
            `is_primary`,
            `date_added`,
            `date_verified`
        )
        VALUES
        (
            " . $userId . ",
            " . $this->dbEscape(strtolower(trim($user['email']))) . ",
            '" . $emailCode . "',
            1,
            1,
            NOW(),
            NOW()
        );";

        $result = $this->dbQuery($sql);
        if (!$result) {

            $this->dbQuery("DELETE FROM `" . NAILS_DB_PREFIX . "user` WHERE `id` = " . $userId);
            $this->dbQuery("DELETE FROM `" . NAILS_DB_PREFIX . "user_meta` WHERE `user_id` = " . $userId);
            return 'Could not create main user email record.';
        }

        return true;
    }

    // --------------------------------------------------------------------------

    private function abort($output, $exitCode = 1)
    {
        $output->writeln('');

        if ($this->dbTransRunning) {

            $output->writeln('<error>Rolling back Database</error>');
            $this->dbTransactionRollback();
        }

        $output->writeln('<error>Aborting install</error>');
        $output->writeln('');
        return $exitCode;
    }

    // --------------------------------------------------------------------------

    private function detectComposerBin()
    {
        //  Detect composer
        $composerBin = 'composer';
        $result = shell_exec('which ' . $composerBin);

        if (empty($result)) {

            $composerBin = 'composer.phar';
            $result = shell_exec('which ' . $composerBin);

            if (empty($result)) {

                $composerBin = '';
            }
        }

        return $composerBin;
    }

    // --------------------------------------------------------------------------

    private function requestModule($moduleName, $input, $output)
    {
        //  Get the module
        do {

            $isValidModule = $this->isValidModule($moduleName);

            if (!$isValidModule) {

                if ($moduleName) {

                    //  Tell user their module name isn't valid
                    $output->writeln('');
                    $output->writeln('Sorry, <info>' . $moduleName . '</info> is not a valid Nails module.');

                    //  Search to see if what they said vaguely matches any module name
                    $searchResult = $this->searchModules($moduleName);

                    if ($searchResult) {

                        $output->writeln('');
                        $output->writeln('Did you mean any of the following:');

                        foreach ($searchResult as $resultModuleName) {

                            $output->writeln(' - <comment>' . $resultModuleName . '</comment>');
                        }
                    }
                }

                $output->writeln('');
                $question = 'Enter the module name you\'d like to install';
                $moduleName = $this->ask($question, '', $input, $output);
            }

        } while (!$isValidModule);

        //  Get the version to install
        $question = 'Enter the version you require for <info>' . $moduleName . '</info>';
        $moduleVersion = $this->ask($question, 'dev-develop', $input, $output);

        return array($moduleName, $moduleVersion);
    }

    // --------------------------------------------------------------------------

    protected function isValidModule($moduleName)
    {
        $potentialModules = _NAILS_GET_POTENTIAL_MODULES();
        return in_array($moduleName, $potentialModules);
    }

    // --------------------------------------------------------------------------

    protected function searchModules($moduleName)
    {
        $potentialModules = _NAILS_GET_POTENTIAL_MODULES();
        $results          = array();

        foreach ($potentialModules as $module) {

            if (strpos($module, $moduleName) !== FALSE) {

                $results[] = $module;
            }
        }

        return $results;
    }
}
