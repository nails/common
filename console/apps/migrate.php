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
use Symfony\Component\Console\Question\ConfirmationQuestion;

require_once 'vendor/nailsapp/common/console/apps/_app.php';

class CORE_NAILS_Migrate extends CORE_NAILS_App
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
		$output->writeln( '<info>-----------------------------</info>' );
		$output->writeln( '<info>Nails Database Migration Tool</info>' );
		$output->writeln( '<info>-----------------------------</info>' );

		// --------------------------------------------------------------------------

		//	Load configs
		if ( ! file_exists( 'config/deploy.php' ) ) :

			$output->writeln( '<error>ERROR:</error> Could not load config/deploy.php.' );
			return FALSE;

		endif;

		require_once 'config/deploy.php';

		if ( ! defined( 'ENVIRONMENT' ) ) :

			$output->writeln( '<error>ERROR:</error> ENVIRONMENT is not defined.' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Check environment
		if ( strtoupper( ENVIRONMENT ) == 'PRODUCTION' ) :

			$output->writeln( '' );
			$output->writeln( '--------------------------------------' );
			$output->writeln( '| <info>WARNING: The app is in PRODUCTION.</info> |' );
			$output->writeln( '--------------------------------------' );
			$output->writeln( '' );

			if ( ! $this->confirm( 'Continue with migration?', TRUE, $input, $output ) ) :

				$output->writeln( '' );
				$output->writeln( 'Aborting migration.' );
				return;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Look for enabled modules
		$output->writeln( '' );
		$output->write( '<comment>Searching for modules... </comment>' );
		$output->writeln( 'found <info>4</info> modules' );

		// --------------------------------------------------------------------------

		//	Gather everything and perform any preliminary tests
		$output->writeln( '<comment>Preparing for migration...</comment> done' );

		// --------------------------------------------------------------------------

		//	Confirm what's going to happen
		$output->writeln( '' );
		$output->writeln( 'The following modules are to be migrated:' );
		$output->writeln( ' - <comment>nailsapp/module-name</comment> from <info>0</info> to <info>123</info>' );
		$output->writeln( ' - <comment>nailsapp/module-name</comment> from <info>0</info> to <info>123</info>' );
		$output->writeln( ' - <comment>nailsapp/module-name</comment> from <info>0</info> to <info>123</info>' );
		$output->writeln( ' - <comment>nailsapp/module-name</comment> from <info>0</info> to <info>123</info>' );
		$output->writeln( '' );

		if ( ! $this->confirm( 'Continue?', TRUE, $input, $output ) ) :

			$output->writeln( '' );
			$output->writeln( 'Aborting migration.' );
			return;

		endif;

		// --------------------------------------------------------------------------

		$output->writeln( '' );
		$output->writeln( '<comment>Starting migration...</comment>' );
		$progress = $this->getHelper('progress');

		$progress->start($output, 4);
		$progress->setCurrent( 0 );
		$i = 0;
		while ($i++ < 4) :

			//	Do something
			sleep(1);

			// Advances the progress bar 1 unit
			$progress->advance();

		endwhile;

		$progress->finish();


		// --------------------------------------------------------------------------

		//	Cleaning up
		$output->writeln( '' );
		$output->writeln( '<comment>Cleaning up...</comment>' );

		// --------------------------------------------------------------------------

		//	And we're done
		$output->writeln( '' );
		$output->writeln( 'Complete!' );
	}


	// --------------------------------------------------------------------------


	protected function confirm( $question, $default, $input, $output )
	{
		$question		= is_array( $question ) ? implode( "\n", $question ) : $question;
		$helper			= $this->getHelper( 'question' );
		$defaultString	= $default ? 'Y' : 'N';
		$question		= new ConfirmationQuestion(  $question . ' [' . $defaultString . ']: ', $default ) ;

		return $helper->ask( $input, $output, $question );
	}
}

/* End of file migrate.php */
/* Location: ./nailsapp/common/console/apps/migrate.php */