<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fatal_error_handler
{
	/**
	 * @return void
	 */
	public function __construct()
	{
		register_shutdown_function( array( &$this, 'handleShutdown' ) );
	}


	// --------------------------------------------------------------------------


	/**
	 * @return void
	 */
	public function handleShutdown()
	{
		//	On non production systems don't bother reporting
		if ( ENVIRONMENT != 'production' ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$aError = error_get_last();

		if ( ! is_null( $aError ) && $aError['type'] === E_ERROR ) :

			$this->saveAndEmailFatal( $aError );

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * This method will gather together most variables, the callstack and the actual triggering error
	 * and then email the whole lot.
	 *
	 * @param unknown_type $insSeverity
	 * @param unknown_type $insMessage
	 * @param unknown_type $insFilePath
	 * @param unknown_type $insLine
	 */
	public function saveAndEmailError( $insSeverity, $insMessage, $insFilePath, $insLine, $infFull = true )
	{
		$oCI =& get_instance();

		$aInfo = array(
			'type'				=> $infFull? 'Triggered' : 'HandleShutdown',
			'severity'			=> $this->getSeverityText( $insSeverity ),
			'message'			=> $insMessage,
			'filepath'			=> $insFilePath,
			'line'				=> $insLine,
			'session'			=> json_encode( $oCI->session->userdata ),
			'post'				=> json_encode( $_POST ),
			'get'				=> json_encode( $_GET ),
			'server'			=> json_encode( $_SERVER ),
			'globals'			=> isset( $GLOBALS['error'] )? json_encode( $GLOBALS['error'] ): '',
			'uri'				=> $oCI->uri->uri_string(),
			'debug_backtrace'	=> json_encode( debug_backtrace() )
		);

		//	Prep the email and send
		if ( isset( $_SERVER['HTTP_HOST'] ) ) :

			$_host = $_SERVER['HTTP_HOST'];

		else :

			if ( $oCI->input->is_cli_request() || isset( $_SERVER['argv'] ) ) :

				//	CLI
				$_host = 'CLI REQUEST';

			else :

				$_host = 'UNABLE TO DETERMINE HOST, SERVER HOST: ' . gethostname();

			endif;

		endif;

		$_subject	= 'FATAL ERROR OCCURRED ON ' . strtoupper( APP_NAME );
		$_message	= 'Hi,' . "\n";
		$_message	.= '' . "\n";
		$_message	.= 'A Fatal Error just occurred within ' . APP_NAME . ' (' . $_host . ')' . "\n";
		$_message	.= '' . "\n";
		$_message	.= 'Please take a look as a matter of urgency; details are noted below:' . "\n";
		$_message	.= '' . "\n";
		$_message	.= '- - - - - - - - - - - - - - - - - - - - - -' . "\n";
		$_message	.= '' . "\n";

		$_message	.= 'Type: ' . $aInfo['type'] . "\n";
		$_message	.= 'Severity: ' . $aInfo['severity'] . "\n";
		$_message	.= 'Message: ' . $aInfo['message'] . "\n";
		$_message	.= 'File Path: ' . $aInfo['filepath'] . "\n";
		$_message	.= 'Line: ' . $aInfo['line'] . "\n\n";

		$this->send_developer_mail( $_subject, $_message );

		// --------------------------------------------------------------------------

		//	Log the error
		$_message = $aInfo['severity'] . ': ' . $aInfo['message'] . ' in ' . $aInfo['filepath'] . ' on line ' . $aInfo['line'];
		log_message( 'error', $_message );

		// --------------------------------------------------------------------------

		//	Show something to the user
		echo $this->show();
	}


	// --------------------------------------------------------------------------


	public function show()
	{
		if ( is_file( FCPATH . APPPATH . 'errors/error_fatal.php' ) ) :

			include_once FCPATH . APPPATH . 'errors/error_fatal.php';

		else :

			include_once NAILS_PATH . 'errors/error_fatal.php';

		endif;
	}


	// --------------------------------------------------------------------------


	public function saveAndEmailFatal( $inaError )
	{
		$this->saveAndEmailError( $inaError['type'], $inaError['message'], $inaError['file'], $inaError['line'], false );
	}


	// --------------------------------------------------------------------------


	private function getSeverityText( $innSeverity )
	{
		$aErrorType = array (
			E_ERROR				=> 'FATAL ERROR',
			E_WARNING			=> 'WARNING',
			E_PARSE				=> 'PARSING ERROR',
			E_NOTICE			=> 'NOTICE',
			E_CORE_ERROR		=> 'CORE ERROR',
			E_CORE_WARNING		=> 'CORE WARNING',
			E_COMPILE_ERROR		=> 'COMPILE ERROR',
			E_COMPILE_WARNING	=> 'COMPILE WARNING',
			E_USER_ERROR		=> 'USER ERROR',
			E_USER_WARNING		=> 'USER WARNING',
			E_USER_NOTICE		=> 'USER NOTICE',
			E_STRICT			=> 'STRICT NOTICE',
			E_RECOVERABLE_ERROR	=> 'RECOVERABLE ERROR'
		);
		return $aErrorType[$innSeverity];
	}


	// --------------------------------------------------------------------------


	public function send_developer_mail( $subject, $message )
	{
		if ( ! defined( 'APP_DEVELOPER_EMAIL' ) || ! APP_DEVELOPER_EMAIL ) :

			//	Log the fact there's no email
			log_message( 'error', 'Attempting to send developer email, but APP_DEVELOPER_EMAIL is not defined.' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$_from_email	= 'root@' . gethostname();
		$_from_name		= defined( 'APP_EMAIL_FROM_NAME' ) ? APP_EMAIL_FROM_NAME : 'Fatal Error Reporter';
		$_reply_to		= defined( 'APP_EMAIL_FROM_EMAIL' ) ? APP_EMAIL_FROM_EMAIL : $_from_email;

		$_to			= ENVIRONMENT != 'production' && defined( 'EMAIL_OVERRIDE' ) && EMAIL_OVERRIDE ? EMAIL_OVERRIDE : APP_DEVELOPER_EMAIL;
		$_headers		= 'From: ' . $_from_name . ' <' . $_from_email . '>' . "\r\n" .
						  'Reply-To: ' . $_reply_to . "\r\n" .
						  'X-Mailer: PHP/' . phpversion()  . "\r\n" .
						  'X-Priority: 1 (Highest)' . "\r\n" .
						  'X-Mailer: X-MSMail-Priority: High/' . "\r\n" .
						  'Importance: High';

		$_message	 = $message;

		// --------------------------------------------------------------------------

		$_ci =& get_instance();

		$_info = array(
			'uri'				=> isset( $_ci->uri )			? $_ci->uri->uri_string()				: '',
			'session'			=> isset( $_ci->session )		? serialize( $_ci->session->userdata )	: '',
			'post'				=> isset( $_POST )				? serialize( $_POST )					: '',
			'get'				=> isset( $_GET )				? serialize( $_GET )					: '',
			'server'			=> isset( $_SERVER )			? serialize( $_SERVER )					: '',
			'globals'			=> isset( $GLOBALS['error'] )	? serialize( $GLOBALS['error'] )		: '',

			'debug_backtrace'	=> serialize( debug_backtrace() )
		);

		$_message	.= '' . "\n";
		$_message	.= '- - - - - - - - - - - - - - - - - - - - - -' . "\n";
		$_message	.= '' . "\n";
		$_message	.= 'DEBUGGING DATA' . "\n";
		$_message	.= '' . "\n";
		$_message	.= 'URI: ' .		$_info['uri'] . "\n\n";
		$_message	.= 'SESSION: ' .	$_info['session'] . "\n\n";
		$_message	.= 'POST: ' .		$_info['post'] . "\n\n";
		$_message	.= 'GET: ' .		$_info['get'] . "\n\n";
		$_message	.= 'SERVER: ' .		$_info['server'] . "\n\n";
		$_message	.= 'GLOBALS: ' .	$_info['globals'] . "\n\n";
		$_message	.= 'BACKTRACE: ' .	$_info['debug_backtrace'] . "\n\n";

		if ( defined( 'NAILS_DB_ENABLED' ) && NAILS_DB_ENABLED ) :

			$_message	.= 'LAST KNOWN QUERY: ' . $_ci->db->last_query() . "\n\n";

		endif;

		@mail( $_to, '!! ' . $subject . ' - ' . APP_NAME , $message, $_headers );
	}
}

/* End of file fatal_error_hook.php */
/* Location: ./application/hooks/fatal_error_hook.php */