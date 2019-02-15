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
    const RESOURCE_PATH     = NAILS_COMMON_PATH . 'resources/console/';
    const APP_PATH          = NAILS_APP_PATH . 'src/Service/';
    const SERVICE_PATH      = NAILS_APP_PATH . 'application/services/services.php';
    const SERVICE_TEMP_PATH = CACHE_PATH . 'services.temp.php';
    const TAB_WIDTH         = 4;

    // --------------------------------------------------------------------------

    private $fServicesHandle;
    private $iServicesTokenLocation;
    private $iServicesIndent;

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

        // --------------------------------------------------------------------------

        //  Detect the services file
        if (!file_exists(static::SERVICE_PATH)) {
            return $this->abort(
                self::EXIT_CODE_FAILURE,
                [
                    'Could not detect the app\'s services.php file',
                    static::SERVICE_PATH,
                ]
            );
        }

        //  Look for the generator token
        $this->fServicesHandle = fopen(static::SERVICE_PATH, "r+");;
        $bFound = false;
        if ($this->fServicesHandle) {
            $iLocation = 0;
            while (($sLine = fgets($this->fServicesHandle)) !== false) {
                if (preg_match('#^(\s*)// GENERATOR\[SERVICES\]#', $sLine, $aMatches)) {
                    $bFound                       = true;
                    $this->iServicesIndent        = strlen($aMatches[1]);
                    $this->iServicesTokenLocation = $iLocation;
                    break;
                }
                $iLocation = ftell($this->fServicesHandle);
            }
            if (!$bFound) {
                fclose($this->fServicesHandle);

                return $this->abort(
                    self::EXIT_CODE_FAILURE,
                    [
                        'Services file does not contain the generator token (i.e // GENERATOR[SERVICES])',
                        'This token is required so that the tool can safely insert new service definitions',
                    ]
                );
            }
        } else {
            fclose($this->fServicesHandle);

            return $this->abort(
                self::EXIT_CODE_FAILURE,
                [
                    'Failed to open the services file for reading and writing.',
                    static::SERVICE_PATH,
                ]
            );
        }

        // --------------------------------------------------------------------------

        try {
            //  Ensure the paths exist
            $this->createPath(self::APP_PATH);
            //  Create the service
            $this->createService();
        } catch (ConsoleException $e) {
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
     * Create the Service
     *
     * @throws ConsoleException
     * @return void
     */
    private function createService(): void
    {
        $aFields  = $this->getArguments();
        $aCreated = [];

        try {

            $aToCreate = [];
            $aServices = array_filter(
                array_map(function ($sService) {
                    return implode('/', array_map('ucfirst', explode('/', ucfirst(trim($sService)))));
                }, explode(',', $aFields['SERVICE_NAME']))
            );

            sort($aServices);

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

            $this->oOutput->writeln('The following services will be created:');
            foreach ($aToCreate as $aConfig) {
                $this->oOutput->writeln('');
                $this->oOutput->writeln('Class: <info>' . $aConfig['CLASS_NAME_FULL'] . '</info>');
                $this->oOutput->writeln('Key:   <info>' . $aConfig['CLASS_NAME_NORMALISED'] . '</info>');
                $this->oOutput->writeln('Path:  <info>' . $aConfig['FILE_PATH'] . '</info>');
            }
            $this->oOutput->writeln('');

            if ($this->confirm('Continue?', true)) {

                //  Generate Services
                $aServiceDefinitions = [];
                foreach ($aToCreate as $aConfig) {
                    $this->oOutput->writeln('');
                    $this->oOutput->write('Creating service <comment>' . $aConfig['CLASS_NAME_FULL'] . '</comment>... ');
                    $this->createPath($aConfig['DIRECTORY']);
                    $this->createFile(
                        $aConfig['FILE_PATH'],
                        $this->getResource('template/service.php', $aConfig)
                    );
                    $aCreated[] = $aConfig['FILE_PATH'];
                    $this->oOutput->writeln('<info>done!</info>');

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
                //  Create a temporary file
                $fTempHandle = fopen(static::SERVICE_TEMP_PATH, 'w+');
                rewind($this->fServicesHandle);
                $iLocation = 0;
                while (($sLine = fgets($this->fServicesHandle)) !== false) {
                    if ($iLocation === $this->iServicesTokenLocation) {
                        fwrite(
                            $fTempHandle,
                            implode("\n", $aServiceDefinitions) . "\n"
                        );
                    }
                    fwrite($fTempHandle, $sLine);
                    $iLocation = ftell($this->fServicesHandle);
                }

                //  @todo (Pablo - 2019-02-11) - Sort the services by name

                //  Move the temp services file into place
                unlink(static::SERVICE_PATH);
                rename(static::SERVICE_TEMP_PATH, static::SERVICE_PATH);
                fclose($fTempHandle);
                fclose($this->fServicesHandle);

                $this->oOutput->writeln('<info>done!</info>');
            }

        } catch (ConsoleException $e) {
            $this->oOutput->writeln('<error>failed!</error>');
            //  Clean up created services
            if (!empty($aCreated)) {
                $this->oOutput->writeln('<error>Cleaning up - removing newly created files</error>');
                foreach ($aCreated as $sPath) {
                    @unlink($sPath);
                }
            }
            throw new ConsoleException($e->getMessage());
        }
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
     * Generate the class file apth
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

    // --------------------------------------------------------------------------

    /**
     * Generates N number of tabs
     *
     * @param int $iNumberTabs The number of tabs to generate
     *
     * @return string
     */
    protected function tabs($iNumberTabs = 0)
    {
        return str_repeat(' ', static::TAB_WIDTH * $iNumberTabs);
    }
}
