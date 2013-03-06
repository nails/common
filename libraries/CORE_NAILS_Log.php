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
				
				if ( isset( $_SERVER['REQUEST_URI'] ) ) :
				
					$_uri = $_SERVER['REQUEST_URI'];
				
				else :
				
					//	Most likely on the CLI
					if ( php_sapi_name() == 'cli' ) :
					
						$_uri = 'CLI: index.php ' . implode( ' ', $_SERVER['argv'] );
					
					else :
					
						$_uri = 'Unable to determine URI';
					
					endif;
				
				endif;
				
				$message	= strtoupper($level).' '.((strtoupper($level) == 'INFO') ? ' -' : '-').' '.date($this->_date_fmt). ' --> '.$msg."\n";
				
				$_subject	= '!! Log folders are not writeable on ' . APP_NAME;
				$_message	= 'I just tried to write to the log folder for ' . APP_NAME . ' and found them not to be writeable.' . "\n";
				$_message	.= '' . "\n";
				$_message	.= 'Get this fixed ASAP - I\'ll bug you every time this happens.' . "\n";
				$_message	.= '' . "\n";
				$_message	.= 'FYI, the entry was:' . "\n";
				$_message	.= '' . "\n";
				$_message	.= $message . "\n";
				$_message	.= '' . "\n";
				$_message	.= 'The calling URI was:' . "\n";
				$_message	.= '' . "\n";
				$_message	.= $_uri . "\n";
				
				send_developer_mail( $_subject , $_message );
			
			else :
			
				parent::write_log( $level, $msg, $php_error );
			
			endif;
		
		endif;
	}
}

/* End of file CORE_NAILS_Log.php */
/* Location: ./application/core/CORE_NAILS_Log.php */