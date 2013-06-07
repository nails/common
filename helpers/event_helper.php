<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists( 'create_event' ) )
{
	function create_event( $type, $created_by = NULL, $level = 0, $interested_parties = NULL, $vars = NULL, $ref = NULL, $_recorded = NULL )
	{
		$_ci =& get_instance();
		$_ci->load->library( 'event' );
		return $_ci->event->create( $type, $created_by, $level, $interested_parties, $vars, $ref );
	}
}

/* End of file event_helper.php */
/* Location: ./application/helpers/event_helper.php */