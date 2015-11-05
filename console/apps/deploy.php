<?php

use Nails\Factory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

require_once 'vendor/nailsapp/common/console/apps/_app.php';

class CORE_NAILS_Deploy extends CORE_NAILS_App
{
    /**
     * Configures the app
     * @return void
     */
    protected function configure()
    {
        $this->setName('deploy');
        $this->setDescription('Sets up Nails after a fresh deploy');
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the app
     * @param  InputInterface  $input  The Input Interface proivided by Symfony
     * @param  OutputInterface $output The Output Interface proivided by Symfony
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln('<info>------------------</info>');
        $output->writeln('<info>Nails Post Deploy </info>');
        $output->writeln('<info>------------------</info>');
        $output->writeln('Beginning...');

        // --------------------------------------------------------------------------

        //  Load configs
        if (file_exists('config/app.php')) {

            $output->writeln('Found <comment>config/app.php</comment> will use values for defaults');
            require_once 'config/app.php';
        }

        if (file_exists('config/deploy.php')) {

            $output->writeln('Found <comment>config/deploy.php</comment> will use values for defaults');
            require_once 'config/deploy.php';
        }

        // --------------------------------------------------------------------------

        //  Setup Factory - config files are required prior to set up
        Factory::setup();

        // --------------------------------------------------------------------------

        //  Check environment
        if (strtoupper(ENVIRONMENT) == 'PRODUCTION') {

            $output->writeln('');
            $output->writeln('--------------------------------------');
            $output->writeln('| <info>WARNING: The app is in PRODUCTION.</info> |');
            $output->writeln('--------------------------------------');
            $output->writeln('');

            if (!$this->confirm('Continue with deployment?', true, $input, $output)) {

                $output->writeln('');
                $output->writeln('Aborting deployment.');
                return;
            }
        }

        // --------------------------------------------------------------------------

        $ok = true;
        $output->writeln('');
        $output->writeln('<info>Testing environment</info>');

        //  Shell exec
        $output->write('PHP\'s <comment>exec()</comment> is enabled... ');

        if (function_exists('exec')) {

            $output->writeln('<info>OK!</info>');

        } else {

            $output->writeln('<error>Not Found</error>');
            $ok = false;
        }

        if ($ok) {

            //  Composer exists
            $output->write('<comment>composer</comment> is installed... ');

            if ($this->cmdExists('composer')) {

                $output->writeln('<info>OK!</info>');
                $composerExecutable = 'composer';

            } elseif ($this->cmdExists('composer.phar')) {

                $output->writeln('<info>OK!</info>');
                $composerExecutable = 'composer.phar';

            } else {

                $output->writeln('<error>Not Found</error>');
                $ok = false;
            }

            //  Bower exists, but only if there's a bower.json
            if (file_exists('bower.json')) {

                $output->write('<comment>bower</comment> is installed... ');

                if ($this->cmdExists('composer')) {

                    $output->writeln('<info>OK!</info>');

                } else {

                    $output->writeln('<error>Not Found</error>');
                    $ok = false;
                }
            }
        }

        //  All good?
        if (!$ok) {

            $output->writeln('<error>Cannot Continue</error>');
            $output->writeln('The environment is not ready for deployment, you should roll your changes back.');
            return;
        }

        // --------------------------------------------------------------------------

        $output->writeln('');
        $output->writeln('<info>Beginning deployment</info>');

        // --------------------------------------------------------------------------

        //  Composer
        unset($execOutput);
        unset($exitCode);
        $output->write('<comment>Composer:</comment> Installing... ');

        $cmd  = $composerExecutable;
        $cmd .= $input->isInteractive() ? '' : ' --no-interaction';
        $cmd .= ' --prefer-dist --optimize-autoloader --no-dev install 2>&1';

        exec($cmd, $execOutput, $exitCode);

        if ($exitCode == 0) {

            $output->writeln('<info>OK!</info>');

        } else {

            $output->writeln('<error>FAILED</error>');
        }

        // --------------------------------------------------------------------------

        //  Bower
        if (file_exists('bower.json')) {

            unset($execOutput);
            unset($exitCode);
            $output->write('<comment>Bower:</comment> Installing... ');

            $cmd  = 'bower install';
            $cmd .= $input->isInteractive() ? '' : ' --config.interactive=false ';
            $cmd .= '2>&1';

            exec($cmd, $execOutput, $exitCode);

            if ($exitCode == 0) {

                $output->writeln('<info>OK!</info>');

            } else {

                $output->writeln('<error>FAILED</error>');
            }
        }

        // --------------------------------------------------------------------------

        //  Migration
        $output->writeln('');
        $output->writeln('<info>Beginning database migration</info>');

        $command = $this->getApplication()->find('migrate');

        $arguments = array(
            'command'          => 'migrate',
            '--no-interaction' => true
        );

        $input = new ArrayInput($arguments);
        $input->setInteractive($input->isInteractive());
        $command->run($input, $output);

        $output->writeln('<info>Finished database migration</info>');

        // --------------------------------------------------------------------------

        //  Cleaning up
        $output->writeln('');
        $output->writeln('<comment>Cleaning up...</comment>');

        // --------------------------------------------------------------------------

        //  And we're done
        $output->writeln('');
        $output->writeln('Complete!');
    }

    // --------------------------------------------------------------------------

    /**
     * Checks whether a particular command exists
     * @param  string $cmd The command to check
     * @return boolean
     */
    private function cmdExists($cmd)
    {
        return (bool) exec('which ' . $cmd);
    }
}
