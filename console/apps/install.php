<?php

/**
 * ---------------------------------------------------------------
 * NAILS CONSOLE: INSTALLER
 * ---------------------------------------------------------------
 *
 * This app handles configuring and reconfiguring a Nails app.
 *
 * Lead Developer: Pablo de la Peña	(p@shedcollective.org, @hellopablo)
 * Lead Developer: Gary Duncan		(g@shedcollective.org, @gsdd)
 *
 * Documentation: http://nailsapp.co.uk/console/install
 */

namespace Nails\Console\Apps;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CORE_NAILS_Install extends Command
{
	/**
	 * Configures the app
	 * @return void
	 */
	protected function configure()
	{
		$this->setName('install');
		$this->setDescription('Configures or reconfigures a Nails site');
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
		$output->writeln('<info>---------------</info>');
		$output->writeln('<info>Nails Installer</info>');
		$output->writeln('<info>---------------</info>');
		$output->writeln('Beginning...');

		// --------------------------------------------------------------------------

		$output->writeln('<comment>TODO:</comment> check what\'s currently in place');
		$output->writeln('<comment>TODO:</comment> ask user for various app related settings (handle -no-interaction)');
		$output->writeln('<comment>TODO:</comment> ask user for various deploy related settings (handle -no-interaction)');
		$output->writeln('<comment>TODO:</comment> ask user which additional modules they would like to include');
		$output->writeln('<comment>TODO:</comment> run intial tests');
		$output->writeln('<comment>TODO:</comment> confirm with user what\'s going to happen (handle -no-interaction)');
		$output->writeln('<comment>TODO:</comment> execute (handle failures)');
		$output->writeln('<comment>TODO:</comment> migrate DB (handle failures)');

		// --------------------------------------------------------------------------

		$output->writeln('Cleaning up...');
		$output->writeln('Complete!');
	}
}

/* End of file install.php */
/* Location: ./nailsapp/common/console/apps/install.php */