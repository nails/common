<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists( '_LOG' ) )
{
	function _LOG( $line = '' )
	{
		return get_instance()->logger->line( $line );
	}
}

if ( ! function_exists( '_LOG_FILE' ) )
{
	function _LOG_FILE( $file = NULL )
	{
		return get_instance()->logger->set_file( $file );
	}
}

/* End of file log_helper.php */
/* Location: ./helpers/log_helper.php */