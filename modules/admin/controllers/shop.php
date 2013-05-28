<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Admin : Shop
*
* Description:	Shop Manager
* 
*/

require_once NAILS_PATH . 'modules/admin/controllers/_admin.php';

class Shop extends Admin_Controller {

	/**
	 * Announces this module's details to those in the know.
	 *
	 * @access static
	 * @param none
	 * @return void
	 **/
	static function announce()
	{
		if ( ! module_is_enabled( 'shop' ) ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$d = new stdClass();
		
		// --------------------------------------------------------------------------
		
		//	Configurations
		$d->name				= 'Shop';					//	Display name.
		
		// --------------------------------------------------------------------------
		
		//	Navigation options
		$d->funcs				= array();
		$d->funcs['index']		= 'Manage Inventory';				//	Sub-nav function.
		$d->funcs['orders']		= 'Manage Orders';					//	Sub-nav function.
		$d->funcs['vouchers']	= 'Manage Vouchers';				//	Sub-nav function.
		
		// --------------------------------------------------------------------------
		
		//	Only announce the controller if the user has permission to know about it
		return self::_can_access( $d, __FILE__ );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Returns an array of notifications for various methods
	 *
	 * @access	static
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	static function notifications()
	{
		$_ci =& get_instance();
		$_notifications = array();
		
		// --------------------------------------------------------------------------
		
		// $_notifications['index']			= array();
		// $_notifications['index']['title']	= 'Active Products';
		// $_notifications['index']['value']	= 13;
		
		// $_notifications['orders']			= array();
		// $_notifications['orders']['type']	= 'alert';
		// $_notifications['orders']['title']	= 'Unfulfilled orders';
		// $_notifications['orders']['value']	= 13;
		
		// --------------------------------------------------------------------------
		
		return $_notifications;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Manage the inventory
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function index()
	{
		//	Set method info
		$this->data['page']->title = 'Manage Inventory';
		
		// --------------------------------------------------------------------------
		
		//	Fetch inventory
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'admin/shop/inventory/index',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Manage orders
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function orders()
	{
		//	Set method info
		$this->data['page']->title = 'Manage Orders';
		
		// --------------------------------------------------------------------------
		
		//	Fetch orders
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'admin/shop/orders/index',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Manage vouchers
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function vouchers()
	{
		//	Set method info
		$this->data['page']->title = 'Manage Vouchers';
		
		// --------------------------------------------------------------------------
		
		//	Fetch orders
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'admin/shop/vouchers/index',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Edit an existing product
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function create()
	{
		//	Set method info
		$this->data['page']->title = 'Add Product';
		
		// --------------------------------------------------------------------------
		
		//	Process POST
		if ( $this->input->post() ) :
		
			//	TODO
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'admin/shop/inventory/create',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Edit an existing product
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function edit()
	{
		//	Set method info
		$this->data['page']->title = 'Edit Product ()';
		
		// --------------------------------------------------------------------------
		
		//	Process POST
		if ( $this->input->post() ) :
		
			//	TODO
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'admin/shop/inventory/edit',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
}


/* End of file universities.php */
/* Location: ./application/modules/admin/controllers/universities.php */