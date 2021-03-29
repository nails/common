<?php

namespace Nails\Common\Console\Command\Database;

use Nails\Common\Service\AppSetting;
use Nails\Console\Command\Base;
use Nails\Config;
use Nails\Environment;
use Nails\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Rebuild
 *
 * @package Nails\Common\Console\Command\Database
 */
class Rebuild extends Base
{
    /**
     * Configures the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('db:rebuild');
        $this->setDescription('Drops every table in the database and runs migrations');
        $this->addOption(
            'dbHost',
            null,
            InputOption::VALUE_OPTIONAL,
            'Database Host'
        );
        $this->addOption(
            'dbUser',
            null,
            InputOption::VALUE_OPTIONAL,
            'Database User'
        );
        $this->addOption(
            'dbPass',
            null,
            InputOption::VALUE_OPTIONAL,
            'Database Password'
        );
        $this->addOption(
            'dbName',
            null,
            InputOption::VALUE_OPTIONAL,
            'Database Name'
        );
        $this->addOption(
            'dbPort',
            null,
            InputOption::VALUE_OPTIONAL,
            'Database Port'
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
     * @throws \Exception
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        parent::execute($oInput, $oOutput);

        $this->banner('Database: Rebuild');

        // --------------------------------------------------------------------------

        //  Check environment
        if (Environment::is(Environment::ENV_PROD)) {
            $this->banner('WARNING: The app is in PRODUCTION', 'error');
            $oOutput->writeln('<error>Aborting rebuild.</error>');
            return static::EXIT_CODE_FAILURE;
        }

        // --------------------------------------------------------------------------

        //  Work out the DB credentials to use
        $sDbHost = $oInput->getOption('dbHost') ?: Config::get('DB_HOST');
        $sDbUser = $oInput->getOption('dbUser') ?: Config::get('DB_USERNAME');
        $sDbPass = $oInput->getOption('dbPass') ?: Config::get('DB_PASSWORD');
        $sDbName = $oInput->getOption('dbName') ?: Config::get('DB_DATABASE');
        $iDbPort = $oInput->getOption('dbPort') ?: Config::get('DB_Port');

        //  Check we have a database to connect to
        if (empty($sDbName)) {
            return $this->abort(static::EXIT_CODE_NO_DB);
        }

        //  Get the DB object
        $oDb = Factory::service('PDODatabase');
        $oDb->connect($sDbHost, $sDbUser, $sDbPass, $sDbName, $iDbPort);

        // --------------------------------------------------------------------------

        //  Which tables are we going to drop; all those which match our prefixes
        $oDb      = Factory::service('PDODatabase');
        $oResult  = $oDb->query('SHOW TABLES');
        $aResults = $oResult->fetchAll(\PDO::FETCH_OBJ);
        $aTables  = [];
        foreach ($aResults as $aResult) {
            $sTable = reset($aResult);
            if (preg_match('/^(' . Config::get('NAILS_DB_PREFIX') . '|' . Config::get('APP_DB_PREFIX') . ')/', $sTable)) {
                $aTables[] = $sTable;
            }
        }

        // --------------------------------------------------------------------------

        $oOutput->writeln('The following database tables will be dropped, and migrations run.');
        $oOutput->writeln('');
        foreach ($aTables as $sTable) {
            $oOutput->writeln(' - <comment>' . $sTable . '</comment>');
        }
        $oOutput->writeln('');

        if ($this->confirm('Continue?', true)) {
            $oOutput->writeln('');
            $oOutput->write('Dropping tables... ');
            $oDb->query('SET FOREIGN_KEY_CHECKS = 0;');
            foreach ($aTables as $sTable) {
                $oDb->query('DROP TABLE IF EXISTS `' . $sTable . '`;');
            }
            $oDb->query('SET FOREIGN_KEY_CHECKS = 1;');
            $oOutput->writeln('<info>done</info>');
            $oOutput->write('Migrating database... ');
            $iExitCode = $this->callCommand(
                'db:migrate',
                array_filter([
                    '--dbHost' => $sDbHost,
                    '--dbUser' => $sDbUser,
                    '--dbPass' => $sDbPass,
                    '--dbName' => $sDbName,
                    '-v'       => $oOutput->getVerbosity() === $oOutput::VERBOSITY_VERBOSE,
                    '-vv'      => $oOutput->getVerbosity() === $oOutput::VERBOSITY_VERY_VERBOSE,
                    '-vvv'     => $oOutput->getVerbosity() === $oOutput::VERBOSITY_DEBUG,
                ]),
                false,
                $oOutput->getVerbosity() <= $oOutput::VERBOSITY_NORMAL
            );

            if ($iExitCode == static::EXIT_CODE_SUCCESS) {
                $oOutput->writeln('<info>done</info>');
            } else {
                $oOutput->writeln('<error>fail</error>');
                return $this->abort(
                    self::EXIT_CODE_FAILURE,
                    [
                        'The Migration tool encountered issues and aborted the migration.',
                        'You should run it manually and investigate any issues.',
                        'The exit code was ' . $iExitCode,
                    ]
                );
            }
        }

        // --------------------------------------------------------------------------

        //  Cleaning up
        $oOutput->writeln('');
        $oOutput->writeln('<comment>Cleaning up</comment>...');

        /**
         * Reload app settings; rebuilding the database might have changed these and
         * any code called after this will want the latests ettings loaded.
         */
        /** @var AppSetting $oAppSettingService */
        $oAppSettingService = Factory::service('AppSetting');
        $oAppSettingService->load();

        // --------------------------------------------------------------------------

        //  And we're done
        $oOutput->writeln('');
        $oOutput->writeln('Complete!');

        return static::EXIT_CODE_SUCCESS;
    }
}
