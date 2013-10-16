<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pdf
{

	private $_ci;
	private $_dompdf;


	// --------------------------------------------------------------------------


	public function __construct()
	{
		$this->_ci =& get_instance();

		// --------------------------------------------------------------------------

		//	Load and configure DOMPDF
		define( 'DOMPDF_ENABLE_AUTOLOAD', FALSE );
		require_once FCPATH . '/vendor/dompdf/dompdf/dompdf_config.inc.php';

		$this->_dompdf = new DOMPDF();
	}


	// --------------------------------------------------------------------------


	public function load_view( $view, $data = array() )
	{
		$_html = $this->_ci->load->view( $view, $data, TRUE );
		$this->_dompdf->load_html( $_html );
	}


	// --------------------------------------------------------------------------


	public function __call( $method, $arguments = array() )
	{
		if ( method_exists( $this->_dompdf, $method ) ) :

			return call_user_func_array( array( $this->_dompdf, $method ), $arguments );

		else :

			throw new exception( 'Call to undefined method Pdf::' . $method . '()' );

		endif;
	}
}