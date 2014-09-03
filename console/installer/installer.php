<?php

/*
 | --------------------------------------------------------------------
 | NAILS MIGRATION TOOL
 | --------------------------------------------------------------------
 |
 | This class handles initial installation
 |
 | Documentation: http://docs.nailsapp.co.uk
 |
 */

namespace Nails\Console\Installer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CORE_NAILS_Installer extends Command
{
	protected function configure()
	{
		$this
			->setName('go')
			->setDescription('Configures a Nails site')
		;
	}

	// --------------------------------------------------------------------------

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('<info>---------------</info>');
		$output->writeln('<info>Nails Installer</info>');
		$output->writeln('<info>---------------</info>');
		$output->writeln('Beginning...');

		$output->writeln('<comment>TODO:</comment> check what\'s currently in place');
		$output->writeln('<comment>TODO:</comment> ask user for various app related settings (handle -no-interaction)');
		$output->writeln('<comment>TODO:</comment> ask user for various deploy related settings (handle -no-interaction)');
		$output->writeln('<comment>TODO:</comment> ask user which additional modules they would like to include');
		$output->writeln('<comment>TODO:</comment> run intial tests');
		$output->writeln('<comment>TODO:</comment> confirm with user what\'s going to happen (handle -no-interaction)');
		$output->writeln('<comment>TODO:</comment> execute (handle failures)');
		$output->writeln('<comment>TODO:</comment> migrate DB (handle failures)');

		$output->writeln('Cleaning up...');
		$output->writeln('Complete!');
	}
}