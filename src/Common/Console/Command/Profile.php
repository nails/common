<?php

/**
 * The profile console command
 *
 * @package  Nails
 * @category Console
 */

namespace Nails\Common\Console\Command;

use Nails\Common\Factory\HttpRequest\Get;
use Nails\Common\Service\FileCache;
use Nails\Common\Service\Profiler;
use Nails\Console\Command\Base;
use Nails\Factory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Profile
 *
 * @package Nails\Common\Console\Command
 */
class Profile extends Base
{
    /**
     * Configure the logs:clean command
     */
    protected function configure()
    {
        $this
            ->setName('profile')
            ->setDescription('Profiles a URL')
            ->addArgument('url', InputArgument::REQUIRED, 'The URL to profile');
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

        $url = $oInput->getArgument('url');

        $this->banner('Profiling: ' . siteUrl($url));

        $oOutput->write('Fetching page... ');

        /** @var Get $oRequest */
        $oRequest = Factory::factory('HttpRequestGet');
        $oRequest
            ->baseUri(siteUrl())
            ->path($url)
            ->setHeader(Profiler::HEADER_PROFILER_ENABLE, Profiler::headerSecret());

        $oResponse = $oRequest->execute();

        if ($oResponse->getStatusCode() !== 200) {
            $oOutput->writeln('<error>expected 200, got ' . $oResponse->getStatusCode() . '</error>');
            return static::EXIT_CODE_FAILURE;
        }

        $oOutput->writeln('<info>done</info>');

        $oOutput->write('Parsing report... ');

        //  Look for the most recent profiler report in the cache
        /** @var FileCache $oFileCache */
        $oFileCache  = Factory::service('FileCache');
        $aCacheFiles = scandir($oFileCache->getDir());
        $aCacheFiles = array_filter($aCacheFiles, function ($sFile) {
            return preg_match('/^profiler-\d\d\d\d-\d\d-\d\d-\d\d-\d\d-\d\d\.json$/', $sFile);
        });
        sort($aCacheFiles);
        $sLatestFile = array_pop($aCacheFiles);

        $oReport = json_decode(file_get_contents($oFileCache->getDir() . $sLatestFile));

        $oOutput->writeln('<info>done</info>');

        //  Render the report
        $oOutput->writeln('');
        $this->keyValueList([
            'Full Report' => $oFileCache->getDir() . $sLatestFile,
        ], bPadTop: false);

        $oOutput->writeln('<info>Timestamps</info>');
        $this->keyValueList([
            'Count'     => $oReport->Timestamps->Count,
            'Total (s)' => $oReport->Timestamps->{'Total (s)'},
        ], bPadTop: false);

        $oOutput->writeln('<info>Queries</info>');
        $this->keyValueList([
            'Count'     => $oReport->Database->Count,
            'Total (s)' => $oReport->Database->{'Total (s)'},
        ], bPadTop: false);

        return static::EXIT_CODE_SUCCESS;
    }
}
