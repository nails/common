<?php

/**
 * ---------------------------------------------------------------
 * NAILS CONSOLE: DEPLOY
 * ---------------------------------------------------------------
 *
 * This app handles deploying a Nails app
 *
 * Lead Developer: Pablo de la Peña	(p@shedcollective.org, @hellopablo)
 * Lead Developer: Gary Duncan		(g@shedcollective.org, @gsdd)
 *
 * Documentation: http://nailsapp.co.uk/console/deploy
 */

namespace Nails\Console\Apps;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

require_once 'vendor/nailsapp/common/console/apps/_app.php';

class CORE_NAILS_Deploy extends CORE_NAILS_App
{
	/**
	 * Configures the app
	 * @return void
	 */
	protected function configure()
	{
		$this->setName( 'deploy' );
		$this->setDescription( 'Sets up Nails after a fresh deploy' );
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
		$output->writeln( '<info>------------------</info>' );
		$output->writeln( '<info>Nails Post Deploy </info>' );
		$output->writeln( '<info>------------------</info>' );
		$output->writeln( 'Beginning...' );

		// --------------------------------------------------------------------------

		//	Load configs
		if ( file_exists( 'config/app.php' ) ) :

			$output->writeln( 'Found <comment>config/app.php</comment> will use values for defaults' );
			require_once 'config/app.php';

		endif;

		if ( file_exists( 'config/deploy.php' ) ) :

			$output->writeln( 'Found <comment>config/deploy.php</comment> will use values for defaults' );
			require_once 'config/deploy.php';

		endif;

		// --------------------------------------------------------------------------

		//	Check environment
		if ( strtoupper( ENVIRONMENT ) == 'PRODUCTION' ) :

			$output->writeln( '' );
			$output->writeln( '--------------------------------------' );
			$output->writeln( '| <info>WARNING: The app is in PRODUCTION.</info> |' );
			$output->writeln( '--------------------------------------' );
			$output->writeln( '' );

			if ( ! $this->confirm( 'Continue with deployment?', TRUE, $input, $output ) ) :

				$output->writeln( '' );
				$output->writeln( 'Aborting deployment.' );
				return;

			endif;

		endif;

		// --------------------------------------------------------------------------

		$_ok = TRUE;
		$output->writeln( '' );
		$output->writeln( '<info>Testing environment</info>' );

		//	Shell exec
		$output->write( 'PHP\'s <comment>exec()</comment> is enabled... ' );

		if ( function_exists( 'exec' ) ) :

			$output->writeln( '<info>OK!</info>' );

		else :

			$output->writeln( '<error>Not Found</error>' );
			$_ok = FALSE;

		endif;

		if ( $_ok ) :

			//	Composer exists
			$output->write( '<comment>composer</comment> is installed... ' );
			if ( $this->_cmd_exists( 'composer' ) ) :

				$output->writeln( '<info>OK!</info>' );
				$_composer_executable = 'composer';

			elseif ( $this->_cmd_exists( 'composer.phar' ) ) :

				$output->writeln( '<info>OK!</info>' );
				$_composer_executable = 'composer.phar';

			else :

				$output->writeln( '<error>Not Found</error>' );
				$_ok = FALSE;

			endif;

			//	Bower exists, but only if there's a bower.json
			if ( file_exists( 'bower.json' ) ) :

				$output->write( '<comment>bower</comment> is installed... ' );
				if ( $this->_cmd_exists( 'composer' ) ) :

					$output->writeln( '<info>OK!</info>' );

				else :

					$output->writeln( '<error>Not Found</error>' );
					$_ok = FALSE;

				endif;

			endif;

		endif;

		//	All good?
		if ( ! $_ok ) :

			$output->writeln( '<error>Cannot Continue</error>' );
			$output->writeln( 'The environment is not ready for deployment, you should roll your changes back.' );
			return;

		endif;

		// --------------------------------------------------------------------------

		$output->writeln( '' );
		$output->writeln( '<info>Beginning deployment</info>' );

		// --------------------------------------------------------------------------

		//	Composer
		unset($exec_output);
		unset($exit_code);
		$output->write( '<comment>Composer:</comment> Installing... ' );
		$_interactive = $input->isInteractive() ? '' : ' --no-interaction';
		exec( $_composer_executable . $_interactive . ' --prefer-dist --optimize-autoloader --no-dev install 2>&1', $exec_output, $exit_code );

		if ( $exit_code == 0 ) :

			$output->writeln( '<info>OK!</info>' );

		else :

			$output->writeln( '<error>FAILED</error>' );

		endif;

		// --------------------------------------------------------------------------

		//	Bower
		if ( file_exists( 'bower.json' ) ) :

			unset($exec_output);
			unset($exit_code);
			$output->write( '<comment>Bower:</comment> Installing... ' );
			$_interactive = $input->isInteractive() ? '' : ' --config.interactive=false';
			exec( 'bower install' . $_interactive . ' 2>&1', $exec_output, $exit_code );

			if ( $exit_code == 0 ) :

				$output->writeln( '<info>OK!</info>' );

			else :

				$output->writeln( '<error>FAILED</error>' );

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Migration
		$output->writeln( '' );
		$output->writeln( '<info>Beginning database migration</info>' );

		$command = $this->getApplication()->find( 'migrate' );

		$arguments = array(
			'command'			=> 'migrate',
			'--no-interaction'	=> TRUE
		);

		$_input		= new ArrayInput( $arguments );
		$_input->setInteractive( $input->isInteractive() );
		$_result	= $command->run($_input, $output);

		$output->writeln( '<info>Finished database migration</info>' );

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


	private function _cmd_exists( $cmd )
	{
		return (bool) exec( 'which ' . $cmd );
	}
}

/* End of file install.php */
/* Location: ./nailsapp/common/console/apps/install.php */