<?php

namespace Nails\Common\Console\Command\Make;

use Nails\Common\Exception\NailsException;
use Nails\Common\Exception\Database\ConnectionException;
use Nails\Console\Command\BaseMaker;
use Nails\Components;
use Nails\Factory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Model extends BaseMaker
{
    const SERVICE_TOKEN          = 'MODELS';
    const SERVICE_RESOURCE_TOKEN = 'RESOURCES';
    const RESOURCE_PATH          = NAILS_COMMON_PATH . 'resources/console/';
    const MODEL_PATH             = NAILS_APP_PATH . 'src/Model/';
    const MODEL_RESOURCE_PATH    = NAILS_APP_PATH . 'src/Resource/';
    const ADMIN_PATH             = NAILS_APP_PATH . 'application/modules/admin/controllers/';

    // --------------------------------------------------------------------------

    /**
     * Configure the command
     */
    protected function configure(): void
    {
        $this
            ->setName('make:model')
            ->setDescription('Creates a new App model')
            ->addArgument(
                'modelName',
                InputArgument::OPTIONAL,
                'Define the name of the model to create'
            )
            ->addOption(
                'skip-db',
                null,
                InputOption::VALUE_OPTIONAL,
                'Skip database table creation',
                false
            )
            ->addOption(
                'skip-seeder',
                null,
                InputOption::VALUE_OPTIONAL,
                'Skip seeder creation',
                false
            )
            ->addOption(
                'auto-detect',
                null,
                InputOption::VALUE_OPTIONAL,
                'Automatically build models from the database'
            );

        if (Components::exists('nails/module-admin')) {
            $this->addOption(
                'skip-admin',
                null,
                InputOption::VALUE_OPTIONAL,
                'Skip admin creation',
                true
            );
        }
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
    protected function execute(InputInterface $oInput, OutputInterface $oOutput): int
    {
        parent::execute($oInput, $oOutput);

        // --------------------------------------------------------------------------

        //  Get options
        $bSkipDb    = stringToBoolean($oInput->getOption('skip-db'));
        $bSkipAdmin = !Components::exists('nails/module-admin') || stringToBoolean($oInput->getOption('skip-admin'));

        // --------------------------------------------------------------------------

        //  Test database connection
        if (!$bSkipDb) {
            try {
                Factory::service('PDODatabase');
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

        try {
            $this
                /**
                 * Validate the services file for resources first, so that the corrcet
                 * values are set on the second call.
                 */
                ->validateServiceFile(static::SERVICE_RESOURCE_TOKEN)
                ->validateServiceFile()
                ->createPath(self::MODEL_PATH);
            if (!$bSkipAdmin) {
                $this->createPath(self::ADMIN_PATH);
            }
            $this->createModel();
        } catch (\Exception $e) {
            return $this->abort(
                self::EXIT_CODE_FAILURE,
                [$e->getMessage()]
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
     * @return $this
     */
    private function createModel(): self
    {
        $oInput      = $this->oInput;
        $oOutput     = $this->oOutput;
        $bSkipDb     = stringToBoolean($oInput->getOption('skip-db'));
        $bSkipSeeder = stringToBoolean($oInput->getOption('skip-seeder'));
        $bSkipAdmin  = !Components::exists('nails/module-admin') || stringToBoolean($oInput->getOption('skip-admin'));
        $bAutoDetect = stringToBoolean($oInput->getOption('auto-detect'));

        //  If auto-detecting models using the database then skip cresting database tables
        if ($bAutoDetect) {
            $bSkipDb = true;
        }

        try {

            if (!$bAutoDetect) {

                $aFields    = $this->getArguments();
                $aModels    = explode(',', $aFields['MODEL_NAME']);
                $aModelData = [];
                sort($aModels);
                foreach ($aModels as $sModelName) {
                    $oModel = $this->prepareModelName($sModelName);
                    if ($this->modelExists($oModel)) {
                        throw new NailsException(
                            'A model by that path already exists "' . $oModel->path . $oModel->filename . '"'
                        );
                    }

                    //  Test to see if the database table exists already
                    if (!$bSkipDb) {
                        $oDb     = Factory::service('PDODatabase');
                        $oResult = $oDb->query('SHOW TABLES LIKE "' . $oModel->table_with_prefix . '"');
                        if ($oResult->rowCount() > 0) {
                            throw new NailsException(
                                'Table "' . $oModel->table_with_prefix . '" already exists. Use option --skip-db to skip database check.'
                            );
                        }
                    }

                    $aModelData[] = $oModel;
                }

            } else {

                $oDb        = Factory::service('PDODatabase');
                $sAppPrefix = defined('APP_DB_PREFIX') ? APP_DB_PREFIX : '';
                $oTables    = $oDb->query('SHOW TABLES LIKE "' . $sAppPrefix . '%";');
                $aTables    = $oTables->fetchAll(\PDO::FETCH_NUM);

                //  If APP_DB_PREFIX is empty then the query above will have all the Nails table's too,
                //  filter them out.
                if (empty($sAppPrefix)) {
                    $aTables = array_filter(
                        $aTables,
                        function ($aTable) {
                            return !preg_match('/^' . NAILS_DB_PREFIX . '/', $aTable[0]);
                        }
                    );
                    $aTables = array_values($aTables);
                }

                $aModelData = array_map(
                    function ($aTable) use ($sAppPrefix) {
                        $sTable = $aTable[0];
                        $sTable = preg_replace('/^' . $sAppPrefix . '/', '', $sTable);
                        $sTable = str_replace('_', ' ', $sTable);
                        $sTable = ucwords($sTable);
                        $sTable = str_replace(' ', '/', $sTable);

                        $oModel = $this->prepareModelName($sTable);

                        return $this->modelExists($oModel) ? null : $oModel;
                    },
                    $aTables
                );
                $aModelData = array_filter($aModelData);
            }

            arraySortMulti($aModelData, 'class_path');

            //  Report to the user and get confirmation
            $oOutput->writeln('The following model(s) will be created:');
            $oOutput->writeln('');
            foreach ($aModelData as $oModel) {
                $oOutput->writeln('Model Class:    <info>\\' . $oModel->class_path . '</info>');
                $oOutput->writeln('Model Path:     <info>' . $oModel->path . $oModel->filename . '</info>');
                $oOutput->writeln('Resource Class: <info>\\' . $oModel->resource_class_path . '</info>');
                $oOutput->writeln('Resource Path:  <info>' . $oModel->resource_path . $oModel->resource_filename . '</info>');
                if (!$bSkipDb) {
                    $oOutput->writeln('Table:          <info>' . $oModel->table_with_prefix . '</info>');
                }
                if (!$bSkipAdmin) {
                    $oOutput->writeln('Admin:          <info>Controller will be created</info>');
                }
                if (!$bSkipSeeder) {
                    $oOutput->writeln('Seeder:         <info>Seeder will be created</info>');
                }
                $oOutput->writeln('');
            }

            if ($this->confirm('Continue?', true)) {

                $aServiceModelDefinitions    = [];
                $aServiceResourceDefinitions = [];
                foreach ($aModelData as $oModel) {

                    $oOutput->writeln('');

                    // --------------------------------------------------------------------------

                    $oOutput->write('Creating model <comment>' . $oModel->class_path . '</comment>... ');
                    $this->createPath($oModel->path);
                    $this->createFile(
                        $oModel->path . $oModel->filename,
                        $this->getResource('template/model.php', (array) $oModel)
                    );
                    $oOutput->writeln('<info>done!</info>');

                    // --------------------------------------------------------------------------

                    $oOutput->write('Creating resource <comment>' . $oModel->resource_class_path . '</comment>... ');
                    $this->createPath($oModel->resource_path);
                    $this->createFile(
                        $oModel->resource_path . $oModel->resource_filename,
                        $this->getResource('template/resource.php', (array) $oModel)
                    );
                    $oOutput->writeln('<info>done!</info>');

                    // --------------------------------------------------------------------------

                    //  Create the database table
                    if (!$bSkipDb) {
                        $oOutput->write('Adding database table... ');
                        $oModel->nails_db_prefix = NAILS_DB_PREFIX;
                        $oDb                     = Factory::service('PDODatabase');
                        $oDb->query($this->getResource('template/model_table.php', (array) $oModel));
                        $oOutput->writeln('<info>done!</info>');
                    }

                    // --------------------------------------------------------------------------

                    //  Generate the service definition
                    $aDefinition                = [
                        str_repeat(' ', $this->iServicesIndent) . '\'' . $oModel->service_name . '\' => function () {',
                        str_repeat(' ', $this->iServicesIndent) . '    return new ' . $oModel->class_path . '();',
                        str_repeat(' ', $this->iServicesIndent) . '},',
                    ];
                    $aServiceModelDefinitions[] = implode("\n", $aDefinition);

                    $aDefinition                   = [
                        str_repeat(' ', $this->iServicesIndent) . '\'' . $oModel->service_name . '\' => function ($oObj) {',
                        str_repeat(' ', $this->iServicesIndent) . '    return new ' . $oModel->resource_class_path . '($oObj);',
                        str_repeat(' ', $this->iServicesIndent) . '},',
                    ];
                    $aServiceResourceDefinitions[] = implode("\n", $aDefinition);

                    // --------------------------------------------------------------------------

                    //  Create admin
                    if (!$bSkipAdmin) {
                        $oOutput->write('Creating admin controller... ');
                        //  Execute the create command, non-interactively and silently
                        $iExitCode = $this->callCommand(
                            'make:controller:admin',
                            [
                                'modelName'    => $oModel->service_name,
                                '--skip-check' => true,
                            ],
                            false,
                            true
                        );
                        if ($iExitCode === static::EXIT_CODE_FAILURE) {
                            $oOutput->writeln('<error>failed!</error>');
                        } else {
                            $oOutput->writeln('<info>done!</info>');
                        }
                    }

                    // --------------------------------------------------------------------------

                    //  Create seeder
                    if (!$bSkipSeeder) {
                        $oOutput->write('Creating seeder... ');
                        //  Execute the create command, non-interactively and silently
                        $iExitCode = $this->callCommand(
                            'make:db:seeder',
                            [
                                'modelName'    => $oModel->service_name,
                                '--skip-check' => true,
                            ],
                            false,
                            true
                        );
                        if ($iExitCode === static::EXIT_CODE_FAILURE) {
                            $oOutput->writeln('<error>failed!</error>');
                        } else {
                            $oOutput->writeln('<info>done!</info>');
                        }
                    }
                }

                // --------------------------------------------------------------------------

                //  Add models to the app's services array
                $oOutput->writeln('');
                $oOutput->write('Adding model(s) to app services...');
                $this->writeServiceFile($aServiceModelDefinitions);
                $oOutput->writeln('<info>done!</info>');

                //  Add resources to app's service array
                $oOutput->write('Adding resource(s) to app services...');
                //  Reset the token detials so we write to the correct part of the file
                $this->validateServiceFile(static::SERVICE_RESOURCE_TOKEN);
                $this->writeServiceFile($aServiceResourceDefinitions);
                $oOutput->writeln('<info>done!</info>');
            }

        } catch (\Exception $e) {

            //  Clean up
            if (!empty($aModelData)) {
                foreach ($aModelData as $oModel) {
                    @unlink($oModel->path . '/' . $oModel->filename);
                }
            }
            throw new NailsException(
                $e->getMessage(),
                is_numeric($e->getCode()) ? $e->getCode() : null
            );
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Prepare the model details and do some preliminary checks
     *
     * @param string $sModelName The name of the model
     *
     * @return \stdClass
     * @throws \Exception
     */
    private function prepareModelName($sModelName): \stdClass
    {
        //  Prepare the supplied model name and test
        $sModelName = preg_replace('/[^a-zA-Z\/ ]/', '', $sModelName);
        $sModelName = preg_replace('/\//', ' \\ ', $sModelName);
        $sModelName = ucwords($sModelName);

        //  Prepare the database table name
        $sTableName = preg_replace('/[^a-z ]/', '', strtolower($sModelName));
        $sTableName = preg_replace('/ +/', '_', $sTableName);
        if (defined('APP_DB_PREFIX')) {
            $sTableNameWithPrefix = APP_DB_PREFIX . $sTableName;
        } else {
            $sTableNameWithPrefix = $sTableName;
        }

        //  Prepare the model name, namespace, filename etc
        $sModelName = 'App\Model\\' . preg_replace('/ /', '', $sModelName);
        $aModelName = explode('\\', $sModelName);
        $aNamespace = array_splice($aModelName, 0, -1);
        $sClassName = implode('', $aModelName);
        $sFilename  = $sClassName . '.php';
        $sPath      = static::MODEL_PATH . implode(DIRECTORY_SEPARATOR, array_slice($aNamespace, 2));
        $sPath      = substr($sPath, -1) !== DIRECTORY_SEPARATOR ? $sPath . DIRECTORY_SEPARATOR : $sPath;
        $sNamespace = implode('\\', $aNamespace);

        //  Set the service name
        $sServiceName = str_replace('App\Model\\', '', $sModelName);
        $sServiceName = str_replace('\\', '', $sServiceName);

        //  Set resource details
        $sResourceNamespace = preg_replace('/^App\\\\Model/', 'App\\Resource', $sNamespace);
        $sResourceClassName = $sClassName;
        $sResourcePath      = static::MODEL_RESOURCE_PATH . implode(DIRECTORY_SEPARATOR, array_slice($aNamespace, 2));
        $sResourcePath      = substr($sResourcePath, -1) !== DIRECTORY_SEPARATOR ? $sResourcePath . DIRECTORY_SEPARATOR : $sResourcePath;
        $sResourceFilename  = $sFilename;


        return (object) [
            'namespace'           => $sNamespace,
            'class_name'          => $sClassName,
            'class_path'          => $sNamespace . '\\' . $sClassName,
            'path'                => $sPath,
            'filename'            => $sFilename,
            'table'               => $sTableName,
            'table_with_prefix'   => $sTableNameWithPrefix,
            'service_name'        => $sServiceName,
            'resource_namespace'  => $sResourceNamespace,
            'resource_class_name' => $sResourceClassName,
            'resource_class_path' => $sResourceNamespace . '\\' . $sResourceClassName,
            'resource_path'       => $sResourcePath,
            'resource_filename'   => $sResourceFilename,
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether a model exists already
     *
     * @param \stdClass $oModel The model definition
     *
     * @return bool
     */
    private function modelExists($oModel): bool
    {
        return file_exists($oModel->path . $oModel->filename);
    }
}
