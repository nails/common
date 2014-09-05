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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

require_once 'vendor/nailsapp/common/console/apps/_app.php';

class CORE_NAILS_Install extends CORE_NAILS_App
{
	/**
	 * Configures the app
	 * @return void
	 */
	protected function configure()
	{
		$this->setName( 'install' );
		$this->setDescription( 'Configures or reconfigures a Nails site' );
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
		$output->writeln( '<info>---------------</info>' );
		$output->writeln( '<info>Nails Installer</info>' );
		$output->writeln( '<info>---------------</info>' );
		$output->writeln( 'Beginning...' );

		// --------------------------------------------------------------------------

		//	Load configs
		if ( file_exists( 'config/app.php' ) ) :

			$output->writeln( 'Found <comment>config/app.php</comment> will use values for defaults' );
			include 'config/app.php';

		endif;

		if ( file_exists( 'config/deploy.php' ) ) :

			$output->writeln( 'Found <comment>config/deploy.php</comment> will use values for defaults' );
			include 'config/deploy.php';

		endif;

		// --------------------------------------------------------------------------

		$output->writeln( '' );
		$output->writeln( '<info>App Settings</info>' );
		$_app_name = $this->ask( 'What\'s the name of this app?', 'My App', $input, $output  );

		// --------------------------------------------------------------------------

		$output->writeln( '' );
		$output->writeln( '<info>Deploy Settings</info>' );
		$_deploy_environment = $this->ask( 'What should the environment be set to?', 'PRODUCTION', $input, $output  );

		// --------------------------------------------------------------------------

		$output->writeln( '<comment>TODO:</comment> ask user which additional modules they would like to include' );
		$output->writeln( '<comment>TODO:</comment> run intial tests' );
		$output->writeln( '<comment>TODO:</comment> confirm with user what\'s going to happen (handle -no-interaction)' );
		$output->writeln( '<comment>TODO:</comment> execute (handle failures)' );
		$output->writeln( '<comment>TODO:</comment> migrate DB (handle failures)' );

		// --------------------------------------------------------------------------

		$output->writeln( 'Cleaning up...' );
		$output->writeln( 'Complete!' );
	}
}

/* End of file install.php */
/* Location: ./nailsapp/common/console/apps/install.php */