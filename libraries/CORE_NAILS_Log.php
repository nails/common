<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Log extends CI_Log
{
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Ignore whatever the parent constructor says about whether logging is
		//	enabled or not. We'll work it out below.

		$this->_enabled = NULL;
	}

	// --------------------------------------------------------------------------

	public function write_log($level = 'error', $msg, $php_error = FALSE)
	{
		//	Ensure this is set correctly. Would use the constructor, however
		//	that is called before the pre_system hook (as the constructor of
		//	the hook class calls log_message() which in turn constructs this class.
		//	The docs LIE when theys ay only benchmark and hooks class are loaded)

		if ( defined( 'DEPLOY_LOG_DIR' ) ) :

			$this->_log_path	= DEPLOY_LOG_DIR;

			//	If we haven't already, check to see if DEPLOY_LOG_DIR is writeable

			if ( NULL == $this->_enabled ) :

				if ( is_writeable( $this->_log_path ) ) :

					//	Writeable!
					$this->_enabled = TRUE;

				else :

					//	Not writeable, disable logging and kick up a fuss
					$this->_enabled = FALSE;

					//	Send developer mail, but only once
					if ( ! defined( 'NAILS_LOG_ERROR_REPORTED' ) ) :

						if ( isset( $_SERVER['REQUEST_URI'] ) ) :

							$_uri = $_SERVER['REQUEST_URI'];

						else :

							//	Most likely on the CLI
							if ( isset( $_SERVER['argv'] ) ) :

								$_uri = 'CLI: ' . implode( ' ', $_SERVER['argv'] );

							else :

								$_uri = 'Unable to determine URI';

							endif;

						endif;

						$_message	= strtoupper($level).' '.((strtoupper($level) == 'INFO') ? ' -' : '-').' '.date($this->_date_fmt). ' --> '.$msg."\n";
						$_appname	= defined( 'APP_NAME' ) ? APP_NAME : '[Could not determine app name]';

						$_subject	= 'Log folders are not writeable on ' . $_appname;
						$_message	= 'I just tried to write to the log folder for ' . $_appname . ' and found them not to be writeable.' . "\n";
						$_message	.= '' . "\n";
						$_message	.= 'Get this fixed ASAP - I\'ll bug you every time this happens.' . "\n";
						$_message	.= '' . "\n";
						$_message	.= 'FYI, the entry was:' . "\n";
						$_message	.= '' . "\n";
						$_message	.= $msg . "\n";
						$_message	.= '' . "\n";
						$_message	.= 'The calling URI was:' . "\n";
						$_message	.= '' . "\n";
						$_message	.= $_uri . "\n";
						$_message	.= '' . "\n";
						$_message	.= 'The path was:' . "\n";
						$_message	.= '' . "\n";
						$_message	.= $this->_log_path . "\n";
						$_message	.= '' . "\n";
						$_message	.= 'PHP SAPI Name:' . "\n";
						$_message	.= '' . "\n";
						$_message	.= php_sapi_name() . "\n";
						$_message	.= '' . "\n";
						$_message	.= 'PHP Debug Backtrace:' . "\n";
						$_message	.= '' . "\n";
						$_message	.= serialize( debug_backtrace() ) . "\n";

						//	Set from details
						$_from_email = 'root@' . gethostname();

						if ( function_exists( 'app_setting' ) ) :

							$_from_name = app_setting( 'from_name', 'email' );

							if ( empty( $_from_name ) ) :

								$_from_name = 'Log Error Reporter';

							endif;

							$_reply_to = app_setting( 'from_email', 'email' );

							if ( empty( $_reply_to ) ) :

								$_reply_to = 'Log Error Reporter';

							endif;

						else :

							$_from_name	= 'Log Error Reporter';
							$_reply_to	= $_from_email;

						endif;

						$_to			= defined( 'ENVIRONMENT' ) && strtoupper( ENVIRONMENT ) != 'PRODUCTION' && defined( 'EMAIL_OVERRIDE' ) && EMAIL_OVERRIDE ? EMAIL_OVERRIDE : APP_DEVELOPER_EMAIL;
						$_headers		= 'From: ' . $_from_name . ' <' . $_from_email . '>' . "\r\n" .
										  'Reply-To: ' . $_reply_to . "\r\n" .
										  'X-Mailer: PHP/' . phpversion()  . "\r\n" .
										  'X-Priority: 1 (Highest)' . "\r\n" .
										  'X-Mailer: X-MSMail-Priority: High/' . "\r\n" .
										  'Importance: High';

						if ( ! empty( $_to ) ) :

							@mail( $_to, '!! ' . $_subject, $_message, $_headers );

						endif;

						define( 'NAILS_LOG_ERROR_REPORTED', TRUE );

					endif;

				endif;

			endif;

		else :

			//	Don't bother writing as we don't know where to write.
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Test Log folder, but only if the error level is to be captured
		$level = strtoupper($level);

		if ( ! isset($this->_levels[$level]) OR ($this->_levels[$level] > $this->_threshold)) :

			return FALSE;

		endif;

		parent::write_log( $level, $msg, $php_error );
	}
}

/* End of file CORE_NAILS_Log.php */
/* Location: ./application/core/CORE_NAILS_Log.php */