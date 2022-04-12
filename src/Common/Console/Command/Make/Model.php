<?php

namespace Nails\Common\Console\Command\Make;

use Exception;
use Nails\Common\Exception\Database\ConnectionException;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\NailsException;
use Nails\Common\Service\Database;
use Nails\Common\Service\Locale;
use Nails\Common\Service\PDODatabase;
use Nails\Common\Traits\Model\Localised;
use Nails\Components;
use Nails\Config;
use Nails\Console\Command\BaseMaker;
use Nails\Console\Exception\Path;
use Nails\Factory;
use stdClass;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Model
 *
 * @package Nails\Common\Console\Command\Make
 */
class Model extends BaseMaker
{
    const SERVICE_TOKEN     = 'MODELS';
    const MODEL_PATH        = NAILS_APP_PATH . 'src/Model/';
    const RESOURCE_PATH     = NAILS_COMMON_PATH . 'resources/console/';
    const RESOURCE_APP_PATH = Resource::APP_PATH;

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
                InputOption::VALUE_NONE,
                'Skip database table creation'
            )
            ->addOption(
                'skip-seeder',
                null,
                InputOption::VALUE_NONE,
                'Skip seeder creation'
            )
            ->addOption(
                'auto-detect',
                null,
                InputOption::VALUE_NONE,
                'Automatically build models from the database'
            )
            ->addOption(
                'localised',
                null,
                InputOption::VALUE_NONE,
                'Create a localised model (or convert an existing model)'
            );
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the app
     *
     * @param InputInterface  $oInput  The Input Interface provided by Symfony
     * @param OutputInterface $oOutput The Output Interface provided by Symfony
     *
     * @return int
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput): int
    {
        parent::execute($oInput, $oOutput);

        // --------------------------------------------------------------------------

        $bSkipDb = $oInput->getOption('skip-db');

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
            } catch (Exception $e) {
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
                ->validateServiceFile()
                ->createPath(self::MODEL_PATH)
                ->createModel();

        } catch (Exception $e) {
            return $this->abort(
                self::EXIT_CODE_FAILURE,
                [$e->getMessage()]
            );
        }

        // --------------------------------------------------------------------------

        //  Cleaning up
        $oOutput->writeln('');
        $oOutput->writeln('<comment>Cleaning up</comment>...');

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
     * @return $this
     * @throws NailsException
     */
    private function createModel(): self
    {
        $oInput      = $this->oInput;
        $oOutput     = $this->oOutput;
        $bSkipDb     = $oInput->getOption('skip-db');
        $bSkipSeeder = $oInput->getOption('skip-seeder');
        $bAutoDetect = $oInput->getOption('auto-detect');
        $bLocalised  = $oInput->getOption('localised');

        //  If auto-detecting models using the database then skip cresting database tables
        if ($bAutoDetect) {
            $bSkipDb = true;
        }

        try {

            if (!$bAutoDetect) {

                $aFields    = $this->getArguments();
                $aModelData = [];
                $aModels    = $this->parseClassNames($aFields['MODEL_NAME']);

                foreach ($aModels as $sModelName) {
                    $oModel = $this->prepareModelName($sModelName);

                    /**
                     * Test whether the model exists already
                     *
                     * Alter our behaviour slightly depending if we're localising or not. An existing model, which
                     * is not-localised is OK as we'll convert it to be localised (assuming the --localised flag
                     * is passed).
                     */

                    if ($this->modelExists($oModel)) {
                        if ($bLocalised && classUses($oModel->class_path, Localised::class)) {
                            throw new NailsException(
                                'A localised model by that path already exists "' . $oModel->path . $oModel->filename . '"'
                            );
                        } elseif (!$bLocalised) {
                            throw new NailsException(
                                'A model by that path already exists "' . $oModel->path . $oModel->filename . '"'
                            );
                        }
                    }

                    if (!$bSkipDb && $this->tableExists($oModel)) {
                        if ($bLocalised && classUses($oModel->class_path, Localised::class)) {
                            throw new NailsException(
                                'Localised table "' . $oModel->table_with_prefix . '" already exists. Use option --skip-db to skip database check.'
                            );
                        } elseif (!$bLocalised) {
                            throw new NailsException(
                                'Table "' . $oModel->table_with_prefix . '" already exists. Use option --skip-db to skip database check.'
                            );
                        }
                    }

                    $aModelData[] = $oModel;
                }

            } else {

                /** @var PDODatabase $oDb */
                $oDb        = Factory::service('PDODatabase');
                $sAppPrefix = Config::get('APP_DB_PREFIX');
                $oTables    = $oDb->query('SHOW TABLES LIKE "' . $sAppPrefix . '%";');
                $aTables    = $oTables->fetchAll(\PDO::FETCH_NUM);

                //  If APP_DB_PREFIX is empty then the query above will have all the Nails table's too,
                //  filter them out.
                if (empty($sAppPrefix)) {
                    $aTables = array_filter(
                        $aTables,
                        function ($aTable) {
                            return !preg_match('/^' . Config::get('NAILS_DB_PREFIX') . '/', $aTable[0]);
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
                if ($bLocalised && $this->modelExists($oModel)) {
                    $oOutput->writeln('Localised:      <info>Model will be converted</info>');
                } else {
                    if (!$bSkipDb) {
                        $oOutput->writeln('Table:          <info>' . $oModel->table_with_prefix . '</info>');
                    }
                    if (!$bSkipSeeder) {
                        $oOutput->writeln('Seeder:         <info>Seeder will be created</info>');
                    }
                }
                $oOutput->writeln('');
            }

            if ($this->confirm('Continue?', true)) {

                $aServiceModelDefinitions = [];

                foreach ($aModelData as $oModel) {
                    $oOutput->writeln('');
                    if ($bLocalised && $this->modelExists($oModel)) {
                        $this->convertExistingModelToLocalised(
                            $oModel,
                            $bSkipDb,
                            $bSkipSeeder
                        );
                    } elseif ($bLocalised && !$this->modelExists($oModel)) {
                        $sServiceDefinition = $this->createLocalisedModel(
                            $oModel,
                            $bSkipDb,
                            $bSkipSeeder
                        );
                    } else {
                        $sServiceDefinition = $this->createNormalModel(
                            $oModel,
                            $bSkipDb,
                            $bSkipSeeder
                        );
                    }

                    if (!empty($sServiceDefinition)) {
                        $aServiceModelDefinitions[] = $sServiceDefinition;
                    }
                }

                // --------------------------------------------------------------------------

                //  Add models to the app's services array
                if (!empty($aServiceModelDefinitions)) {
                    $this->oOutput->writeln('');
                    $this->oOutput->write('Adding model(s) to app services...');
                    $this->writeServiceFile($aServiceModelDefinitions);
                    $this->oOutput->writeln('<info>done</info>');
                }
            }

        } catch (Exception $e) {
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
     * @return stdClass
     * @throws Exception
     */
    private function prepareModelName($sModelName): stdClass
    {
        //  Prepare the supplied model name and test
        $sModelName = preg_replace('/[^a-zA-Z\/\\\ ]/', '', $sModelName);
        $sModelName = preg_replace('/(\/|\\\\)/', ' \\ ', $sModelName);
        $sModelName = ucwords($sModelName);

        //  Prepare the database table name
        $sTableName           = preg_replace('/[^a-z ]/', '', strtolower($sModelName));
        $sTableName           = preg_replace('/ +/', '_', $sTableName);
        $sTableNameWithPrefix = Config::get('APP_DB_PREFIX') . $sTableName;

        //  Prepare the model name, namespace, filename etc
        $sModelName = 'App\Model\\' . preg_replace('/ /', '', $sModelName);
        $aModelName = explode('\\', $sModelName);
        $aNamespace = array_splice($aModelName, 0, -1);
        $sClassName = implode('', $aModelName);
        $sFilename  = $sClassName . '.php';
        $sPath      = static::MODEL_PATH . implode(DIRECTORY_SEPARATOR, array_slice($aNamespace, 2));
        $sPath      = substr($sPath, -1) !== DIRECTORY_SEPARATOR ? $sPath . DIRECTORY_SEPARATOR : $sPath;
        $sNamespace = implode('\\', $aNamespace);

        //  Prepare the resource name, namespace, filename etc
        $sResourceName      = str_replace('App\Model\\', 'App\Resource\\', $sModelName);
        $sResourceNameCmd   = str_replace('App\Model\\', '', $sModelName);
        $aResourceName      = explode('\\', $sResourceName);
        $aResourceNamespace = array_splice($aResourceName, 0, -1);
        $sResourceClassName = implode('', $aResourceName);
        $sResourceFilename  = $sResourceClassName . '.php';
        $sResourcePath      = static::RESOURCE_APP_PATH . implode(DIRECTORY_SEPARATOR, array_slice($aResourceNamespace, 2));
        $sResourcePath      = substr($sResourcePath, -1) !== DIRECTORY_SEPARATOR ? $sResourcePath . DIRECTORY_SEPARATOR : $sResourcePath;
        $sResourceNamespace = implode('\\', $aResourceNamespace);

        //  Set the service name
        $sServiceName = str_replace('App\Model\\', '', $sModelName);
        $sServiceName = str_replace('\\', '', $sServiceName);

        return (object) [
            'namespace'           => $sNamespace,
            'class_name'          => $sClassName,
            'class_path'          => $sNamespace . '\\' . $sClassName,
            'path'                => $sPath,
            'filename'            => $sFilename,
            'table'               => $sTableName,
            'table_with_prefix'   => $sTableNameWithPrefix,
            'resource_name'       => $sResourceNameCmd,
            'resource_class_path' => $sResourceNamespace . '\\' . $sClassName,
            'resource_path'       => $sResourcePath,
            'resource_filename'   => $sResourceFilename,
            'service_name'        => $sServiceName,
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether a model exists already
     *
     * @param stdClass $oModel The model definition
     *
     * @return bool
     */
    private function modelExists(stdClass $oModel): bool
    {
        return file_exists($oModel->path . $oModel->filename);
    }

    // --------------------------------------------------------------------------

    /**
     * Determine whether a particular table exists already
     *
     * @param stdClass $oModel The model definition
     *
     * @return bool
     * @throws FactoryException
     */
    private function tableExists(stdClass $oModel): bool
    {
        /** @var PDODatabase $oDb */
        $oDb     = Factory::service('PDODatabase');
        $oResult = $oDb->query('SHOW TABLES LIKE "' . $oModel->table_with_prefix . '"');
        return $oResult->rowCount() > 0;
    }

    // --------------------------------------------------------------------------

    /**
     * Orchestrates the conversion of a normal model to a localised model
     *
     * @param stdClass $oModel      The model being converted
     * @param bool     $bSkipDb     Whether to skip table creation
     * @param bool     $bSkipSeeder Whether to skip seeder creation
     *
     * @throws FactoryException
     */
    private function convertExistingModelToLocalised(
        stdClass $oModel,
        bool $bSkipDb,
        bool $bSkipSeeder
    ): void {
        $this
            ->addLocalisedUseStatement($oModel)
            ->convertTablesToLocalised($oModel);
    }

    // --------------------------------------------------------------------------

    /**
     * Orchestrates the creation of a localised model
     *
     * @param stdClass $oModel      The model being created
     * @param bool     $bSkipDb     Whether to skip table creation
     * @param bool     $bSkipSeeder Whether to skip seeder creation
     *
     * @return string
     * @throws FactoryException
     * @throws NailsException
     * @throws Path\DoesNotExistException
     * @throws Path\IsNotWritableException
     * @throws Exception
     */
    private function createLocalisedModel(
        stdClass $oModel,
        bool $bSkipDb,
        bool $bSkipSeeder
    ): string {
        $this
            ->createModelFile($oModel, 'model_localised')
            ->createResource($oModel);

        if (!$bSkipDb) {
            $this->createDatabaseTable($oModel, 'model_table_localised');
        }

        if (!$bSkipSeeder) {
            $this->createSeeder($oModel);
        }

        return $this->generateServiceDefinition($oModel);
    }

    // --------------------------------------------------------------------------

    /**
     * Orchestrates the creation of a  model
     *
     * @param stdClass $oModel      The model being created
     * @param bool     $bSkipDb     Whether to skip table creation
     * @param bool     $bSkipSeeder Whether to skip seeder creation
     *
     * @return string
     * @throws FactoryException
     * @throws NailsException
     * @throws Path\DoesNotExistException
     * @throws Path\IsNotWritableException
     * @throws Exception
     */
    private function createNormalModel(
        stdClass $oModel,
        bool $bSkipDb,
        bool $bSkipSeeder
    ): string {

        $this
            ->createModelFile($oModel, 'model')
            ->createResource($oModel);

        if (!$bSkipDb) {
            $this->createDatabaseTable($oModel, 'model_table');
        }

        if (!$bSkipSeeder) {
            $this->createSeeder($oModel);
        }

        return $this->generateServiceDefinition($oModel);
    }

    // --------------------------------------------------------------------------

    /**
     * Creates the model file
     *
     * @param stdClass $oModel    The model being created
     * @param string   $sTemplate The template to use
     *
     * @return $this
     * @throws NailsException
     * @throws Path\DoesNotExistException
     * @throws Path\IsNotWritableException
     */
    private function createModelFile(stdClass $oModel, string $sTemplate): self
    {
        $this->oOutput->write('Creating model <comment>' . $oModel->class_path . '</comment>... ');
        $this->createPath($oModel->path);
        $this->createFile(
            $oModel->path . $oModel->filename,
            $this->getResource('template/' . $sTemplate . '.php', (array) $oModel)
        );
        $this->oOutput->writeln('<info>done</info>');

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Creates the resource file
     *
     * @param stdClass $oModel The model being created
     *
     * @return $this
     * @throws NailsException
     * @throws Path\DoesNotExistException
     * @throws Path\IsNotWritableException
     */
    private function createResource(stdClass $oModel): self
    {
        $this->oOutput->write('Creating resource... ');
        //  Execute the create command, non-interactively and silently
        $iExitCode = $this->callCommand(
            'make:resource',
            [
                'resourceName' => $oModel->resource_name,
            ],
            false,
            true
        );
        if ($iExitCode === static::EXIT_CODE_FAILURE) {
            $this->oOutput->writeln('<error>fail</error>');
        } else {
            $this->oOutput->writeln('<info>done</info>');
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Creates the database table
     *
     * @param stdClass $oModel    The model being created
     * @param string   $sTemplate The template to use
     *
     * @return $this
     * @throws NailsException
     * @throws FactoryException
     */
    private function createDatabaseTable(stdClass $oModel, string $sTemplate): self
    {
        /** @var PDODatabase $oDb */
        $oDb = Factory::service('PDODatabase');

        $this->oOutput->write('Adding database table... ');
        $oModel->nails_db_prefix = Config::get('NAILS_DB_PREFIX');
        $oDb->query($this->getResource('template/' . $sTemplate . '.php', (array) $oModel));
        $this->oOutput->writeln('<info>done</info>');

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Creates a seeder
     *
     * @param stdClass $oModel The model being created
     *
     * @return $this
     * @throws Exception
     */
    private function createSeeder(stdClass $oModel)
    {
        $this->oOutput->write('Creating seeder... ');
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
            $this->oOutput->writeln('<error>fail</error>');
        } else {
            $this->oOutput->writeln('<info>done</info>');
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Generates the service file definitions for a model
     *
     * @param stdClass $oModel The model being created
     *
     * @return string
     */
    private function generateServiceDefinition(stdClass $oModel): string
    {
        return implode(PHP_EOL, [
            str_repeat(' ', $this->iServicesIndent) . '\'' . $oModel->service_name . '\' => function () {',
            str_repeat(' ', $this->iServicesIndent) . '    return new \\' . $oModel->class_path . '();',
            str_repeat(' ', $this->iServicesIndent) . '},',
        ]);
    }

    // --------------------------------------------------------------------------

    /**
     * Adds a `use Localised` statement to a model
     *
     * @param stdClass $oModel The model being converted
     *
     * @return $this
     */
    private function addLocalisedUseStatement(stdClass $oModel): self
    {
        $sFile = file_get_contents($oModel->path . $oModel->filename);

        // --------------------------------------------------------------------------

        //  Add the imports
        $aClasses = [];
        if (preg_match_all('/^use (.+);$/m', $sFile, $aMatches)) {
            if (!empty($aMatches[1])) {
                $aClasses = $aMatches[1];
            }
        }

        $aClasses[] = Localised::class;
        $aClasses   = array_unique($aClasses);
        $aClasses   = array_filter($aClasses);
        sort($aClasses);
        $aClasses    = array_values($aClasses);
        $sStatements = implode(PHP_EOL, array_map(function ($sClass) {
            return 'use ' . $sClass . ';';
        }, $aClasses));

        //  Write the imports after `namespace`
        $sFile = preg_replace('/^use.+?;(\n\n|\nclass)/sm', '', $sFile);
        $sFile = preg_replace('/^(namespace .+)$\n/m', "$1\n\n" . $sStatements . PHP_EOL, $sFile);

        // --------------------------------------------------------------------------

        $aClasses = [];
        if (preg_match_all('/^    use (.+);$/m', $sFile, $aMatches)) {
            if (!empty($aMatches[1])) {
                $aClasses = $aMatches[1];
            }
        }

        $aTraitBits = explode('\\', Localised::class);
        $aClasses[] = end($aTraitBits);
        $aClasses   = array_unique($aClasses);
        $aClasses   = array_filter($aClasses);
        sort($aClasses);
        $aClasses    = array_values($aClasses);
        $sStatements = implode(PHP_EOL, array_map(function ($sClass) {
            return '    use ' . $sClass . ';';
        }, $aClasses));

        //  Write the statements after the class definition
        $sFile = preg_replace('/^    use.+?;(\n\n|\nclass)/sm', '', $sFile);
        $sFile = preg_replace('/^(class .+)$\n^{$/m', "$1\n{\n" . $sStatements . PHP_EOL, $sFile);

        // --------------------------------------------------------------------------

        file_put_contents(
            $oModel->path . $oModel->filename,
            $sFile
        );

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Converts existing non-localised model tables to a localised version
     *
     * @param stdClass $oModel The model being converted
     *
     * @return $this
     * @throws FactoryException
     */
    private function convertTablesToLocalised(stdClass $oModel): self
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');
        /** @var Locale $locale */
        $oLocale = Factory::service('Locale');

        $aQueries = [
            //  Rename the existing table (and data)
            'RENAME TABLE `' . $oModel->table_with_prefix . '` TO `' . $oModel->table_with_prefix . '_localised`;',

            //  Drop the AUTO_INCREMENT key
            'ALTER TABLE `' . $oModel->table_with_prefix . '_localised` CHANGE `id` `id` INT(11) UNSIGNED NOT NULL;',

            //  Change the primary key
            'ALTER TABLE  `' . $oModel->table_with_prefix . '_localised` DROP PRIMARY KEY, ADD PRIMARY KEY (`id`, `language`, `region`)',

            //  Create the new top-level table
            'CREATE TABLE `' . $oModel->table_with_prefix . '` (id INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT) DEFAULT CHARACTER SET `utf8`;',

            //  Add a foreign key to the localised table
            'ALTER TABLE `' . $oModel->table_with_prefix . '_localised` ADD FOREIGN KEY (`id`) REFERENCES `' . $oModel->table_with_prefix . '` (`id`) ON DELETE CASCADE;',

            //  Add the locale columns
            'ALTER TABLE `' . $oModel->table_with_prefix . '_localised` ADD `language` VARCHAR(5) NULL DEFAULT NULL AFTER `id`;',
            'ALTER TABLE `' . $oModel->table_with_prefix . '_localised` ADD `region` VARCHAR(5) NULL DEFAULT NULL AFTER `language`;',

            //  Populate the top-level table
            'INSERT INTO ' . $oModel->table_with_prefix . ' SELECT id FROM ' . $oModel->table_with_prefix . '_localised;',

            //  Update the columns with the default language
            'UPDATE `' . $oModel->table_with_prefix . '_localised` SET `language` = "' . $oLocale->get()->getLanguage() . '"',
            'UPDATE `' . $oModel->table_with_prefix . '_localised` SET `region` = "' . $oLocale->get()->getRegion() . '"',
        ];

        //  Disable foreign key checks so we can shift the tables about
        $oForeignKeyChecks = $oDb->foreignKeyCheck();
        $oForeignKeyChecks->off();

        array_walk($aQueries, function ($sQuery) use ($oDb) {
            $oDb->query($sQuery);
        });

        $oForeignKeyChecks->on();

        $this->warning(array_merge(['Remember to add migrations!', ''], $aQueries));

        return $this;
    }
}
