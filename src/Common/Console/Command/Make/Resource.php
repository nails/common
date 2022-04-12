<?php

namespace Nails\Common\Console\Command\Make;

use Nails\Console\Command\BaseMaker;
use Nails\Console\Exception\ConsoleException;
use Nails\Factory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Resource
 *
 * @package Nails\Common\Console\Command\Make
 */
class Resource extends BaseMaker
{
    const SERVICE_TOKEN = 'RESOURCES';
    const RESOURCE_PATH = NAILS_COMMON_PATH . 'resources/console/';
    const APP_PATH      = NAILS_APP_PATH . 'src/Resource/';

    // --------------------------------------------------------------------------

    /**
     * Configure the command
     */
    protected function configure(): void
    {
        $this
            ->setName('make:resource')
            ->setDescription('Creates a new App resource')
            ->addArgument(
                'resourceName',
                InputArgument::OPTIONAL,
                'Define the name of the resource to create'
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

        try {

            $this
                ->validateServiceFile()
                ->createPath(self::APP_PATH)
                ->createResource();

        } catch (\Throwable $e) {
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

    /**
     * Create the Resource
     *
     * @return$this
     * @throws ConsoleException
     */
    private function createResource(): self
    {
        $aFields  = $this->getArguments();
        $aCreated = [];

        try {

            $aToCreate  = [];
            $aResources = $this->parseClassNames($aFields['RESOURCE_NAME']);

            foreach ($aResources as $sResource) {

                $aResourceBits = explode('/', $sResource);
                $aResourceBits = array_map('ucfirst', $aResourceBits);

                $sNamespace       = $this->generateNamespace($aResourceBits);
                $sClassName       = $this->generateClassName($aResourceBits);
                $sClassNameFull   = $sNamespace . '\\' . $sClassName;
                $sClassNameNormal = str_replace('AppResource', '', str_replace('\\', '', $sClassNameFull));
                $sFilePath        = $this->generateFilePath($aResourceBits);

                //  Test it does not already exist
                if (file_exists($sFilePath)) {
                    throw new ConsoleException(
                        'A resource at "' . $sFilePath . '" already exists'
                    );
                }
                try {
                    $oTest = Factory::resource($sClassNameNormal, 'app');
                    throw new ConsoleException(
                        'A resource by "' . $sClassNameNormal . '" is already defined'
                    );
                } catch (\Exception $e) {
                    //  No exception? No problem!
                }

                $aToCreate[] = [
                    'NAMESPACE'             => $sNamespace,
                    'CLASS_NAME'            => $sClassName,
                    'CLASS_NAME_FULL'       => $sClassNameFull,
                    'CLASS_NAME_NORMALISED' => $sClassNameNormal,
                    'FILE_PATH'             => $sFilePath,
                    'DIRECTORY'             => dirname($sFilePath) . DIRECTORY_SEPARATOR,
                ];
            }

            $this->oOutput->writeln('The following resource(s) will be created:');
            foreach ($aToCreate as $aConfig) {
                $this->oOutput->writeln('');
                $this->oOutput->writeln('Class: <info>' . $aConfig['CLASS_NAME_FULL'] . '</info>');
                $this->oOutput->writeln('Key:   <info>' . $aConfig['CLASS_NAME_NORMALISED'] . '</info>');
                $this->oOutput->writeln('Path:  <info>' . $aConfig['FILE_PATH'] . '</info>');
            }
            $this->oOutput->writeln('');

            if ($this->confirm('Continue?', true)) {

                $this->oOutput->writeln('');

                //  Generate Resources
                $aResourceDefinitions = [];
                foreach ($aToCreate as $aConfig) {
                    $this->oOutput->write('Creating resource <comment>' . $aConfig['CLASS_NAME_FULL'] . '</comment>... ');
                    $this->createPath($aConfig['DIRECTORY']);
                    $this->createFile(
                        $aConfig['FILE_PATH'],
                        $this->getResource('template/resource.php', $aConfig)
                    );
                    $aCreated[] = $aConfig['FILE_PATH'];
                    $this->oOutput->writeln('<info>done</info>');

                    //  Generate the resource definition
                    $aDefinition            = [
                        str_repeat(' ', $this->iServicesIndent) . '\'' . $aConfig['CLASS_NAME_NORMALISED'] . '\' => function ($oObj) {',
                        str_repeat(' ', $this->iServicesIndent) . '    return new ' . $aConfig['CLASS_NAME_FULL'] . '($oObj);',
                        str_repeat(' ', $this->iServicesIndent) . '},',
                    ];
                    $aResourceDefinitions[] = implode("\n", $aDefinition);
                }

                //  Add resources to the app's resources array
                $this->oOutput->writeln('');
                $this->oOutput->write('Adding resource(s) to app services... ');
                $this->writeServiceFile($aResourceDefinitions);
                $this->oOutput->writeln('<info>done</info>');
            }

        } catch (ConsoleException $e) {
            $this->oOutput->writeln('<error>fail</error>');
            //  Clean up created resources
            if (!empty($aCreated)) {
                $this->oOutput->writeln('<error>Cleaning up - removing newly created files</error>');
                foreach ($aCreated as $sPath) {
                    @unlink($sPath);
                }
            }
            throw new ConsoleException($e->getMessage());
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Generate the class namespace
     *
     * @param array $aResourceBits The supplied classname "bits"
     *
     * @return string
     */
    protected function generateNamespace(array $aResourceBits): string
    {
        array_pop($aResourceBits);
        return implode('\\', array_merge(['App', 'Resource'], $aResourceBits));
    }

    // --------------------------------------------------------------------------

    /**
     * Generate the class name
     *
     * @param array $aResourceBits The supplied classname "bits"
     *
     * @return string
     */
    protected function generateClassName(array $aResourceBits): string
    {
        return array_pop($aResourceBits);
    }

    // --------------------------------------------------------------------------

    /**
     * Generate the class file path
     *
     * @param array $aResourceBits The supplied classname "bits"
     *
     * @return string
     */
    protected function generateFilePath(array $aResourceBits): string
    {
        $sClassName = array_pop($aResourceBits);
        return implode(
            DIRECTORY_SEPARATOR,
            array_map(
                function ($sItem) {
                    return rtrim($sItem, DIRECTORY_SEPARATOR);
                },
                array_merge(
                    [static::APP_PATH],
                    $aResourceBits,
                    [$sClassName . '.php']
                )
            )
        );
    }
}
