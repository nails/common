<?php

namespace Nails\Common\Console\Command\Routes;

use Nails\Common\Exception\NailsException;
use Nails\Console\Command\Base;
use Nails\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Rewrite extends Base
{
    /**
     * Configures the app
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('routes:rewrite');
        $this->setDescription('Rewrites the App\'s routes file');
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
    protected function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        parent::execute($oInput, $oOutput);

        $this->banner('Nails Routes Rewrite');

        $oRoutesService = Factory::service('Routes');

        try {
            if (!$oRoutesService->update(null, $oOutput)) {
                throw new NailsException($oRoutesService->lastError());
            }
        } catch (\Exception $e) {
            $this->abort(
                static::EXIT_CODE_FAILURE,
                [
                    'There was a problem writing the routes.',
                    $e->getMessage(),
                ]
            );
        }

        //  Cleaning up
        $oOutput->writeln('');
        $oOutput->writeln('<comment>Cleaning up...</comment>');

        //  And we're done!
        $oOutput->writeln('');
        $oOutput->writeln('Complete!');

        return static::EXIT_CODE_SUCCESS;
    }
}
