<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Proxy extends NAILS_Controller
{
	public function index()
	{
		$_url = preg_replace( '#nails_assets/proxy/?#', NAILS_URL, uri_string() );
		$this->load->library( 'proxylib', $_url );
		$this->proxylib->go();
	}


	public function _remap()
	{
		$this->index();
	}
}




