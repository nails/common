<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Log extends CI_Log
{
	public function write_log($level = 'error', $msg, $php_error = FALSE)
	{
		if ( ENVIRONMENT == 'production' ) :
		
			//	Test Log folder
			if ( ! is_writeable( $this->_log_path ) ) :
			
				//	Kick up a fuss and tell Shed
				$message	= strtoupper($level).' '.((strtoupper($level) == 'INFO') ? ' -' : '-').' '.date($this->_date_fmt). ' --> '.$msg."\n";
				
				$_to		= 'hello@shedcollective.org';
				$_subject	= 'Log folders are not writeable on ' . APP_NAME;
				$_message	= 'I just tried to write to the log folder for ' . APP_NAME . ' and found them not to be writeable.' . "\n";
				$_message	.= '' . "\n";
				$_message	.= 'Get this fixed ASAP - I\'ll bug you every time this happens.' . "\n";
				$_message	.= '' . "\n";
				$_message	.= 'FYI, the log entry was:' . "\n";
				$_message	.= $message;
				
				$_headers = 'From: ' . APP_EMAIL_FROM_NAME . ' <' . 'root@' . gethostname() . '>' . "\r\n" .
							'Reply-To: ' . APP_EMAIL_FROM_EMAIL . "\r\n" .
							'X-Mailer: PHP/' . phpversion()  . "\r\n" .
							'X-Priority: 1 (Highest)' . "\r\n" .
							'X-Mailer: X-MSMail-Priority: High/' . "\r\n" .
							'Importance: High';
				
				@mail( $_to, $_subject , $_message, $_headers );
			
			else :
			
				parent::write_log( $level, $msg, $php_error );
			
			endif;
		
		endif;
	}
}

/* End of file CORE_NAILS_Log.php */
/* Location: ./application/core/CORE_NAILS_Log.php */