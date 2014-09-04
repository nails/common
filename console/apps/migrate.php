<?php

/**
 * ---------------------------------------------------------------
 * NAILS CONSOLE: DATABASE MIGRATION TOOL
 * ---------------------------------------------------------------
 *
 * This app handles migrating the parent app's database tables and
 * all enabled modules.
 *
 * Lead Developer: Pablo de la Peña	(p@shedcollective.org, @hellopablo)
 * Lead Developer: Gary Duncan		(g@shedcollective.org, @gsdd)
 *
 * Documentation: http://nailsapp.co.uk/console/migrate
 */

namespace Nails\Console\Apps;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CORE_NAILS_Migrate extends Command
{
	/**
	 * Configures the app
	 * @return void
	 */
	protected function configure()
	{
		$this->setName('migrate');
		$this->setDescription('Runs database migration across all enabled modules');
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

/* End of file migrate.php */
/* Location: ./nailsapp/common/console/apps/migrate.php */