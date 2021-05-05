<?php

namespace Nails\Common\Console\Command\Make;

use Nails\Console\Command\BaseMaker;
use Nails\Console\Exception\ConsoleException;
use Nails\Factory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Service extends BaseMaker
{
    const SERVICE_TOKEN = 'SERVICES';
    const RESOURCE_PATH = NAILS_COMMON_PATH . 'resources/console/';
    const APP_PATH      = NAILS_APP_PATH . 'src/Service/';

    // --------------------------------------------------------------------------

    /**
     * Configure the command
     */
    protected function configure(): void
    {
        $this
            ->setName('make:service')
            ->setDescription('Creates a new App service')
            ->addArgument(
                'serviceName',
                InputArgument::OPTIONAL,
                'Define the name of the service to create'
            );
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

        try {
            $this
                ->validateServiceFile()
                ->createPath(self::APP_PATH)
                ->createService();
        } catch (ConsoleException $e) {
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
     * Create the Service
     *
     * @throws ConsoleException
     * @return$this
     */
    private function createService(): self
    {
        $aFields  = $this->getArguments();
        $aCreated = [];

        try {

            $aToCreate = [];
            $aServices = $this->parseClassNames($aFields['SERVICE_NAME']);

            foreach ($aServices as $sService) {

                $aServiceBits = explode('/', $sService);
                $aServiceBits = array_map('ucfirst', $aServiceBits);

                $sNamespace       = $this->generateNamespace($aServiceBits);
                $sClassName       = $this->generateClassName($aServiceBits);
                $sClassNameFull   = $sNamespace . '\\' . $sClassName;
                $sClassNameNormal = str_replace('AppService', '', str_replace('\\', '', $sClassNameFull));
                $sFilePath        = $this->generateFilePath($aServiceBits);

                //  Test it does not already exist
                if (file_exists($sFilePath)) {
                    throw new ConsoleException(
                        'A service at "' . $sFilePath . '" already exists'
                    );
                }
                try {
                    $oTest = Factory::service($sClassNameNormal, 'app');
                    throw new ConsoleException(
                        'A service by "' . $sClassNameNormal . '" is already defined'
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

            $this->oOutput->writeln('The following service(s) will be created:');
            foreach ($aToCreate as $aConfig) {
                $this->oOutput->writeln('');
                $this->oOutput->writeln('Class: <info>' . $aConfig['CLASS_NAME_FULL'] . '</info>');
                $this->oOutput->writeln('Key:   <info>' . $aConfig['CLASS_NAME_NORMALISED'] . '</info>');
                $this->oOutput->writeln('Path:  <info>' . $aConfig['FILE_PATH'] . '</info>');
            }
            $this->oOutput->writeln('');

            if ($this->confirm('Continue?', true)) {

                $this->oOutput->writeln('');

                //  Generate Services
                $aServiceDefinitions = [];
                foreach ($aToCreate as $aConfig) {
                    $this->oOutput->write('Creating service <comment>' . $aConfig['CLASS_NAME_FULL'] . '</comment>... ');
                    $this->createPath($aConfig['DIRECTORY']);
                    $this->createFile(
                        $aConfig['FILE_PATH'],
                        $this->getResource('template/service.php', $aConfig)
                    );
                    $aCreated[] = $aConfig['FILE_PATH'];
                    $this->oOutput->writeln('<info>done</info>');

                    //  Generate the service definition
                    $aDefinition           = [
                        str_repeat(' ', $this->iServicesIndent) . '\'' . $aConfig['CLASS_NAME_NORMALISED'] . '\' => function () {',
                        str_repeat(' ', $this->iServicesIndent) . '    return new ' . $aConfig['CLASS_NAME_FULL'] . '();',
                        str_repeat(' ', $this->iServicesIndent) . '},',
                    ];
                    $aServiceDefinitions[] = implode("\n", $aDefinition);
                }

                //  Add services to the app's services array
                $this->oOutput->writeln('');
                $this->oOutput->write('Adding service(s) to app services... ');
                $this->writeServiceFile($aServiceDefinitions);
                $this->oOutput->writeln('<info>done</info>');
            }

        } catch (ConsoleException $e) {
            $this->oOutput->writeln('<error>fail</error>');
            //  Clean up created services
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
     * Generate the class name
     *
     * @param array $aServiceBits The supplied classname "bits"
     *
     * @return string
     */
    protected function generateClassName(array $aServiceBits): string
    {
        return array_pop($aServiceBits);
    }

    // --------------------------------------------------------------------------


    /**
     * Generate the class namespace
     *
     * @param array $aServiceBits The supplied classname "bits"
     *
     * @return string
     */
    protected function generateNamespace(array $aServiceBits): string
    {
        array_pop($aServiceBits);
        return implode('\\', array_merge(['App', 'Service'], $aServiceBits));
    }

    // --------------------------------------------------------------------------

    /**
     * Generate the class file path
     *
     * @param array $aServiceBits The supplied classname "bits"
     *
     * @return string
     */
    protected function generateFilePath(array $aServiceBits): string
    {
        $sClassName = array_pop($aServiceBits);
        return implode(
            DIRECTORY_SEPARATOR,
            array_map(
                function ($sItem) {
                    return rtrim($sItem, DIRECTORY_SEPARATOR);
                },
                array_merge(
                    [static::APP_PATH],
                    $aServiceBits,
                    [$sClassName . '.php']
                )
            )
        );
    }
}
