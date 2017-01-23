<?php

namespace Nails\Common\Console\Command\Model;

use Nails\Common\Exception\Database\ConnectionException;
use Nails\Console\Command\Base;
use Nails\Environment;
use Nails\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class Create extends Base
{
    const MODEL_PATH            = FCPATH . 'src/Model/';
    const ADMIN_PATH            = FCPATH . 'application/modules/admin/controllers/';
    const MODEL_PATH_PERMISSION = 0755;

    // --------------------------------------------------------------------------

    /**
     * Configure the command
     */
    protected function configure()
    {
        $this->setName('model:create');
        $this->setDescription('[WIP] Creates a new App Model');
        $this->addArgument(
            'modelName',
            InputArgument::OPTIONAL,
            'Define the name of the model to create'
        );
        $this->addOption(
            'skip-db',
            null,
            InputOption::VALUE_OPTIONAL,
            'Skip database table creation'
        );
        $this->addOption(
            'skip-admin',
            null,
            InputOption::VALUE_OPTIONAL,
            'Skip admin creation'
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
        $oOutput->writeln('');
        $oOutput->writeln('<info>----------------</info>');
        $oOutput->writeln('<info>Nails Model Tool</info>');
        $oOutput->writeln('<info>----------------</info>');

        // --------------------------------------------------------------------------

        //  Setup Factory - config files are required prior to set up
        Factory::setup();

        // --------------------------------------------------------------------------

        //  Check environment
        if (Environment::not('DEVELOPMENT')) {
            return $this->abort(
                $oOutput,
                self::EXIT_CODE_FAILURE,
                'This tool is only available on DEVELOPMENT environments'
            );
        }

        // --------------------------------------------------------------------------

        //  Test database connection
        $bSkipDb = stringToBoolean($oInput->getOption('skip-db'));
        if (!$bSkipDb) {
            try {
                Factory::service('ConsoleDatabase', 'nailsapp/module-console');
            } catch (ConnectionException $e) {
                return $this->abort(
                    $oOutput,
                    self::EXIT_CODE_FAILURE,
                    [
                        'Failed to connect to the database.',
                        $e->getMessage(),
                    ]
                );
            } catch (\Exception $e) {
                return $this->abort(
                    $oOutput,
                    self::EXIT_CODE_FAILURE,
                    [
                        'An exception occurred when connecting to the database.',
                        $e->getMessage(),
                    ]
                );
            }
        }

        // --------------------------------------------------------------------------

        //  Check we can write where we need to write
        if (!is_dir(self::MODEL_PATH)) {
            if (!mkdir(self::MODEL_PATH, self::MODEL_PATH_PERMISSION, true)) {
                return $this->abort(
                    $oOutput,
                    self::EXIT_CODE_FAILURE,
                    [
                        'Model directory does not exist and could not be created',
                        self::MODEL_PATH,
                    ]
                );
            }
        } elseif (!is_writable(self::MODEL_PATH)) {
            return $this->abort(
                $oOutput,
                self::EXIT_CODE_FAILURE,
                [
                    'Model directory exists but is not writeable',
                    self::MODEL_PATH,
                ]
            );
        }

        $bSkipAdmin = !isModuleEnabled('nailsapp/module-admin') || stringToBoolean($oInput->getOption('skip-admin'));
        if (!$bSkipAdmin) {
            if (!is_dir(self::ADMIN_PATH)) {
                if (!mkdir(self::ADMIN_PATH, self::MODEL_PATH_PERMISSION, true)) {
                    return $this->abort(
                        $oOutput,
                        self::EXIT_CODE_FAILURE,
                        [
                            'Admin controller directory does not exist and could not be created',
                            self::ADMIN_PATH,
                        ]
                    );
                }
            } elseif (!is_writable(self::ADMIN_PATH)) {
                return $this->abort(
                    $oOutput,
                    self::EXIT_CODE_FAILURE,
                    [
                        'Admin controller directory exists but is not writeable',
                        self::ADMIN_PATH,
                    ]
                );
            }
        }

        // --------------------------------------------------------------------------

        //  Detect the services file
        $sServicesPath = FCPATH . APPPATH . 'services/services.php';
        if (!file_exists($sServicesPath)) {
            return $this->abort(
                $oOutput,
                self::EXIT_CODE_FAILURE,
                [
                    'Could not detect the app\'s services.php file',
                    $sServicesPath,
                ]
            );
        }

        // --------------------------------------------------------------------------

        //  Get field names
        $aFields = [
            'MODEL_NAME' => $oInput->getArgument('modelName') ?: '',
        ];
        foreach ($aFields as $sField => &$sValue) {
            if (empty($sValue)) {
                $sField = ucwords(strtolower(str_replace('_', ' ', $sField)));
                $sError = '';
                do {
                    $sValue = $this->ask($sError . $sField . ':', '', $oInput, $oOutput);
                    $sError = '<error>Please specify</error> ';
                } while (empty($sValue));
            }
        }
        unset($sValue);

        // --------------------------------------------------------------------------

        $oOutput->writeln('');
        try {
            $this->createModel($aFields, $oInput, $oOutput);
        } catch (\Exception $e) {
            return $this->abort(
                $oOutput,
                self::EXIT_CODE_FAILURE,
                [
                    $e->getMessage(),
                ]
            );
        }

        // --------------------------------------------------------------------------

        //  Cleaning up
        $oOutput->writeln('');
        $oOutput->writeln('<comment>Cleaning up...</comment>');

        // --------------------------------------------------------------------------

        //  And we're done
        $oOutput->writeln('');
        $oOutput->writeln('Complete!');

        return self::EXIT_CODE_SUCCESS;
    }

    // --------------------------------------------------------------------------

    /**
     * Create the Model
     *
     * @param array $aFields The details to create the Model with
     * @param InputInterface $oInput The Input Interface provided by Symfony
     * @param OutputInterface $oOutput The Output Interface provided by Symfony
     * @throws \Exception
     * @return int
     */
    private function createModel($aFields, $oInput, $oOutput)
    {
        try {

            $bSkipDb    = stringToBoolean($oInput->getOption('skip-db'));
            $bSkipAdmin = !isModuleEnabled('nailsapp/module-admin') || stringToBoolean($oInput->getOption('skip-admin'));
            $aModels    = explode(',', $aFields['MODEL_NAME']);
            $aModelData = [];
            foreach ($aModels as $sModelName) {
                $aModelData[] = $this->prepareModelName($oInput, $sModelName);
            }

            Factory::helper('array');
            array_sort_multi($aModelData, 'class_path');

            //  Report to the user and get confirmation
            $oOutput->writeln('The following models will be created:');
            $oOutput->writeln('');
            foreach ($aModelData as $oModel) {
                $oOutput->writeln('Class: <info>\\' . $oModel->class_path . '</info>');
                $oOutput->writeln('Path:  <info>' . $oModel->path . '/' . $oModel->filename . '</info>');
                if (!$bSkipDb) {
                    $oOutput->writeln('Table: <info>' . $oModel->table . '</info>');
                }
                $oOutput->writeln('');
            }

            if ($this->confirm('Continue?', true, $oInput, $oOutput)) {

                foreach ($aModelData as $oModel) {

                    $oOutput->writeln('');
                    $oOutput->write('Creating model <comment>' . $oModel->class_path . '</comment>... ');
                    if (!is_dir($oModel->path)) {
                        //  Attempt to create the containing directory
                        if (!mkdir($oModel->path, static::MODEL_PATH_PERMISSION, true)) {
                            throw new \Exception('Failed to create model directory "' . $oModel->path . '"');
                        }
                    }

                    //  Create the model file
                    $sFile   = $oModel->path . $oModel->filename;
                    $hHandle = fopen($sFile, 'w');
                    if (!$hHandle) {
                        throw new \Exception('Failed to open ' . $sFile . ' for writing');
                    }

                    if (fwrite($hHandle, $this->getResource('model.php', (array) $oModel)) === false) {
                        throw new \Exception('Failed to write to ' . $sFile);
                    }

                    fclose($hHandle);

                    $oOutput->writeln('<info>done!</info>');

                    //  Add to the app's services array
                    $oOutput->write('Adding model to app services...');
                    //  Read the services file into memory
                    //  @todo
                    //  Export contents of array with var_export and parse in new items
                    //  @todo
                    //  Add the new service definition at the appropriate bit
                    //  @todo
                    //  Overwrite the file
                    //  @todo
                    $oOutput->writeln('<info>done!</info>');

                    //  Create admin
                    if (!$bSkipAdmin) {
                        $oOutput->write('Creating admin controller...');
                        //  @todo
                        $oOutput->writeln('<info>done!</info>');
                    }

                    //  Create the database table
                    if (!$bSkipDb) {
                        $oOutput->write('Adding database table...');
                        $oModel->nails_db_prefix = NAILS_DB_PREFIX;
                        $oDb                     = Factory::service('ConsoleDatabase', 'nailsapp/module-console');
                        $oDb->query($this->getResource('model_table.php', (array) $oModel));
                        $oOutput->writeln('<info>done!</info>');
                    }
                }
            }

        } catch (\Exception $e) {

            //  Clean up
            if (!empty($aModelData)) {
                foreach ($aModelData as $oModel) {
                    @unlink($oModel->path . '/' . $oModel->filename);
                }
            }
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Prepare the model details and do some preliminary checks
     * @param object $oInput The input class
     * @param string $sModelName The name of the model
     * @return object
     * @throws \Exception
     */
    private function prepareModelName($oInput, $sModelName)
    {
        //  Prepare the supplied model name and test
        $sModelName = preg_replace('/[^a-zA-Z\/ ]/', '', $sModelName);
        $sModelName = preg_replace('/\//', ' \\ ', $sModelName);
        $sModelName = ucwords($sModelName);

        //  Prepare the database table name
        $sTableName = preg_replace('/[^a-z ]/', '', strtolower($sModelName));
        $sTableName = preg_replace('/ +/', '_', $sTableName);
        if (defined('APP_DB_PREFIX')) {
            $sTableName = APP_DB_PREFIX . $sTableName;
        }

        //  Prepare the model name, namespace, filename etc
        $sModelName = 'App\Model\\' . preg_replace('/ /', '', $sModelName);
        $aModelName = explode('\\', $sModelName);
        $aNamespace = array_splice($aModelName, 0, -1);
        $sClassName = implode('', $aModelName);
        $sFilename  = $sClassName . '.php';
        $sPath      = static::MODEL_PATH . implode('/', array_slice($aNamespace, 2));
        $sPath      = substr($sPath, -1) !== '/' ? $sPath . '/' : $sPath;
        $sNamespace = implode('\\', $aNamespace);

        if (file_exists($sPath . $sFilename)) {
            throw new \Exception('A model by that path already exists "' . $sPath . $sFilename . '"');
        }

        //  Test to see if the database table exists already
        if (!stringToBoolean($oInput->getOption('skip-db'))) {
            $oDb     = Factory::service('ConsoleDatabase', 'nailsapp/module-console');
            $oResult = $oDb->query('SHOW TABLES LIKE "' . $sTableName . '"');
            if ($oResult->rowCount() > 0) {
                throw new \Exception(
                    'Table "' . $sTableName . '" already exists. Use option --skip-db to skip database checks.'
                );
            }
        }

        return (object) [
            'namespace'        => $sNamespace,
            'class_name'       => $sClassName,
            'class_path'       => $sNamespace . '\\' . $sClassName,
            'path'             => $sPath,
            'filename'         => $sFilename,
            'table'            => $sTableName,
            'admin_class_name' => '@todo',
            'service_name'     => '@todo',
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Get a resource and substitute fields into it
     *
     * @param string $sFile The file to fetch
     * @param array $aFields The template fields
     * @return string
     */
    private function getResource($sFile, $aFields)
    {
        $sResource = require NAILS_COMMON_PATH . 'resources/console/template/' . $sFile;

        foreach ($aFields as $sField => $sValue) {
            $sKey      = '{{' . strtoupper($sField) . '}}';
            $sResource = str_replace($sKey, $sValue, $sResource);
        }

        return $sResource;
    }
}
