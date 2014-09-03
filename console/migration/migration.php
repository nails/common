<?php

/*
 | --------------------------------------------------------------------
 | NAILS MIGRATION TOOL
 | --------------------------------------------------------------------
 |
 | This class handles database migrations
 |
 | Documentation: http://docs.nailsapp.co.uk
 |
 */

namespace Nails\Console\MigrationTool;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CORE_NAILS_MigrationTool extends Command
{
	protected function configure()
	{
		$this
			->setName('go')
			->setDescription('Runs database migration across all enabled modules.')
		;
	}

	// --------------------------------------------------------------------------

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('<info>-----------------------------</info>');
		$output->writeln('<info>Nails Database Migration Tool</info>');
		$output->writeln('<info>-----------------------------</info>');
		$output->writeln('Beginning...');

		$output->writeln('<comment>TODO:</comment> check environment and confirm if production (handle -no-interaction)');
		$output->writeln('<comment>TODO:</comment> fetch all enabled modules and look for migration scripts');
		$output->writeln('<comment>TODO:</comment> gather everything and test things');
		$output->writeln('<comment>TODO:</comment> setup migration table stuff');
		$output->writeln('<comment>TODO:</comment> confirm with user what\'s going to happen (handle -no-interaction)');
		$output->writeln('<comment>TODO:</comment> execute migrations (handle failures)');

		$output->writeln('Cleaning up...');
		$output->writeln('Complete!');
	}
}