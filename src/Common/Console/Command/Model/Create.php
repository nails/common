<?php

namespace Nails\Common\Console\Command\Model;

use Nails\Common\Exception\Database\ConnectionException;
use Nails\Console\Command\BaseMaker;
use Nails\Factory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends BaseMaker
{
    const RESOURCE_PATH = NAILS_COMMON_PATH . 'resources/console/';
    const MODEL_PATH    = FCPATH . 'src/Model/';
    const ADMIN_PATH    = FCPATH . 'application/modules/admin/controllers/';

    // --------------------------------------------------------------------------

    /**
     * Configure the command
     */
    protected function configure()
    {
        $this->setName('make:model');
        $this->setDescription('[WIP] Creates a new App model');
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
        parent::execute($oInput, $oOutput);

        // --------------------------------------------------------------------------

        //  Get options
        $bSkipDb    = stringToBoolean($oInput->getOption('skip-db'));
        $bSkipAdmin = !isModuleEnabled('nailsapp/module-admin') || stringToBoolean($oInput->getOption('skip-admin'));

        // --------------------------------------------------------------------------

        //  Test database connection
        if (!$bSkipDb) {
            try {
                Factory::service('ConsoleDatabase', 'nailsapp/module-console');
            } catch (ConnectionException $e) {
                return $this->abort(
                    self::EXIT_CODE_FAILURE,
                    [
                        'Failed to connect to the database.',
                        $e->getMessage(),
                    ]
                );
            } catch (\Exception $e) {
                return $this->abort(
                    self::EXIT_CODE_FAILURE,
                    [
                        'An exception occurred when connecting to the database.',
                        $e->getMessage(),
                    ]
                );
            }
        }

        // --------------------------------------------------------------------------

        //  Detect the services file
        $sServicesPath = FCPATH . APPPATH . 'services/services.php';
        if (!file_exists($sServicesPath)) {
            return $this->abort(
                self::EXIT_CODE_FAILURE,
                [
                    'Could not detect the app\'s services.php file',
                    $sServicesPath,
                ]
            );
        }

        // --------------------------------------------------------------------------

        try {
            //  Ensure the paths exist
            $this->createPath(self::MODEL_PATH);
            if (!$bSkipAdmin) {
                $this->createPath(self::ADMIN_PATH);
            }
            //  Create the model
            $this->createModel();
        } catch (\Exception $e) {
            return $this->abort(
                self::EXIT_CODE_FAILURE,
                $e->getMessage()
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
     * @throws \Exception
     * @return void
     */
    private function createModel()
    {
        $oInput     = $this->oInput;
        $oOutput    = $this->oOutput;
        $aFields    = $this->getArguments();
        $bSkipDb    = stringToBoolean($oInput->getOption('skip-db'));
        $bSkipAdmin = !isModuleEnabled('nailsapp/module-admin') || stringToBoolean($oInput->getOption('skip-admin'));

        try {

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
                $oOutput->writeln('Path:  <info>' . $oModel->path . $oModel->filename . '</info>');
                if (!$bSkipDb) {
                    $oOutput->writeln('Table: <info>' . $oModel->table . '</info>');
                }
                $oOutput->writeln('');
            }

            if ($this->confirm('Continue?', true)) {

                foreach ($aModelData as $oModel) {

                    $oOutput->writeln('');
                    $oOutput->write('Creating model <comment>' . $oModel->class_path . '</comment>... ');

                    //  Ensure path exists
                    $this->createPath($oModel->path);

                    //  Create the model file
                    $this->createFile(
                        $oModel->path . $oModel->filename,
                        $this->getResource('template/model.php', (array) $oModel)
                    );

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
                        $oDb->query($this->getResource('template/model_table.php', (array) $oModel));
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
            throw new \Exception(
                $e->getMessage(),
                $e->getCode()
            );
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
}
