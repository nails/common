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
		//	On non-production systems don't bother reporting
		if ( strtoupper( ENVIRONMENT ) != 'PRODUCTION' ) :

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
		$_message	.= 'Please take a look as a matter of urgency; details are noted below and extended system state data is attached:' . "\n";
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


	public function show( $subject = '', $message = '' )
	{
		if ( is_file( FCPATH . APPPATH . 'errors/error_fatal.php' ) ) :

			include_once FCPATH . APPPATH . 'errors/error_fatal.php';

		else :

			include_once NAILS_COMMON_PATH . 'errors/error_fatal.php';

		endif;
		exit(0);
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
		if ( ! APP_DEVELOPER_EMAIL ) :

			//	Log the fact there's no email
			log_message( 'error', 'Attempting to send developer email, but APP_DEVELOPER_EMAIL is not defined.' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$_from_email = 'root@' . gethostname();

		if ( function_exists( 'app_setting' ) ) :

			$_from_name = app_setting( 'from_name', 'email' );

			if ( empty( $_from_name ) ) :

				$_from_name = 'Log Error Reporter';

			endif;

			$_reply_to = app_setting( 'from_email', 'email' );

			if ( empty( $_reply_to ) ) :

				$_reply_to = 'Fatal Error Reporter';

			endif;

		else :

			$_from_name	= 'Fatal Error Reporter';
			$_reply_to	= $_from_email;

		endif;

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

		$_extended	 = 'URI: ' .				$_info['uri'] . "\n\n";
		$_extended	.= 'SESSION: ' .			$_info['session'] . "\n\n";
		$_extended	.= 'POST: ' .				$_info['post'] . "\n\n";
		$_extended	.= 'GET: ' .				$_info['get'] . "\n\n";
		$_extended	.= 'SERVER: ' .				$_info['server'] . "\n\n";
		$_extended	.= 'GLOBALS: ' .			$_info['globals'] . "\n\n";
		$_extended	.= 'BACKTRACE: ' .			$_info['debug_backtrace'] . "\n\n";

		if ( isset( $_ci->db ) ) :

			$_extended	.= 'LAST KNOWN QUERY: ' .	$_ci->db->last_query() . "\n\n";

		endif;


		// --------------------------------------------------------------------------

		//	Prepare and send
		$_mime_boundary	= md5(uniqid(time()));
		$_to			= strtoupper( ENVIRONMENT ) != 'PRODUCTION' && EMAIL_OVERRIDE ? EMAIL_OVERRIDE : APP_DEVELOPER_EMAIL;

		//	Headers
		$_headers		 = 'From: ' . $_from_name . ' <' . $_from_email . '>' . "\r\n";
		$_headers		.= 'Reply-To: ' . $_reply_to . "\r\n";
		$_headers		.= 'X-Mailer: PHP/' . phpversion()  . "\r\n";
		$_headers		.= 'X-Priority: 1 (Highest)' . "\r\n";
		$_headers		.= 'X-Mailer: X-MSMail-Priority: High/' . "\r\n";
		$_headers		.= 'Importance: High' . "\r\n";
		$_headers		.= 'MIME-Version:1.0' . "\r\n";
		$_headers		.= 'Content-Type:multipart/mixed; boundary="' . $_mime_boundary . '"' . "\r\n\r\n";

		//	Message
		$_headers		.= '--' . $_mime_boundary . "\r\n";
		$_headers		.= 'Content-Type:text/html; charset="ISO-8859-1"' . "\r\n";
		$_headers		.= 'Content-Transfer-Encoding:7bit' . "\r\n\r\n";

		$_headers		.= '<html><head><style type="text/css">body { font:10pt Arial; }</style></head><body>' . str_replace( "\r", '', str_replace( "\n", '<br />', $message ) ) . '</body></html>' . "\r\n\r\n";

		//	Attachment
		$_headers .= '--' . $_mime_boundary . "\r\n";
		$_headers .= 'Content-Type:application/octet-stream; name="debugging-data.txt"' . "\r\n";
		$_headers .= 'Content-Transfer-Encoding:base64' . "\r\n";
		$_headers .= 'Content-Disposition:attachment; filename="debugging-data.txt"' . "\r\n";
		$_headers .= base64_encode( $_extended ) . "\r\n\r\n";

		// --------------------------------------------------------------------------

		//	Send!
		if ( ! empty( $_to ) ) :

			@mail( $_to, '!! ' . $subject . ' - ' . APP_NAME , '', $_headers );

		endif;
	}
}

/* End of file fatal_error_hook.php */
/* Location: ./application/hooks/fatal_error_hook.php */