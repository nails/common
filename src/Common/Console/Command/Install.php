<?php

namespace Nails\Common\Console\Command;

use Nails\Console\Command\Base;
use Nails\Environment;
use Nails\Factory;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Install extends Base
{
    /**
     * The endpoint for the components API
     *
     * @var string
     */
    protected $componentEndpoint = 'http://components.nailsapp.co.uk/';

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
        $this->setName('install');
        $this->setDescription('Configures or reconfigures a Nails site');

        $this->addArgument(
            'componentName',
            InputArgument::OPTIONAL,
            'If a component name is provided it will be added to composer.json if valid'
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the app
     *
     * @param  InputInterface  $input  The Input Interface provided by Symfony
     * @param  OutputInterface $output The Output Interface provided by Symfony
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $component = $input->getArgument('componentName');

        if (!empty($component)) {

            return $this->executeComponentInstaller($component, $input, $output);

        } else {

            return $this->executeInstaller($input, $output);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the Nails Installer
     *
     * @param  InputInterface  $input  The Input Interface provided by Symfony
     * @param  OutputInterface $output The Output Interface provided by Symfony
     * @return void
     */
    protected function executeInstaller(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln('<info>---------------</info>');
        $output->writeln('<info>Nails Installer</info>');
        $output->writeln('<info>---------------</info>');
        $output->writeln('Beginning...');

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

            //  Can we install components? We need exec() and composer to be available
            $execAvailable     = function_exists('exec');
            $composerAvailable = (bool) $this->detectComposerBin();

            if ($execAvailable && $composerAvailable) {

                $output->writeln('');
                $output->writeln('<info>Nails Components</info>');

                $question          = 'Would you like to define components to enable now?';
                $installComponents = $this->confirm($question, false, $input, $output);

                //  Show a list of already installed components
                $installedComponents = _NAILS_GET_COMPONENTS();

                //  I know the variables are almost the same name. Just to confuse ya. >_<
                if ($installComponents) {

                    if ($installedComponents) {

                        $output->writeln('');
                        $output->writeln('The following components are already installed:');
                        $output->writeln('');

                        foreach ($installedComponents as $component) {
                            $output->writeln(' - <info>' . $component->slug . '</info>');
                        }

                        $output->writeln('');
                    }
                }

                $installTheseComponents = array();

                while ($installComponents) {

                    $component = $this->requestComponent(null, $input, $output);

                    if (is_array($component)) {

                        $installTheseComponents[$component[0]] = $component[1];
                    }

                    $output->writeln('');
                    $question          = 'Would you like to enable another component?';
                    $installComponents = $this->confirm($question, false, $input, $output);
                }
            }

            // --------------------------------------------------------------------------

            //  Only ask to create users if nailsapp/module-auth is installed (or will be installed)
            $isAuthModuleAvailable = false;

            foreach ($installedComponents as $component) {

                if ($component->name == 'nailsapp/module-auth') {

                    $isAuthModuleAvailable = true;
                    break;
                }
            }

            //  Not instaled, will it be installed?
            if (!$isAuthModuleAvailable) {

                foreach ($installTheseComponents as $componentName => $componentVersion) {

                    if ($componentName == 'nailsapp/module-auth') {

                        $isAuthModuleAvailable = true;
                        break;
                    }
                }
            }

            if ($isAuthModuleAvailable) {

                $output->writeln('');
                $output->writeln('<info>Users</info>');

                $question   = 'Would you like to create some users?';
                $createUser = $this->confirm($question, false, $input, $output);
                $users      = array();

                $userField               = array();
                $userField['first_name'] = 'First Name';
                $userField['last_name']  = 'Surname';
                $userField['email']      = 'Email Address';
                $userField['username']   = 'Username';
                $userField['password']   = 'Password';

                if ($createUser) {

                    do {

                        $temp      = array();
                        $userCount = count($users) + 1;

                        $output->writeln('');
                        $output->writeln('User #' . $userCount);

                        foreach ($userField as $key => $label) {

                            do {

                                $temp[$key] = $this->ask($label, '', $input, $output);

                            } while (empty($temp[$key]));
                        }

                        $users[] = $temp;

                        $output->writeln('');

                        $question   = 'Create another user?';
                        $createUser = $this->confirm($question, false, $input, $output);

                        if (!$createUser) {

                            $output->writeln('');
                        }

                    } while ($createUser);
                }
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

            //  Install components
            if (!empty($installTheseComponents)) {

                $output->writeln('');

                if (count($installTheseComponents) > 1) {

                    $output->writeln('The following components will be installed:');

                } else {

                    $output->writeln('The following component will be installed:');
                }

                foreach ($installTheseComponents as $componentName => $componentVersion) {

                    $output->writeln(' - <comment>' . $componentName . ':' . $componentVersion . '</comment>');
                }
            }

            //  Migrate databases
            $output->writeln('');
            $output->writeln('Migrate the database');

            //  Add users
            if (!empty($users)) {

                $output->writeln('');

                if (count($users) == 1) {

                    $output->writeln('Add user <info>' . $users[0]['first_name'] . ' ' . $users[0]['last_name'] . '</info>');

                } else {

                    $output->writeln('Add <info>' . count($users) . '</info> users');
                }
            }

            $output->writeln('');
            $question  = 'Does this look OK?';
            $doInstall = $this->confirm($question, true, $input, $output);

            if ($doInstall) {


                $curStep  = 1;
                $numSteps = 1; //  app.php
                $numSteps += 1; //  deploy.php
                $numSteps += !empty($installTheseComponents) ? 1 : 0;
                $numSteps += 1; //  migrate DB.php
                $numSteps += !empty($users) ? 1 : 0;

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

                //  Install Components
                if (!empty($installTheseComponents)) {

                    $output->writeln('<comment>[' . $curStep . '/' . $numSteps . ']</comment> Installing components</info>...');

                    foreach ($installTheseComponents as $componentName => $componentVersion) {

                        $output->write(' - <comment>' . $componentName . ':' . $componentVersion . '</comment>... ');
                        $this->installComponent($componentName, $componentVersion, $output);
                    }
                    $curStep++;
                }

                /**
                 * Get the current database credential values. If ane xisting deploy.php is already there then
                 * we can't rely on it having the latest details (as it might have just been updated).
                 */

                $dbHost = $this->getVarValue('DEPLOY_DB_HOST', $deployVars);
                $dbUser = $this->getVarValue('DEPLOY_DB_USERNAME', $deployVars);
                $dbPass = $this->getVarValue('DEPLOY_DB_PASSWORD', $deployVars);
                $dbName = $this->getVarValue('DEPLOY_DB_DATABASE', $deployVars);

                //  Migrate DB
                $output->write('<comment>[' . $curStep . '/' . $numSteps . ']</comment> Migrating database... ');
                $this->migrateDb($output, $dbHost, $dbUser, $dbPass, $dbName);
                $curStep++;

                //  Add Users
                if (!empty($users)) {

                    $output->writeln('<comment>[' . $curStep . '/' . $numSteps . ']</comment> Creating Users</info>...');

                    //  Setup Factory - we need config files and/or constants to be set
                    Factory::setup();
                    $this->oDb = Factory::service('ConsoleDatabase', 'nailsapp/module-console');

                    foreach ($users as $user) {

                        $output->write(' - <comment>' . $user['first_name'] . ' ' . $user['last_name'] . '</comment>... ');
                        $result = $this->createUser($user, $appVars, $deployVars, $output);

                        if ($result === true) {

                            $output->writeln('<info>DONE</info>');

                        } else {

                            $output->writeln('<error>FAILED: ' . $result . '</error>');
                        }
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
     * Executes the Nails Component Installer
     *
     * @param  InputInterface  $input  The Input Interface provided by Symfony
     * @param  OutputInterface $output The Output Interface provided by Symfony
     * @return void
     */
    protected function executeComponentInstaller($component, InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>-------------------------</info>');
        $output->writeln('<info>Nails Component Installer</info>');
        $output->writeln('<info>-------------------------</info>');

        // --------------------------------------------------------------------------

        //  Get the Component
        $component = $this->requestComponent($component, $input, $output);

        if (!$component) {

            return $this->abort($output, 0);
        }

        //  Confirm with user
        $output->writeln('');
        $output->writeln('<info>I\'m about to do the following:</info>');
        $output->writeln(' - Install <info>' . $component[0] . ':' . $component[1] . '</info>');
        $output->writeln(' - Migrate the database');
        $output->writeln('');

        $question  = 'Continue?';
        $doInstall = $this->confirm($question, true, $input, $output);

        if ($doInstall) {

            //  Attempt to install
            $output->writeln('');
            $output->write('<comment>[1/2]</comment> Installing <info>' . $component[0] . ':' . $component[1] . '</info>... ');
            if (!$this->installComponent($component[0], $component[1], $output)) {

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
     *
     * @return array
     */
    private function defineAppVars()
    {
        $vars   = array();
        $vars[] = '// App Constants';
        $vars[] = array(
            'key'     => 'APP_NAME',
            'label'   => 'App Name',
            'value'   => defined('APP_NAME') ? APP_NAME : 'My App',
            'options' => array()
        );

        $vars[] = array(
            'key'     => 'APP_DEFAULT_TIMEZONE',
            'label'   => 'App Timezone',
            'value'   => defined('APP_DEFAULT_TIMEZONE') ? APP_DEFAULT_TIMEZONE : date_default_timezone_get(),
            'options' => array()
        );

        $vars[] = array(
            'key'     => 'APP_PRIVATE_KEY',
            'label'   => 'App Private Key',
            'value'   => defined('APP_PRIVATE_KEY') ? APP_PRIVATE_KEY : md5(rand(0, 1000) . microtime(true)),
            'options' => array()
        );

        $vars[] = array(
            'key'     => 'APP_DEVELOPER_EMAIL',
            'label'   => 'Developer Email',
            'value'   => defined('APP_DEVELOPER_EMAIL') ? APP_DEVELOPER_EMAIL : '',
            'options' => array()
        );

        // --------------------------------------------------------------------------

        //  Any constants defined by the components?
        $componentVars = $this->getConstantsFromComponents('APP', $vars);
        $vars          = array_merge($vars, $componentVars);

        // --------------------------------------------------------------------------

        //  Any other constants defined in app.php?
        $appFile = $this->getConstantsFromFile('config/app.php', $vars);
        $vars    = array_merge($vars, $appFile);

        // --------------------------------------------------------------------------

        return $vars;
    }

    // --------------------------------------------------------------------------

    /**
     * Defines all the Deploy vars and their defaults
     *
     * @return array
     */
    private function defineDeployVars()
    {
        $vars   = array();
        $vars[] = array(
            'key'     => 'ENVIRONMENT',
            'label'   => 'Environment',
            'value'   => Environment::get() ?: 'PRODUCTION',
            'options' => array('DEVELOPMENT', 'STAGING', 'PRODUCTION')
        );

        $vars[] = array(
            'key'     => 'BASE_URL',
            'label'   => 'Base URL',
            'value'   => defined('BASE_URL') ? BASE_URL : '',
            'options' => array()
        );

        $vars[] = array(
            'key'     => 'DEPLOY_PRIVATE_KEY',
            'label'   => 'Deployment Private Key',
            'value'   => defined('DEPLOY_PRIVATE_KEY') ? DEPLOY_PRIVATE_KEY : md5(rand(0, 1000) . microtime(true)),
            'options' => array()
        );

        $vars[] = array(
            'key'     => 'DEPLOY_DB_HOST',
            'label'   => 'Database Host',
            'value'   => defined('DEPLOY_DB_HOST') ? DEPLOY_DB_HOST : 'localhost',
            'options' => array()
        );

        $vars[] = array(
            'key'     => 'DEPLOY_DB_USERNAME',
            'label'   => 'Database User',
            'value'   => defined('DEPLOY_DB_USERNAME') ? DEPLOY_DB_USERNAME : '',
            'options' => array()
        );

        $vars[] = array(
            'key'     => 'DEPLOY_DB_PASSWORD',
            'label'   => 'Database Password',
            'value'   => defined('DEPLOY_DB_PASSWORD') ? DEPLOY_DB_PASSWORD : '',
            'options' => array()
        );

        $vars[] = array(
            'key'     => 'DEPLOY_DB_DATABASE',
            'label'   => 'Database Name',
            'value'   => defined('DEPLOY_DB_DATABASE') ? DEPLOY_DB_DATABASE : '',
            'options' => array()
        );

        $vars[] = array(
            'key'     => 'DEPLOY_EMAIL_HOST',
            'label'   => 'Email Host',
            'value'   => defined('DEPLOY_EMAIL_HOST') ? DEPLOY_EMAIL_HOST : 'localhost',
            'options' => array()
        );

        $vars[] = array(
            'key'     => 'DEPLOY_EMAIL_USER',
            'label'   => 'Email Username',
            'value'   => defined('DEPLOY_EMAIL_USER') ? DEPLOY_EMAIL_USER : '',
            'options' => array()
        );

        $vars[] = array(
            'key'     => 'DEPLOY_EMAIL_PASS',
            'label'   => 'Email Password',
            'value'   => defined('DEPLOY_EMAIL_PASS') ? DEPLOY_EMAIL_PASS : '',
            'options' => array()
        );

        $vars[] = array(
            'key'     => 'DEPLOY_EMAIL_PORT',
            'label'   => 'Email Port',
            'value'   => defined('DEPLOY_EMAIL_PORT') ? DEPLOY_EMAIL_PORT : '25',
            'options' => array()
        );

        // --------------------------------------------------------------------------

        //  Any constants defined by the components?
        $componentVars = $this->getConstantsFromComponents('DEPLOY', $vars);
        $vars          = array_merge($vars, array_filter($componentVars));

        // --------------------------------------------------------------------------

        //  Any other constants defined in deploy.php?
        $appFile = $this->getConstantsFromFile('config/deploy.php', $vars);
        $vars    = array_merge($vars, array_filter($appFile));

        // --------------------------------------------------------------------------

        return $vars;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the current value of a variable
     *
     * @param  string $key  The key to return
     * @param  array  $vars The variable array to look at
     * @return mixed        var value (usually string) on success, null on failure
     */
    private function getVarValue($key, $vars)
    {
        foreach ($vars as $var) {

            if ($key == $var['key']) {

                return $var['value'];
            }
        }

        return null;
    }

    // --------------------------------------------------------------------------

    /**
     * Finds all constants defined in a particular file
     *
     * @param  string $path The path to analyse
     * @param  array  $vars The existing variables to check against (so only new variables are returned)
     * @return array
     */
    private function getConstantsFromFile($path, $vars = array())
    {
        $out = array();

        if (file_exists($path)) {

            $appFile = file_get_contents($path);
            $pattern = '/define\([\'|"](.+?)[\'|"]\,(.*)\)/';
            preg_match_all($pattern, $appFile, $matches);

            if (!empty($matches[0])) {

                $numMatches = count($matches[0]);

                //  Remove quotes from stringy values
                for ($i = 0; $i < $numMatches; $i++) {

                    $matches[2][$i] = trim($matches[2][$i]);

                    if (substr($matches[2][$i], 0, 1) == '\'' || substr($matches[2][$i], 0, 1) == '"') {
                        //  Remove the first and last character; subtracting 2 to account for the removal of both chars
                        $matches[2][$i] = substr($matches[2][$i], 1, strlen($matches[2][$i]) - 2);
                    }
                }

                for ($i = 0; $i < $numMatches; $i++) {

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
                            'key'     => $matches[1][$i],
                            'label'   => $name,
                            'value'   => defined($matches[1][$i]) ? constant($matches[1][$i]) : '',
                            'options' => array()
                        );
                    }
                }
            }
        }

        return $out;
    }

    // --------------------------------------------------------------------------

    /**
     * Finds all constants defined by the enabled components for either app.php or deploy.php
     *
     * @param  string $type The type of constant (either APP or DEPLOY)
     * @param  array  $vars The existing variables to check against (so only new variables are returned)
     * @return array
     */
    private function getConstantsFromComponents($type, $vars = array())
    {
        //  @TODO: Look for components
        return array();
    }

    // --------------------------------------------------------------------------

    /**
     * Finds constants for a particular component
     *
     * @param  string $component The name of the component to look at
     * @param  string $type      The type of constant (either APP or DEPLOY)
     * @return array
     */
    private function getConstantsFromComponent($component, $type)
    {
        //  @TODO: Analyse component
        return array();
    }

    // --------------------------------------------------------------------------

    /**
     * Checks for the existance and writeability of the config files and directory
     *
     * @return array
     */
    private function preInstallTests()
    {
        $preTestErrors      = array();
        $appConfigExists    = file_exists('config/app.php');
        $deployConfigExists = file_exists('config/deploy.php');

        //  If config/app.php is there is it writeable?
        if ($appConfigExists) {

            if (!is_writable(FCPATH . 'config/app.php')) {

                $preTestErrors[] = '<comment>config/app.php</comment> exists, but is not writeable.';
            }
        }

        //  If config/deploy.php is there, is it writeable?
        if ($deployConfigExists) {

            if (!is_writable(FCPATH . 'config/deploy.php')) {

                $preTestErrors[] = '<comment>config/app.php</comment> exists, but is not writeable.';
            }
        }

        //  If a file is missing we need to be able to write to the directory.
        if (!$appConfigExists || !$deployConfigExists) {

            if (!is_writable(FCPATH . 'config/')) {
                $preTestErrors[] = '<comment>config/</comment> is not writeable.';
            }
        }

        return $preTestErrors;
    }

    // --------------------------------------------------------------------------

    /**
     * Requests the user to confirm all the variables
     *
     * @param array           &$vars  An array of the variables to set
     * @param InputInterface  $input  The Input Interface provided by Symfony
     * @param OutputInterface $output The Output Interface provided by Symfony
     */
    private function setVars(&$vars, $input, $output)
    {
        foreach ($vars as &$v) {

            if (is_array($v)) {

                $question = 'What should "' . $v['label'] . '" be set to?';
                $question .= !empty($v['options']) ? ' (' . implode('|', $v['options']) . ')' : '';

                $v['value'] = $this->ask($question, $v['value'], $input, $output);
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Writes the supplied variables to the config file
     *
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

                if (is_numeric($v['value'])) {

                    $sValue = $v['value'];

                } else {

                    $sValue = "'" . str_replace("'", "\'", $v['value']) . "'";
                }

                fwrite($fp, "define('" . $v['key'] . "', " . $sValue . ");\n");
            }
        }

        fclose($fp);

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Installs a particular component
     *
     * @param  string          $componentName    The name of the component to install
     * @param  string          $componentVersion The version of the component to install
     * @param  OutputInterface $output           The Output Interface provided by Symfony
     * @return boolean
     */
    private function installComponent($componentName, $componentVersion, $output)
    {
        $composerBin = $this->detectComposerBin();

        exec($composerBin . ' require ' . $componentName . ':' . $componentVersion, $execOutput, $execReturn);

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
     *
     * @param  OutputInterface $output The Output Interface provided by Symfony
     * @param  string          $dbhost The database hsot to connect to
     * @param  string          $dbuser The database user to connect with
     * @param  string          $dbpass The database password to connect with
     * @param  string          $dbname The database name to connect to
     * @return boolean
     */
    private function migrateDb($output, $dbhost = null, $dbuser = null, $dbpass = null, $dbname = null)
    {
        //  Execute the migrate command, silently
        $cmd = $this->getApplication()->find('migrate');

        $cmdInput = new ArrayInput(
            array(
                'command'  => 'migrate',
                '--dbHost' => $dbhost,
                '--dbUser' => $dbuser,
                '--dbPass' => $dbpass,
                '--dbName' => $dbname
            )
        );

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

    /**
     * Creates a new user account using group_id 1; i.e the default super user group
     *
     * @param  array           $user       The user's details
     * @param  array           $appVars    The application vars
     * @param  array           $deployVars The deploy vars
     * @param  OutputInterface $output     The Output Interface provided by Symfony
     * @return mixed                       boolean on success, false on failure
     */
    public function createUser($user, $appVars, $deployVars, $output)
    {
        //  @TODO: Test username/email for duplicates

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

        $oPasswordModel = Factory::model('UserPassword', 'nailsapp/module-auth');
        $password       = $oPasswordModel->generateHashObject($user['password']);

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
            " . $this->oDb->escape(strtolower(trim($user['username']))) . ",
            '" . $password->password . "',
            '" . $password->password_md5 . "',
            '" . $password->engine . "',
            '" . $password->salt . "',
            NOW(),
            " . $this->oDb->escape($user['first_name']) . ",
            " . $this->oDb->escape($user['last_name']) . "
        );";

        $result = $this->oDb->query($sql);
        if (!$result) {

            return 'Could not create main user record.';
        }

        // --------------------------------------------------------------------------

        //  Get the user's ID
        $userId = $this->oDb->lastInsertId();

        // --------------------------------------------------------------------------

        //  Update the main record's id_md5 value
        $sql    = "UPDATE `" . NAILS_DB_PREFIX . "user` SET `id_md5` = MD5(`id`) WHERE `id` = " . $userId . ";";
        $result = $this->oDb->query($sql);
        if (!$result) {

            $this->oDb->query("DELETE FROM `" . NAILS_DB_PREFIX . "user` WHERE `id` = " . $userId);

            return 'Could not set MD5 ID on main user record.';
        }

        // --------------------------------------------------------------------------

        //  Create the user meta record
        $sql    = "INSERT INTO `" . NAILS_DB_PREFIX . "user_meta_app` (`user_id`) VALUES (" . $userId . ");";
        $result = $this->oDb->query($sql);
        if (!$result) {

            $this->oDb->query("DELETE FROM `" . NAILS_DB_PREFIX . "user` WHERE `id` = " . $userId);

            return 'Could not create user_meta_app record.';
        }

        // --------------------------------------------------------------------------

        //  Create the email record
        $emailCode = $oPasswordModel->salt();
        $sql       = "INSERT INTO `" . NAILS_DB_PREFIX . "user_email`
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
            " . $this->oDb->escape(strtolower(trim($user['email']))) . ",
            '" . $emailCode . "',
            1,
            1,
            NOW(),
            NOW()
        );";

        $result = $this->oDb->query($sql);
        if (!$result) {

            $this->oDb->query("DELETE FROM `" . NAILS_DB_PREFIX . "user` WHERE `id` = " . $userId);
            $this->oDb->query("DELETE FROM `" . NAILS_DB_PREFIX . "user_meta_app` WHERE `user_id` = " . $userId);

            return 'Could not create main user email record.';
        }

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Performs the abort functionality and returns the exit code
     *
     * @param  OutputInterface $output   The Output Interface provided by Symfony
     * @param  integer         $exitCode The exit code
     * @return int
     */
    private function abort($output, $exitCode = 1)
    {
        $output->writeln('');

        if (!empty($this->oDb) && $this->oDb->isTransactionRunning()) {

            $output->writeln('<error>Rolling back Database</error>');
            $this->oDb->transactionRollback();
        }

        $output->writeln('<error>Aborting install</error>');
        $output->writeln('');

        return $exitCode;
    }

    // --------------------------------------------------------------------------

    /**
     * Works out which binary to use for composer
     *
     * @return string
     */
    private function detectComposerBin()
    {
        //  Detect composer
        $composerBin = 'composer';
        $result      = shell_exec('which ' . $composerBin);

        if (empty($result)) {

            $composerBin = 'composer.phar';
            $result      = shell_exec('which ' . $composerBin);

            if (empty($result)) {

                $composerBin = '';
            }
        }

        return $composerBin;
    }

    // --------------------------------------------------------------------------

    /**
     * Asks the suer which component they'd like to install and validates it against
     * the Nails Components repository.
     *
     * @param  string          $componentName The component name to install
     * @param  InputInterface  $input         The Input Interface provided by Symfony
     * @param  OutputInterface $output        The Output Interface provided by Symfony
     * @return array
     */
    private function requestComponent($componentName, $input, $output)
    {
        //  Get the component
        do {

            //  If a component name has been specified, check to see if it's valid
            if (!empty($componentName)) {

                /**
                 * A component name has been given, check it out against the Nails
                 * Component repository to see if it's valid.
                 */

                $isValidComponent = $this->isValidComponent($componentName, $output);

            } elseif (empty($componentName) && !is_null($componentName)) {

                /**
                 * If the component name is empty, but not null then it means the user hit
                 * enter without typing anything, skip out of this so they're not stuck in
                 * a loop
                 */

                return false;

            } else {

                /**
                 * Set isValidComponent to false so that the loop continues.
                 */

                $isValidComponent = false;
            }

            if (!$isValidComponent) {

                $output->writeln('');
                $question      = 'Enter the component name you\'d like to install';
                $componentName = $this->ask($question, '', $input, $output);

            } else {

                $componentName    = $isValidComponent;
                $isValidComponent = true;
            }

        } while (!$isValidComponent);

        //  Already installed?
        $installed            = _NAILS_GET_COMPONENTS();
        $componentIsInstalled = false;

        foreach ($installed as $component) {

            if ($componentName == $component->name) {

                $componentIsInstalled = true;
                break;
            }
        }

        if (!$componentIsInstalled) {

            //  Get the version to install
            $question         = 'Enter the version you require for <info>' . $componentName . '</info>';
            $componentVersion = $this->ask($question, 'dev-develop', $input, $output);

            return array($componentName, $componentVersion);

        } else {

            $output->writeln('');
            $output->writeln('<info>' . $componentName . '</info> is already installed.');

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Searches Nails components Repository for the component name.
     *
     * @param  string          $componentName The component's name
     * @param  OutputInterface $output        The Output Interface provided by Symfony
     * @return mixed                        String on success (component's full name), false on failure
     */
    protected function isValidComponent($componentName, $output)
    {
        $result = @file_get_contents($this->componentEndpoint . 'api/search?term=' . urlencode($componentName));

        if (empty($result)) {

            $output->writeln('');
            $output->writeln('<error>ERROR</error>');
            $output->writeln('Query to ' . $this->componentEndpoint . ' failed. Could not validate component.');
            $output->writeln('');

            return false;
        }

        $result = json_decode($result);

        if (empty($result)) {

            $output->writeln('');
            $output->writeln('<error>ERROR</error>');
            $output->writeln('Failed to decode search results from ' . $this->componentEndpoint);
            $output->writeln('');

            return false;
        }

        if (empty($result->results)) {

            return false;

        } elseif (count($result->results) > 1) {

            $output->writeln('');
            $output->writeln('More than 1 component for <info>' . $componentName . '</info>. Did you mean:</comment>');

            foreach ($result->results as $component) {

                $url = $component->homepage;
                $url = !$url ? $component->repository : $url;

                $output->writeln('');
                $output->writeln(' - <info>' . $component->name . '</info>');

                if (!empty($component->description)) {

                    $output->writeln('   ' . $component->description);
                }

                if (!empty($url)) {

                    $output->writeln('   ' . $url);
                }
            }

            return false;

        } else {

            return $result->results[0]->name;
        }
    }
}
