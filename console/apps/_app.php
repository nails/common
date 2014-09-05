<?php

/**
 * ---------------------------------------------------------------
 * NAILS CONSOLE: CONSOLE APP HELPER METHODS
 * ---------------------------------------------------------------
 *
 * This class provides some basic helper methods to console apps.
 *
 * Lead Developer: Pablo de la Peña	(p@shedcollective.org, @hellopablo)
 * Lead Developer: Gary Duncan		(g@shedcollective.org, @gsdd)
 *
 * Documentation: http://nailsapp.co.uk/console/app
 */

namespace Nails\Console\Apps;

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Command\Command;

class CORE_NAILS_App extends Command
{
	protected function confirm( $question, $default, $input, $output )
	{
		$question		= is_array( $question ) ? implode( "\n", $question ) : $question;
		$helper			= $this->getHelper( 'question' );
		$defaultString	= $default ? 'Y' : 'N';
		$question		= new ConfirmationQuestion(  $question . ' [' . $defaultString . ']: ', $default ) ;

		return $helper->ask( $input, $output, $question );
	}

	// --------------------------------------------------------------------------

	protected function ask( $question, $default, $input, $output )
	{
		$question		= is_array( $question ) ? implode( "\n", $question ) : $question;
		$helper			= $this->getHelper( 'question' );
		$question		= new Question(  $question . ' [' . $default . ']: ', $default ) ;

		return $helper->ask( $input, $output, $question );
	}
}

/* End of file _app.php */
/* Location: ./nailsapp/common/console/apps/_app.php */