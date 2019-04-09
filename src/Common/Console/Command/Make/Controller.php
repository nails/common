<?php

namespace Nails\Common\Console\Command\Make;

use Nails\Console\Command\BaseMaker;
use Nails\Console\Exception\ConsoleException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Controller extends BaseMaker
{
    const RESOURCE_PATH   = NAILS_COMMON_PATH . 'resources/console/';
    const CONTROLLER_PATH = NAILS_APP_PATH . 'application/modules/';

    // --------------------------------------------------------------------------

    /**
     * Configure the command
     */
    protected function configure(): void
    {
        $this
            ->setName('make:controller')
            ->setDescription('Creates a new App controller')
            ->addArgument(
                'controllerName',
                InputArgument::OPTIONAL,
                'Define the name of the controller to create'
            )
            ->addArgument(
                'methods',
                InputArgument::OPTIONAL,
                'Comma separated list of methods to include',
                'index'
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
            //  Ensure the paths exist
            $this->createPath(self::CONTROLLER_PATH);
            //  Create the controller
            $this->createController();
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
     * Create the Model
     *
     * @return void
     * @throws ConsoleException
     */
    private function createController(): void
    {
        $aFields  = $this->getArguments();
        $aCreated = [];

        try {

            $aControllers = array_filter(
                array_map(function ($sController) {
                    return implode('/', array_map('ucfirst', explode('/', ucfirst(trim($sController)))));
                }, explode(',', $aFields['CONTROLLER_NAME']))
            );
            $aMethods     = array_filter(
                array_map(function ($sMethod) {
                    return lcfirst(trim($sMethod));
                }, explode(',', $aFields['METHODS']))
            );

            sort($aControllers);

            foreach ($aControllers as $sController) {

                $aControllerBits = explode('/', $sController);
                $aControllerBits = array_map('ucfirst', $aControllerBits);

                if (count($aControllerBits) > 2) {
                    throw new ConsoleException('Controllers cannot be deeper than one directory');
                }

                $aFields['MODULE_NAME']     = getFromArray(0, $aControllerBits);
                $aFields['CONTROLLER_NAME'] = getFromArray(1, $aControllerBits, $aFields['MODULE_NAME']);
                $this->oOutput->write('Creating controller <comment>' . $sController . '</comment>... ');


                if (count($aControllerBits) == 2) {
                    //  A path has been specified, splice in the correct folder structure
                    $aLastControllerBits = array_splice($aControllerBits, -1, 1);
                    $aControllerBits     = array_merge(
                        array_map('lcfirst', $aControllerBits),
                        ['controllers'],
                        array_map('ucfirst', $aLastControllerBits)
                    );
                } else {
                    //  The controller should be in a folder called itself
                    $aControllerBits = array_merge(
                        array_map('lcfirst', $aControllerBits),
                        ['controllers'],
                        array_map('ucfirst', $aControllerBits)
                    );
                }

                $aViewBits = $aControllerBits;
                array_splice($aViewBits, -2, 1, ['views']);

                $sControllerPath     = self::CONTROLLER_PATH . implode('/', array_slice($aControllerBits, 0, -1)) . '/';
                $sViewPath           = self::CONTROLLER_PATH . implode('/', array_slice($aViewBits, 0, -1)) . '/';
                $sControllerFilename = end($aControllerBits) . '.php';
                $sClassName          = ucfirst(underscoreToCamelcase(end($aControllerBits)));

                $aFields['CONTROLLER_PATH']     = $sControllerPath;
                $aFields['CONTROLLER_FILENAME'] = $sControllerFilename;
                $aFields['CONTROLLER_CLASS']    = $sClassName;
                $aFields['VIEW_PATH']           = $sViewPath;

                //  Check if the controller already exists
                if (file_exists($sControllerPath . $sControllerFilename)) {
                    throw new ConsoleException(
                        'Controller "' . $sController . '" already exists at "' . $sControllerPath . $sControllerFilename . '"'
                    );
                }

                //  Generate methods
                if ($aFields['MODULE_NAME'] === $aFields['CONTROLLER_NAME']) {
                    $sViewPathPrefix = '';
                    $sViewPrefix     = lcfirst($aFields['CONTROLLER_NAME']) . '/';
                } else {
                    $sViewPathPrefix = $aFields['CONTROLLER_NAME'] . '/';
                    $sViewPrefix     = lcfirst($aFields['MODULE_NAME']) . '/' . $aFields['CONTROLLER_NAME'] . '/';
                }

                $aMethodStrings = [];
                $aViews         = [];
                reset($aMethods);
                foreach ($aMethods as $sMethod) {

                    $aViews[]         = $sViewPathPrefix . $sMethod;
                    $aMethodStrings[] = '';
                    $aMethodStrings[] = $this->getResource(
                        'template/controller_method.php',
                        [
                            'METHOD_NAME' => $sMethod,
                            'METHOD_VIEW' => $sViewPrefix . $sMethod,
                        ]
                    );
                    $aMethodStrings[] = '';
                    $aMethodStrings[] = '// --------------------------------------------------------------------------';
                }

                //  Remove the first item (blank string) and the last two (separators)
                $aMethodStrings = array_slice($aMethodStrings, 1, -2);

                //  Add leading tabs
                array_walk(
                    $aMethodStrings,
                    function (&$sLine) {
                        $sLine = trim($sLine);
                        if (!empty($sLine)) {
                            $aLines = explode("\n", $sLine);
                            foreach ($aLines as &$sSingleLine) {
                                $sSingleLine = $this->tabs(1) . $sSingleLine;
                            }
                            $sLine = implode("\n", $aLines);
                        }
                    }
                );

                $aFields['METHODS'] = implode("\n", $aMethodStrings);

                //  Ensure the path exists
                $this->createPath($aFields['CONTROLLER_PATH']);

                //  Create the controller and write to it
                $this->createFile(
                    $aFields['CONTROLLER_PATH'] . $aFields['CONTROLLER_FILENAME'],
                    $this->getResource('template/controller.php', $aFields)
                );
                $aCreated[] = $aFields['CONTROLLER_PATH'] . $aFields['CONTROLLER_FILENAME'];
                $this->oOutput->writeln('<info>done!</info>');

                //  Create the views
                $this->oOutput->write('Creating views for controller <comment>' . $sController . '</comment>... ');
                $this->createPath($aFields['VIEW_PATH']);

                foreach ($aViews as $sView) {
                    $sViewPath    = $aFields['VIEW_PATH'] . $sView . '.php';
                    $sViewSubPath = dirname($sView);
                    if ($sViewSubPath !== '.') {
                        $this->createPath($aFields['VIEW_PATH'] . $sViewSubPath);
                    }
                    $this->createFile(
                        $sViewPath,
                        $this->getResource(
                            'template/view.php',
                            [
                                'VIEW' => preg_replace('#^' . NAILS_APP_PATH . '#', '', $sViewPath),
                            ]
                        )
                    );
                    $aCreated[] = $sView;

                }
                $this->oOutput->writeln('<info>done!</info>');
            }

        } catch (ConsoleException $e) {
            $this->oOutput->writeln('<error>failed!</error>');
            //  Clean up created models
            if (!empty($aCreated)) {
                $this->oOutput->writeln('<error>Cleaning up - removing newly created files</error>');
                foreach ($aCreated as $sPath) {
                    @unlink($sPath);
                }
            }
            throw new ConsoleException($e->getMessage());
        }
    }
}
