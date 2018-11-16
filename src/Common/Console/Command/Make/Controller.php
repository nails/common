<?php

namespace Nails\Common\Console\Command\Make;

use Nails\Console\Command\BaseMaker;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Controller extends BaseMaker
{
    const RESOURCE_PATH   = NAILS_COMMON_PATH . 'resources/console/';
    const CONTROLLER_PATH = NAILS_APP_PATH . 'application/modules/';
    const TAB_WIDTH       = 4;

    // --------------------------------------------------------------------------

    /**
     * Configure the command
     */
    protected function configure()
    {
        $this->setName('make:controller');
        $this->setDescription('Creates a new App controller');
        $this->addArgument(
            'controllerName',
            InputArgument::OPTIONAL,
            'Define the name of the controller to create'
        );
        $this->addArgument(
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
     * @param  InputInterface  $oInput  The Input Interface provided by Symfony
     * @param  OutputInterface $oOutput The Output Interface provided by Symfony
     *
     * @return int
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        parent::execute($oInput, $oOutput);

        // --------------------------------------------------------------------------

        try {
            //  Ensure the paths exist
            $this->createPath(self::CONTROLLER_PATH);
            //  Create the controller
            $this->createController();
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
    private function createController()
    {
        $aFields  = $this->getArguments();
        $aCreated = [];

        try {

            $aControllers = array_filter(explode(',', $aFields['CONTROLLER_NAME']));
            $aMethods     = explode(',', $aFields['METHODS']);
            sort($aControllers);

            foreach ($aControllers as $sController) {

                $aFields['CONTROLLER_NAME'] = $sController;
                $this->oOutput->write('Creating controller <comment>' . $sController . '</comment>... ');

                //  Work out where the controller should live
                $aControllerBits = explode('/', $sController);

                if (count($aControllerBits) > 2) {
                    throw new \Exception('Controllers cannot be deeper than one directory');
                }

                if (count($aControllerBits) > 1) {
                    //  A path has been specified, splice in the correct folder structure
                    array_splice($aControllerBits, -1, 0, ['controllers']);
                } else {
                    //  The controller should be in a folder called itself
                    $aControllerBits = array_merge($aControllerBits, ['controllers'], $aControllerBits);
                }

                //  Work out where the views should live
                $aViewBits = explode('/', $sController);

                if (count($aViewBits) > 2) {
                    throw new \Exception('Controllers cannot be deeper than one directory');
                }

                if (count($aViewBits) > 1) {
                    //  A path has been specified, splice in the correct folder structure
                    array_splice($aViewBits, -1, 0, ['views']);
                } else {
                    //  The controller should be in a folder called itself
                    $aViewBits = array_merge($aViewBits, ['views']);
                }

                $sControllerPath = self::CONTROLLER_PATH . implode('/', array_slice($aControllerBits, 0, -1)) . '/';
                $sFilename       = end($aControllerBits) . '.php';
                $sClassName      = ucfirst(underscoreToCamelcase(end($aControllerBits)));

                $aFields['CONTROLLER_PATH']     = $sControllerPath;
                $aFields['CONTROLLER_FILENAME'] = $sFilename;
                $aFields['CONTROLLER_CLASS']    = $sClassName;

                //  Check if the controller already exists
                if (file_exists($sControllerPath . $sFilename)) {
                    throw new \Exception(
                        'Controller "' . $sController . '" already exists at "' . $sControllerPath . $sFilename . '"'
                    );
                }

                //  Generate methods
                $aMethodStrings = [];
                reset($aMethods);
                foreach ($aMethods as $sMethod) {

                    $sMethodView      = $sController . '/' . $sMethod;
                    $aMethodStrings[] = '';
                    $aMethodStrings[] = $this->getResource(
                        'template/controller_method.php',
                        [
                            'METHOD_NAME' => $sMethod,
                            'METHOD_VIEW' => $sMethodView,
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
                $this->createPath($sControllerPath);

                //  Create the controller and write to it
                $this->createFile(
                    $sControllerPath . $sFilename,
                    $this->getResource('template/controller.php', $aFields)
                );
                $aCreated[] = $sControllerPath . $sFilename;
                $this->oOutput->writeln('<info>done!</info>');

                //  Create the views
                $this->oOutput->write('Creating views for controller <comment>' . $sController . '</comment>... ');
                $sViewPath = static::CONTROLLER_PATH . implode('/', $aViewBits) . '/';
                $this->createPath($sViewPath);

                foreach ($aMethods as $sMethod) {
                    $sView = $sViewPath . $sMethod . '.php';
                    $this->createFile(
                        $sView,
                        $this->getResource(
                            'template/view.php',
                            [
                                'VIEW' => preg_replace('#^' . NAILS_APP_PATH . '#', '', $sView),
                            ]
                        )
                    );
                    $aCreated[] = $sView;

                }
                $this->oOutput->writeln('<info>done!</info>');
            }

        } catch (\Exception $e) {
            $this->oOutput->writeln('<error>failed!</error>');
            //  Clean up created models
            if (!empty($aCreated)) {
                $this->oOutput->writeln('<error>Cleaning up - removing newly created files</error>');
                foreach ($aCreated as $sPath) {
                    @unlink($sPath);
                }
            }
            throw new \Exception($e->getMessage());
        }
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
