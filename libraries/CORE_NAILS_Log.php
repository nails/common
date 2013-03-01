<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Log extends CI_Log
{
	public function write_log($level = 'error', $msg, $php_error = FALSE)
	{
		if ( ENVIRONMENT == 'production' ) :
		
			//	Test Log folder, but only if the error level is to be captured
			$level = strtoupper($level);
	
			if ( ! isset($this->_levels[$level]) OR ($this->_levels[$level] > $this->_threshold)) :
			
				return FALSE;
				
			endif;
			
			if ( ! is_writeable( $this->_log_path ) ) :
			
				//	Kick up a fuss and tell Shed
				$message	= strtoupper($level).' '.((strtoupper($level) == 'INFO') ? ' -' : '-').' '.date($this->_date_fmt). ' --> '.$msg."\n";
				
				$_subject	= 'Log folders are not writeable on ' . APP_NAME;
				$_message	= 'I just tried to write to the log folder for ' . APP_NAME . ' and found them not to be writeable.' . "\n";
				$_message	.= '' . "\n";
				$_message	.= 'Get this fixed ASAP - I\'ll bug you every time this happens.' . "\n";
				$_message	.= '' . "\n";
				$_message	.= 'FYI, the log folder was ' . $this->_log_path . ' and the entry was:' . "\n";
				$_message	.= $message;
				
				send_developer_mail( $_subject , $_message );
			
			else :
			
				parent::write_log( $level, $msg, $php_error );
			
			endif;
		
		endif;
	}
}

/* End of file CORE_NAILS_Log.php */
/* Location: ./application/core/CORE_NAILS_Log.php */