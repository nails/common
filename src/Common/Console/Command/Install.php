<?php

namespace Nails\Common\Console\Command;

use Nails\Console\Command\Base;
use Nails\Environment;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Install extends Base
{
    const EXIT_CODE_INSTALL_FAILED = 3;
    const EXIT_CODE_MIGRATE_FAILED = 4;

    // --------------------------------------------------------------------------
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
     * @param  InputInterface $oInput The Input Interface provided by Symfony
     * @param  OutputInterface $oOutput The Output Interface provided by Symfony
     * @return int
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        parent::execute($oInput, $oOutput);

        $component = $oInput->getArgument('componentName');

        if (!empty($component)) {
            return $this->executeComponentInstaller($component);
        } else {
            return $this->executeInstaller();
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the Nails Installer
     *
     * @return int
     */
    protected function executeInstaller()
    {
        $oOutput = $this->oOutput;

        // --------------------------------------------------------------------------

        $oOutput->writeln('');
        $oOutput->writeln('<info>---------------</info>');
        $oOutput->writeln('<info>Nails Installer</info>');
        $oOutput->writeln('<info>---------------</info>');
        $oOutput->writeln('Beginning...');

        // --------------------------------------------------------------------------

        //  Define app & deploy vars
        $appVars    = $this->defineAppVars();
        $deployVars = $this->defineDeployVars();

        // --------------------------------------------------------------------------

        //  Pre-Install tests
        $preTestErrors = $this->preInstallTests();

        // --------------------------------------------------------------------------

        if (empty($preTestErrors)) {

            $oOutput->writeln('');
            $oOutput->writeln('<info>App Settings</info>');
            $this->setVars($appVars);

            // --------------------------------------------------------------------------

            $oOutput->writeln('');
            $oOutput->writeln('<info>Deploy Settings</info>');
            $this->setVars($deployVars);

            // --------------------------------------------------------------------------

            //  Can we install components? We need exec() and composer to be available
            $execAvailable          = function_exists('exec');
            $composerAvailable      = (bool) $this->detectComposerBin();
            $installTheseComponents = [];

            if ($execAvailable && $composerAvailable) {

                $oOutput->writeln('');
                $oOutput->writeln('<info>Nails Components</info>');

                $question          = 'Would you like to define components to enable now?';
                $installComponents = $this->confirm($question, false);

                //  Show a list of already installed components
                $installedComponents = _NAILS_GET_COMPONENTS();

                //  I know the variables are almost the same name. Just to confuse ya. >_<
                if ($installComponents) {

                    if ($installedComponents) {

                        $oOutput->writeln('');
                        $oOutput->writeln('The following components are already installed:');
                        $oOutput->writeln('');

                        foreach ($installedComponents as $component) {
                            $oOutput->writeln(' - <info>' . $component->slug . '</info>');
                        }

                        $oOutput->writeln('');
                    }
                }

                $installTheseComponents = [];

                while ($installComponents) {

                    $component = $this->requestComponent();

                    if (is_array($component)) {
                        $installTheseComponents[$component[0]] = $component[1];
                    }

                    $oOutput->writeln('');
                    $question          = 'Would you like to enable another component?';
                    $installComponents = $this->confirm($question, false);
                }
            }

            // --------------------------------------------------------------------------

            //  Tell user what's about to happen
            $oOutput->writeln('<info>I\'m about to do the following:</info>');

            //  app.php
            $oOutput->writeln('');
            $oOutput->writeln('Write <info>config/app.php</info>');

            foreach ($appVars as &$v) {

                if (is_string($v)) {
                    continue;
                }

                $sValue = $v['value'] ?: '<blank>';
                $oOutput->writeln(' - Set <comment>' . $v['label'] . '</comment> to <comment>' . $sValue . '</comment>');
            }

            //  deploy.php
            $oOutput->writeln('');
            $oOutput->writeln('Write <info>config/deploy.php</info>');

            foreach ($deployVars as &$v) {

                if (is_string($v)) {
                    continue;
                }

                $sValue = $v['value'] ?: '<blank>';
                $oOutput->writeln(' - Set <comment>' . $v['label'] . '</comment> to <comment>' . $sValue . '</comment>');
            }

            //  Install components
            if (!empty($installTheseComponents)) {

                $oOutput->writeln('');

                if (count($installTheseComponents) > 1) {
                    $oOutput->writeln('The following components will be installed:');
                } else {
                    $oOutput->writeln('The following component will be installed:');
                }

                foreach ($installTheseComponents as $componentName => $componentVersion) {
                    $oOutput->writeln(' - <comment>' . $componentName . ':' . $componentVersion . '</comment>');
                }
            }

            //  Migrate databases
            $oOutput->writeln('');
            $oOutput->writeln('Migrate the database');

            $oOutput->writeln('');
            $question  = 'Does this look OK?';
            $doInstall = $this->confirm($question, true);

            if ($doInstall) {

                $curStep  = 1;
                $numSteps = 1; //  app.php
                $numSteps += 1; //  deploy.php
                $numSteps += !empty($installTheseComponents) ? 1 : 0;
                $numSteps += 1; //  migrate DB.php
                $numSteps += !empty($users) ? 1 : 0;

                $oOutput->writeln('');
                $oOutput->writeln('<info>Installing...</info>');

                //  Write app.php
                $oOutput->write('<comment>[' . $curStep . '/' . $numSteps . ']</comment> Writing <info>config/app.php</info>... ');
                if ($this->writeFile($appVars, 'config/app.php')) {
                    $oOutput->writeln('<info>DONE</info>');
                } else {
                    $oOutput->writeln('<error>FAILED</error>');
                }
                $curStep++;

                //  Write deploy.php
                $oOutput->write('<comment>[' . $curStep . '/' . $numSteps . ']</comment> Writing <info>config/deploy.php</info>... ');
                if ($this->writeFile($deployVars, 'config/deploy.php')) {
                    $oOutput->writeln('<info>DONE</info>');
                } else {
                    $oOutput->writeln('<error>FAILED</error>');
                }
                $curStep++;

                //  Install Components
                if (!empty($installTheseComponents)) {

                    $oOutput->writeln('<comment>[' . $curStep . '/' . $numSteps . ']</comment> Installing components</info>...');

                    foreach ($installTheseComponents as $componentName => $componentVersion) {
                        $oOutput->write(' - <comment>' . $componentName . ':' . $componentVersion . '</comment>... ');
                        $this->installComponent($componentName, $componentVersion, $oOutput);
                    }
                    $curStep++;
                }

                /**
                 * Get the current database credential values. If an existing deploy.php is already there then
                 * we can't rely on it having the latest details (as it might have just been updated).
                 */

                $dbHost = $this->getVarValue('DEPLOY_DB_HOST', $deployVars);
                $dbUser = $this->getVarValue('DEPLOY_DB_USERNAME', $deployVars);
                $dbPass = $this->getVarValue('DEPLOY_DB_PASSWORD', $deployVars);
                $dbName = $this->getVarValue('DEPLOY_DB_DATABASE', $deployVars);

                //  Migrate DB
                $oOutput->write('<comment>[' . $curStep . '/' . $numSteps . ']</comment> Migrating database... ');
                $this->migrateDb($dbHost, $dbUser, $dbPass, $dbName);

                // --------------------------------------------------------------------------

                //  Cleaning up
                $oOutput->writeln('');
                $oOutput->writeln('<comment>Cleaning up...</comment>');

                //  And we're done!
                $oOutput->writeln('');
                $oOutput->writeln('Complete!');

                return self::EXIT_CODE_SUCCESS;

            } else {

                return $this->abort();
            }

        } else {

            $exitCode = $this->abort();

            foreach ($preTestErrors as $error) {
                $oOutput->writeln(' - ' . $error);
            }

            return $exitCode;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the Nails Component Installer
     *
     * @param  string $component The component to install
     * @return int
     */
    protected function executeComponentInstaller($component)
    {
        $oOutput = $this->oOutput;

        // --------------------------------------------------------------------------

        $oOutput->writeln('<info>-------------------------</info>');
        $oOutput->writeln('<info>Nails Component Installer</info>');
        $oOutput->writeln('<info>-------------------------</info>');

        // --------------------------------------------------------------------------

        //  Get the Component
        $component = $this->requestComponent($component);

        if (!$component) {
            return $this->abort(static::EXIT_CODE_SUCCESS);
        }

        //  Confirm with user
        $oOutput->writeln('');
        $oOutput->writeln('<info>I\'m about to do the following:</info>');
        $oOutput->writeln(' - Install <info>' . $component[0] . ':' . $component[1] . '</info>');
        $oOutput->writeln(' - Migrate the database');
        $oOutput->writeln('');

        $question  = 'Continue?';
        $doInstall = $this->confirm($question, true);

        if ($doInstall) {

            //  Attempt to install
            $oOutput->writeln('');
            $oOutput->write('<comment>[1/2]</comment> Installing <info>' . $component[0] . ':' . $component[1] . '</info>... ');
            if (!$this->installComponent($component[0], $component[1], $oOutput)) {
                return $this->abort(static::EXIT_CODE_INSTALL_FAILED);
            }

            //  Migrate DB
            $oOutput->write('<comment>[2/2]</comment> Migrating database... ');
            if (!$this->migrateDb()) {
                return $this->abort(static::EXIT_CODE_MIGRATE_FAILED);
            }

            //  Cleaning up
            $oOutput->writeln('');
            $oOutput->writeln('<comment>Cleaning up...</comment>');

            //  And we're done!
            $oOutput->writeln('');
            $oOutput->writeln('Complete!');

            return self::EXIT_CODE_SUCCESS;

        } else {
            return $this->abort(static::EXIT_CODE_SUCCESS);
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
        $vars   = [];
        $vars[] = '// App Constants';
        $vars[] = [
            'key'   => 'APP_NAME',
            'label' => 'App Name',
            'value' => defined('APP_NAME') ? APP_NAME : 'Untitled',
        ];

        $vars[] = [
            'key'      => 'APP_DEFAULT_TIMEZONE',
            'label'    => 'App Timezone',
            'value'    => defined('APP_DEFAULT_TIMEZONE') ? APP_DEFAULT_TIMEZONE : date_default_timezone_get(),
            'validate' => function ($sInput) {
                return in_array($sInput, timezone_identifiers_list());
            },
        ];

        $vars[] = [
            'key'   => 'APP_PRIVATE_KEY',
            'label' => 'App Private Key',
            'value' => defined('APP_PRIVATE_KEY') && !empty(APP_PRIVATE_KEY) ? APP_PRIVATE_KEY : md5(rand(0, 1000) . microtime(true)),
        ];

        $vars[] = [
            'key'   => 'APP_DEVELOPER_EMAIL',
            'label' => 'Developer Email',
            'value' => defined('APP_DEVELOPER_EMAIL') ? APP_DEVELOPER_EMAIL : '',
        ];

        $vars[] = [
            'key'   => 'APP_DB_PREFIX',
            'label' => 'Prefix for app database tables',
            'value' => defined('APP_DB_PREFIX') ? 'app_' : '',
        ];

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
        $vars   = [];
        $vars[] = [
            'key'      => 'ENVIRONMENT',
            'label'    => 'Environment',
            'value'    => Environment::get() ?: 'DEVELOPMENT',
            'options'  => ['DEVELOPMENT', 'STAGING', 'PRODUCTION'],
            'callback' => function ($sInput) {
                return trim(strtoupper($sInput));
            },
        ];

        $vars[] = [
            'key'      => 'BASE_URL',
            'label'    => 'Base URL',
            'value'    => defined('BASE_URL') ? BASE_URL : '',
            'callback' => function ($sInput) {
                return trim(rtrim($sInput, '/') . '/');
            },
        ];

        $vars[] = [
            'key'   => 'DEPLOY_PRIVATE_KEY',
            'label' => 'Deployment Private Key',
            'value' => defined('DEPLOY_PRIVATE_KEY') ? DEPLOY_PRIVATE_KEY : md5(rand(0, 1000) . microtime(true)),
        ];

        $vars[] = [
            'key'   => 'DEPLOY_DB_HOST',
            'label' => 'Database Host',
            'value' => defined('DEPLOY_DB_HOST') ? DEPLOY_DB_HOST : 'localhost',
        ];

        $vars[] = [
            'key'   => 'DEPLOY_DB_USERNAME',
            'label' => 'Database User',
            'value' => defined('DEPLOY_DB_USERNAME') ? DEPLOY_DB_USERNAME : '',
        ];

        $vars[] = [
            'key'   => 'DEPLOY_DB_PASSWORD',
            'label' => 'Database Password',
            'value' => defined('DEPLOY_DB_PASSWORD') ? DEPLOY_DB_PASSWORD : '',
        ];

        $vars[] = [
            'key'   => 'DEPLOY_DB_DATABASE',
            'label' => 'Database Name',
            'value' => defined('DEPLOY_DB_DATABASE') ? DEPLOY_DB_DATABASE : '',
        ];

        $vars[] = [
            'key'   => 'DEPLOY_EMAIL_HOST',
            'label' => 'Email Host',
            'value' => defined('DEPLOY_EMAIL_HOST') ? DEPLOY_EMAIL_HOST : 'localhost',
        ];

        $vars[] = [
            'key'   => 'DEPLOY_EMAIL_USER',
            'label' => 'Email Username',
            'value' => defined('DEPLOY_EMAIL_USER') ? DEPLOY_EMAIL_USER : '',
        ];

        $vars[] = [
            'key'   => 'DEPLOY_EMAIL_PASS',
            'label' => 'Email Password',
            'value' => defined('DEPLOY_EMAIL_PASS') ? DEPLOY_EMAIL_PASS : '',
        ];

        $vars[] = [
            'key'   => 'DEPLOY_EMAIL_PORT',
            'label' => 'Email Port',
            'value' => defined('DEPLOY_EMAIL_PORT') ? DEPLOY_EMAIL_PORT : 25,
        ];

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
     * @param  string $key The key to return
     * @param  array $vars The variable array to look at
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
     * @param  array $vars The existing variables to check against (so only new variables are returned)
     * @return array
     */
    private function getConstantsFromFile($path, $vars = [])
    {
        $out = [];

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

                        $out[] = [
                            'key'     => $matches[1][$i],
                            'label'   => $name,
                            'value'   => defined($matches[1][$i]) ? constant($matches[1][$i]) : '',
                            'options' => [],
                        ];
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
     * @param  array $vars The existing variables to check against (so only new variables are returned)
     * @return array
     */
    private function getConstantsFromComponents($type, $vars = [])
    {
        //  @TODO: Look for components
        return [];
    }

    // --------------------------------------------------------------------------

    /**
     * Checks for the existence and writeability of the config files and directory
     *
     * @return array
     */
    private function preInstallTests()
    {
        $preTestErrors      = [];
        $appConfigExists    = file_exists('config/app.php');
        $deployConfigExists = file_exists('config/deploy.php');

        //  If config/app.php is there is it writable?
        if ($appConfigExists) {

            if (!is_writable(FCPATH . 'config/app.php')) {

                $preTestErrors[] = '<comment>config/app.php</comment> exists, but is not writable.';
            }
        }

        //  If config/deploy.php is there, is it writable?
        if ($deployConfigExists) {

            if (!is_writable(FCPATH . 'config/deploy.php')) {

                $preTestErrors[] = '<comment>config/app.php</comment> exists, but is not writable.';
            }
        }

        //  If a file is missing we need to be able to write to the directory.
        if (!$appConfigExists || !$deployConfigExists) {

            if (!is_writable(FCPATH . 'config/')) {
                $preTestErrors[] = '<comment>config/</comment> is not writable.';
            }
        }

        return $preTestErrors;
    }

    // --------------------------------------------------------------------------

    /**
     * Requests the user to confirm all the variables
     *
     * @param array &$vars An array of the variables to set
     */
    private function setVars(&$vars)
    {
        foreach ($vars as &$v) {

            if (is_array($v)) {

                $question = 'What should "' . $v['label'] . '" be set to?';

                if (!empty($v['options'])) {

                    $question .= ' (' . implode('|', $v['options']) . ')';

                    //  The field has options, ensure the option selected is valid
                    do {

                        $v['value'] = $this->ask($question, $v['value']);
                        if (isset($v['callback']) && is_callable($v['callback'])) {
                            $v['value'] = call_user_func($v['callback'], $v['value']);
                        }

                        $bIsValidOption = in_array($v['value'], $v['options']);
                        $question       = '<error>Selection must be one of ' . implode(', ', $v['options']) . '</error>';

                    } while (!$bIsValidOption);


                } elseif (isset($v['validate']) && is_callable($v['validate'])) {

                    //  Validator, keep asking until validator passes
                    do {

                        $v['value'] = $this->ask($question, $v['value']);
                        if (isset($v['callback']) && is_callable($v['callback'])) {
                            $v['value'] = call_user_func($v['callback'], $v['value']);
                        }

                        $question = '<error>Sorry, that is not a valid selection.</error>';

                    } while (!call_user_func($v['validate'], $v['value']));

                } else {
                    //  No validator, just ask and accept what's given
                    $v['value'] = $this->ask($question, $v['value']);
                    if (isset($v['callback']) && is_callable($v['callback'])) {
                        $v['value'] = call_user_func($v['callback'], $v['value']);
                    }
                }
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Writes the supplied variables to the config file
     *
     * @param  array $vars The variables to write
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
     * @param  string $componentName The name of the component to install
     * @param  string $componentVersion The version of the component to install
     * @param  OutputInterface $oOutput The Output Interface provided by Symfony
     * @return boolean
     */
    private function installComponent($componentName, $componentVersion, $oOutput)
    {
        $composerBin = $this->detectComposerBin();

        exec($composerBin . ' require ' . $componentName . ':' . $componentVersion, $execOutput, $execReturn);

        if ($execReturn !== 0) {

            $oOutput->writeln('<error>FAILED</error>');
            $oOutput->writeln('Composer failed with exit code "' . $execReturn . '"');

            return false;
        }

        $oOutput->writeln('<info>DONE</info>');

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Migrates the DB for a fresh install
     *
     * @param  string $sDbHost The database host to connect to
     * @param  string $sDbUser The database user to connect with
     * @param  string $sDbPass The database password to connect with
     * @param  string $sDbName The database name to connect to
     * @return boolean
     */
    private function migrateDb($sDbHost = null, $sDbUser = null, $sDbPass = null, $sDbName = null)
    {
        //  Execute the migrate command, non-interactively and silently
        $iExitCode = $this->callCommand(
            'db:migrate',
            [
                '--dbHost' => $sDbHost,
                '--dbUser' => $sDbUser,
                '--dbPass' => $sDbPass,
                '--dbName' => $sDbName,
            ],
            false,
            true
        );

        if ($iExitCode == 0) {

            $this->oOutput->writeln('<info>DONE</info>');

            return true;

        } else {

            $this->abort(
                self::EXIT_CODE_FAILURE,
                [
                    'The Migration tool encountered issues and aborted the migration.',
                    'You should run it manually and investigate any issues.',
                    'The exit code was ' . $exitCode,
                ]
            );

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
        $aMessages[] = 'Aborting install';
        if (!empty($this->oDb) && $this->oDb->isTransactionRunning()) {
            $aMessages[] = 'Rolling back Database';
            $this->oDb->transactionRollback();
        }

        return parent::abort($iExitCode, $aMessages);
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
     * Asks the user which component they'd like to install and validates it against
     * the Nails Components repository.
     *
     * @param  string $componentName The component name to install
     * @return array|bool
     */
    private function requestComponent($componentName = '')
    {
        $oOutput = $this->oOutput;

        // --------------------------------------------------------------------------

        //  Get the component
        do {

            //  If a component name has been specified, check to see if it's valid
            if (!empty($componentName)) {

                /**
                 * A component name has been given, check it out against the Nails
                 * Component repository to see if it's valid.
                 */

                $isValidComponent = $this->isValidComponent($componentName, $oOutput);

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

                $oOutput->writeln('');
                $question      = 'Enter the component name you\'d like to install';
                $componentName = $this->ask($question, '');

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
            $componentVersion = $this->ask($question, 'dev-develop');

            return [$componentName, $componentVersion];

        } else {

            $oOutput->writeln('');
            $oOutput->writeln('<info>' . $componentName . '</info> is already installed.');

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Searches Nails components Repository for the component name.
     *
     * @param  string $componentName The component's name
     * @param  OutputInterface $oOutput The Output Interface provided by Symfony
     * @return mixed                        String on success (component's full name), false on failure
     */
    protected function isValidComponent($componentName, $oOutput)
    {
        $result = @file_get_contents($this->componentEndpoint . 'api/search?term=' . urlencode($componentName));

        if (empty($result)) {

            $oOutput->writeln('');
            $oOutput->writeln('<error>ERROR</error>');
            $oOutput->writeln('Query to ' . $this->componentEndpoint . ' failed. Could not validate component.');
            $oOutput->writeln('');

            return false;
        }

        $result = json_decode($result);

        if (empty($result)) {

            $oOutput->writeln('');
            $oOutput->writeln('<error>ERROR</error>');
            $oOutput->writeln('Failed to decode search results from ' . $this->componentEndpoint);
            $oOutput->writeln('');

            return false;
        }

        if (empty($result->results)) {

            return false;

        } elseif (count($result->results) > 1) {

            $oOutput->writeln('');
            $oOutput->writeln('More than 1 component for <info>' . $componentName . '</info>. Did you mean:</comment>');

            foreach ($result->results as $component) {

                $url = $component->homepage;
                $url = !$url ? $component->repository : $url;

                $oOutput->writeln('');
                $oOutput->writeln(' - <info>' . $component->name . '</info>');

                if (!empty($component->description)) {

                    $oOutput->writeln('   ' . $component->description);
                }

                if (!empty($url)) {

                    $oOutput->writeln('   ' . $url);
                }
            }

            return false;

        } else {

            return $result->results[0]->name;
        }
    }
}
