<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		NALS_API_Controller
 *
 * Description:	This controller executes various bits of common admin API functionality
 *
 **/


class NAILS_API_Controller extends NAILS_Controller
{
	/**
	 *	Execute common functionality
	 *
	 *	@access	public
	 *	@param	none
	 *	@return void
	 *
	 **/
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Load language file
		$this->lang->load( 'api' );
	}

	// --------------------------------------------------------------------------


	/**
	 *	Take the input and spit it out as JSON
	 *
	 *	@access	public
	 *	@param	none
	 *	@return void
	 *
	 **/
	protected function _out( $out = array(), $format = 'JSON', $send_header = TRUE )
	{
		//	Set JSON headers
		$this->output->set_content_type( 'application/json' );
		$this->output->set_header( 'Cache-Control: no-store, no-cache, must-revalidate' );
		$this->output->set_header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
		$this->output->set_header( 'Pragma: no-cache' );

		// --------------------------------------------------------------------------

		//	Send the correct status header, default to 200 OK
		if ( isset( $out['status'] ) ) :

			if ( $send_header ) :

				switch ( $out['status'] ) :

					case 400 :	$this->output->set_header( $this->input->server( 'SERVER_PROTOCOL' ) . ' 400 Bad Request' );			break;
					case 401 :	$this->output->set_header( $this->input->server( 'SERVER_PROTOCOL' ) . ' 401 Unauthorized' );			break;
					case 404 :	$this->output->set_header( $this->input->server( 'SERVER_PROTOCOL' ) . ' 404 Not Found' );				break;
					case 500 :	$this->output->set_header( $this->input->server( 'SERVER_PROTOCOL' ) . ' 500 Internal Server Error' );	break;
					default  :	$this->output->set_header( $this->input->server( 'SERVER_PROTOCOL' ) . ' 200 OK' );						break;

				endswitch;

			endif;

		else:

			$out['status'] = 200;

			if ( $send_header ) :

				$this->output->set_header( $this->input->server( 'SERVER_PROTOCOL' ) . ' 200 OK' );

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Output content
		$this->output->set_output( json_encode( $out ) );
	}


	// --------------------------------------------------------------------------


	/**
	 *	Take the input and spit it out as JSON
	 *
	 *	@access	public
	 *	@param	none
	 *	@return void
	 *
	 **/
	public function _remap( $method )
	{
		if ( method_exists( $this, $method ) ) :

			$this->{$method}();

		else :

			$this->_method_not_found( $method );

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 *	Output JSON for when a method is not found (or enabled)
	 *
	 *	@access	public
	 *	@param	none
	 *	@return void
	 *
	 **/
	protected function _method_not_found( $method )
	{
		 $this->_out( array( 'status' => 400, 'error' => lang( 'not_valid_method', $method ) ) );

		 //	Careful now, this might break in future updates of CI
		 echo $this->output->_display();
		 exit(0);
	}
}

/* End of file _api.php */
/* Location: ./application/modules/api/controllers/_api.php */