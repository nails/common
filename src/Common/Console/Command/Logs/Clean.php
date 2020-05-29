<?php

/**
 * The logs:clean console command
 *
 * @package  Nails
 * @category Console
 */

namespace Nails\Common\Console\Command\Logs;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Service\Logger;
use Nails\Console\Command\Base;
use Nails\Factory;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Clean
 *
 * @package Nails\Common\Console\Command\Logs
 */
class Clean extends Base
{
    /**
     * The number of days to keep log files
     *
     * @var int
     */
    const LOG_RETENTION = 180;

    // --------------------------------------------------------------------------

    /**
     * Configure the logs:clean command
     */
    protected function configure()
    {
        $this
            ->setName('logs:clean')
            ->setDescription('Deletes log files older than ' . static::LOG_RETENTION . ' days');
    }

    // --------------------------------------------------------------------------

    /**
     * Execute the command
     *
     * @param InputInterface  $oInput  The Input Interface provided by Symfony
     * @param OutputInterface $oOutput The Output Interface provided by Symfony
     *
     * @return int
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        parent::execute($oInput, $oOutput);

        $this->banner('Logs: Clean');
        $oOutput->writeln('Cleaning log files older than <comment>' . static::LOG_RETENTION . '</comment> days');
        $oOutput->writeln('');

        /** @var Logger $oLogger */
        $oLogger = Factory::service('Logger');
        $this->cleanPath($oLogger->getDir());

        $oOutput->writeln('');
        $oOutput->writeln('Complete');
        $oOutput->writeln('');

        return static::EXIT_CODE_SUCCESS;
    }

    // --------------------------------------------------------------------------

    /**
     * Cleans a path
     *
     * @param string $sPath
     *
     * @return $this
     * @throws FactoryException
     */
    protected function cleanPath(string $sPath): Clean
    {
        $this->oOutput->writeln('Cleaning <comment>' . $sPath . '</comment>');

        $oFiles = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $sPath,
                RecursiveDirectoryIterator::SKIP_DOTS
            ),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        /** @var \DateTime $oNow */
        $oNow = Factory::factory('DateTime');
        $i    = 0;

        /** @var \SplFileInfo $oFile */
        foreach ($oFiles as $oFile) {

            $sFileName = basename($oFile->getRealPath());
            if (preg_match('/^.+\.php$/', $sFileName)) {

                $oModified = \DateTime::createFromFormat('U', $oFile->getMTime());

                if ($oNow->diff($oModified, true)->days > static::LOG_RETENTION) {
                    $i++;
                    $this->oOutput->writeln(' ↳ Removing <comment>' . $sFileName . '</comment>');
                }
            }
        }

        $this->oOutput->writeln('<comment>' . $i . '</comment> files were cleaned');

        return $this;
    }
}
