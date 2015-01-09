<?php

/**
 * ---------------------------------------------------------------
 * NAILS CONSOLE: INSTALLER
 * ---------------------------------------------------------------
 *
 * This app handles configuring and reconfiguring a Nails app.
 *
 * Lead Developer: Pablo de la Peña (p@shedcollective.org, @hellopablo)
 * Lead Developer: Gary Duncan      (g@shedcollective.org, @gsdd)
 *
 * Documentation: http://nailsapp.co.uk/console/install
 */

namespace Nails\Console\Apps;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

require_once 'vendor/nailsapp/common/console/apps/_app.php';

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
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the app
     * @param  InputInterface  $input  The Input Interface proivided by Symfony
     * @param  OutputInterface $output The Output Interface proivided by Symfony
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>---------------</info>');
        $output->writeln('<info>Nails Installer</info>');
        $output->writeln('<info>---------------</info>');
        $output->writeln('Beginning...');

        // --------------------------------------------------------------------------

        //  Define app & deploy vars
        $appVars    = $this->defineAppVars();
        $deployVars = $this->defineDeployVars();

        // --------------------------------------------------------------------------

        //  Load configs
        if (file_exists('config/app.php')) {

            $output->writeln('Found <comment>config/app.php</comment> will use values for defaults');

            require_once 'config/app.php';

            foreach ($appVars as &$v) {

                if (defined($v['key'])) {

                    $v['value'] = constant($v['key']);
                }
            }
        }

        if (file_exists('config/deploy.php')) {

            $output->writeln('Found <comment>config/deploy.php</comment> will use values for defaults');

            require_once 'config/deploy.php';

            foreach ($deployVars as &$v) {

                if (defined($v['key'])) {

                    $v['value'] = constant($v['key']);
                }
            }
        }

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

            $output->writeln('');
            $output->writeln('<info>Modules</info>');

            $question = 'Would you like to define modules to enable now?';
            $installModules = $this->confirm($question, false, $input, $output);

            if ($installModules) {

                $output->writeln('');
                $output->writeln('<comment>@TODO:</comment> Interface for installing modules');
                $output->writeln('');

                $installTheseModules    = array();
                $installTheseModules[]  = 'test/module-1';
                $installTheseModules[]  = 'test/module-2';
                $installTheseModules[]  = 'test/module-3';
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

                $output->writeln(' - Set <comment>' . $v['label'] . '</comment> to <comment>' . $v['value'] . '</comment>');
            }

            //  deploy.php
            $output->writeln('');
            $output->writeln('Write <info>config/deploy.php</info>');

            foreach ($deployVars as &$v) {

                $output->writeln(' - Set <comment>' . $v['label'] . '</comment> to <comment>' . $v['value'] . '</comment>');
            }

            //  Install modules
            if (!empty($installTheseModules)) {

                $output->writeln('');

                if (count($installTheseModules) > 1) {

                    $output->writeln('The following module(s) will be installed:');

                } else {

                    $output->writeln('The following module will be installed:');
                }

                foreach ($installTheseModules as $moduleName) {

                    $output->writeln(' - <comment>' . $moduleName . '</comment>');
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

                    foreach ($installTheseModules as $moduleName) {

                        $output->write(' - <comment>' . $moduleName . '</comment>... ');
                        if ($this->installModule($moduleName, $input, $output)) {

                            $output->writeln('<info>DONE</info>');

                        } else {

                            $output->writeln('<error>FAILED</error>');
                        }
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
                            if ($this->createUser($user, $output)) {

                                $output->writeln('<info>DONE</info>');
                            } else {

                                $output->writeln('<error>FAILED</error>');
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

                $output->writeln('');
                $output->writeln('<error>Aborting install.</error>');
            }

        } else {

            $output->writeln('');
            $output->writeln('<error>ERROR:</error> Aborting install');

            foreach ($preTestErrors as $error) {

                $output->writeln(' - ' . $error);
            }
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
        $vars[] = array(
                    'key'       => 'APP_NAME',
                    'label'     => 'App Name',
                    'value'     => 'My App',
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'APP_DEFAULT_TIMEZONE',
                    'label'     => 'App Timezone',
                    'value'     => date_default_timezone_get(),
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'APP_PRIVATE_KEY',
                    'label'     => 'App Private Key',
                    'value'     => md5(rand(0, 1000) . microtime(true)),
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'APP_DEVELOPER_EMAIL',
                    'label'     => 'Developer Email',
                    'value'     => '',
                    'options'   => array()
                );

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
                    'value'     => 'PRODUCTION',
                    'options'   => array('DEVELOPMENT', 'STAGING', 'PRODUCTION')
                );

        $vars[] = array(
                    'key'       => 'BASE_URL',
                    'label'     => 'Base URL',
                    'value'     => '',
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'DEPLOY_PRIVATE_KEY',
                    'label'     => 'Deployment Private Key',
                    'value'     => md5(rand(0, 1000) . microtime(true)),
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'DEPLOY_DB_HOST',
                    'label'     => 'Database Host',
                    'value'     => 'localhost',
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'DEPLOY_DB_USERNAME',
                    'label'     => 'Database User',
                    'value'     => '',
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'DEPLOY_DB_PASSWORD',
                    'label'     => 'Database Password',
                    'value'     => '',
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'DEPLOY_DB_DATABASE',
                    'label'     => 'Database Name',
                    'value'     => '',
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'DEPLOY_EMAIL_HOST',
                    'label'     => 'Email Host',
                    'value'     => 'localhost',
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'DEPLOY_EMAIL_USER',
                    'label'     => 'Email Username',
                    'value'     => '',
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'DEPLOY_EMAIL_PASS',
                    'label'     => 'Email Password',
                    'value'     => '',
                    'options'   => array()
                );

        $vars[] = array(
                    'key'       => 'DEPLOY_EMAIL_PORT',
                    'label'     => 'Email Port',
                    'value'     => '25',
                    'options'   => array()
                );

        return $vars;
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
     * @param InputInterface  $input  The Input Interface proivided by Symfony
     * @param OutputInterface $output The Output Interface proivided by Symfony
     */
    private function setVars(&$vars, $input, $output)
    {
        foreach ($vars as &$v) {

            $question  = 'What should "' . $v['label'] . '" be set to?';
            $question .= !empty($v['options']) ? ' (' . implode('|', $v['options']) . ')' : '';

            $v['value'] = $this->ask($question, $v['value'], $input, $output);
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

            fwrite($fp, "define('" . $v['key'] . "', '" . str_replace("'", "\'", $v['value']) . "');\n");
        }

        fclose($fp);

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Installs a particular module
     * @param  string          $moduleName The name of the modoule to install
     * @param  InputInterface  $input      The Input Interface proivided by Symfony
     * @param  OutputInterface $output     The Output Interface proivided by Symfony
     * @return boolean
     */
    private function installModule($moduleName, $input, $output)
    {
        //  @TODO: Add module to composer.json

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Migrates the DB for a fresh install
     * @param  OutputInterface $output The Output Interface proivided by Symfony
     * @return boolean
     */
    private function migrateDb($output)
    {
        //  Execute the migrate command, silently
        $cmd       = $this->getApplication()->find('migrate');
        $cmdInput  = new ArrayInput(array('command' => 'migrate'));
        $cmdOutput = new NullOutput();
        $exitCode  = $cmd->run($cmdInput, $cmdOutput);

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

    public function createUser($user, $output)
    {
        //  @TODO Create users
        return true;
    }
}
