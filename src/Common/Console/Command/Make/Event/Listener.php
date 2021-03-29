<?php

namespace Nails\Common\Console\Command\Make\Event;

use Nails\Console\Command\BaseMaker;
use Nails\Console\Exception\ConsoleException;
use Nails\Factory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Listener extends BaseMaker
{
    const SERVICE_TOKEN = 'SERVICES';
    const RESOURCE_PATH = NAILS_COMMON_PATH . 'resources/console/';
    const APP_PATH      = NAILS_APP_PATH . 'src/Event/Listener/';

    // --------------------------------------------------------------------------

    /**
     * Configure the command
     */
    protected function configure(): void
    {
        $this
            ->setName('make:event:listener')
            ->setDescription('Creates a new Event Listener')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Define the name of the event listener to create'
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

        try {
            $this
                ->createPath(self::APP_PATH)
                ->createEventListener();
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
     * Create the Listener
     *
     * @return$this
     * @throws ConsoleException
     */
    private function createEventListener(): self
    {
        $aFields  = $this->getArguments();
        $aCreated = [];

        try {

            $aToCreate  = [];
            $aListeners = array_filter(
                array_map(function ($sListener) {
                    return implode('/', array_map('ucfirst', explode('/', ucfirst(trim($sListener)))));
                }, explode(',', $aFields['NAME']))
            );

            sort($aListeners);

            foreach ($aListeners as $sListener) {

                $aListenerBits = explode('/', $sListener);
                $aListenerBits = array_map('ucfirst', $aListenerBits);

                $sNamespace     = $this->generateNamespace($aListenerBits);
                $sClassName     = $this->generateClassName($aListenerBits);
                $sClassNameFull = $sNamespace . '\\' . $sClassName;
                $sFilePath      = $this->generateFilePath($aListenerBits);

                //  Test it does not already exist
                if (file_exists($sFilePath)) {
                    throw new ConsoleException(
                        'An event listener at "' . $sFilePath . '" already exists'
                    );
                }

                $aToCreate[] = [
                    'NAMESPACE' => $sNamespace,
                    'CLASS_NAME' => $sClassName,
                    'CLASS_NAME_FULL' => $sClassNameFull,
                    'FILE_PATH' => $sFilePath,
                    'DIRECTORY' => dirname($sFilePath) . DIRECTORY_SEPARATOR,
                ];
            }

            $this->oOutput->writeln('The following event listeners(s) will be created:');
            foreach ($aToCreate as $aConfig) {
                $this->oOutput->writeln('');
                $this->oOutput->writeln('Class: <info>' . $aConfig['CLASS_NAME_FULL'] . '</info>');
                $this->oOutput->writeln('Path:  <info>' . $aConfig['FILE_PATH'] . '</info>');
            }
            $this->oOutput->writeln('');

            if ($this->confirm('Continue?', true)) {
                foreach ($aToCreate as $aConfig) {
                    $this->oOutput->writeln('');
                    $this->oOutput->write('Creating event listener <comment>' . $aConfig['CLASS_NAME_FULL'] . '</comment>... ');
                    $this->createPath($aConfig['DIRECTORY']);
                    $this->createFile(
                        $aConfig['FILE_PATH'],
                        $this->getResource('template/event_listener.php', $aConfig)
                    );
                    $aCreated[] = $aConfig['FILE_PATH'];
                    $this->oOutput->writeln('<info>done</info>');

                    //  @todo (Pablo - 2019-04-30) - Add to src/Events.php if properly configured
                }
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
     * @param array $aListenerBits The supplied classname "bits"
     *
     * @return string
     */
    protected function generateClassName(array $aListenerBits): string
    {
        return array_pop($aListenerBits);
    }

    // --------------------------------------------------------------------------


    /**
     * Generate the class namespace
     *
     * @param array $aListenerBits The supplied classname "bits"
     *
     * @return string
     */
    protected function generateNamespace(array $aListenerBits): string
    {
        array_pop($aListenerBits);
        return implode('\\', array_merge(['App', 'Event', 'Listener'], $aListenerBits));
    }

    // --------------------------------------------------------------------------

    /**
     * Generate the class file path
     *
     * @param array $aListenerBits The supplied classname "bits"
     *
     * @return string
     */
    protected function generateFilePath(array $aListenerBits): string
    {
        $sClassName = array_pop($aListenerBits);
        return implode(
            DIRECTORY_SEPARATOR,
            array_map(
                function ($sItem) {
                    return rtrim($sItem, DIRECTORY_SEPARATOR);
                },
                array_merge(
                    [static::APP_PATH],
                    $aListenerBits,
                    [$sClassName . '.php']
                )
            )
        );
    }
}
