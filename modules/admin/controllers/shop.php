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
		$d = new stdClass();
		
		// --------------------------------------------------------------------------
		
		//	Configurations
		$d->name				= 'Shop';					//	Display name.
		
		// --------------------------------------------------------------------------
		
		//	Navigation options
		$d->funcs['index']		= 'Manage Inventory';				//	Sub-nav function.
		$d->funcs['orders']		= 'Manage Orders';					//	Sub-nav function.
		$d->funcs['vouchers']	= 'Manage Vouchers';				//	Sub-nav function.
		$d->funcs['settings']	= 'Shop Settings';					//	Sub-nav function.
		
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
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Configure the shop
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function settings()
	{
		//	Set method info
		$this->data['page']->title = 'Shop Settings';

		// --------------------------------------------------------------------------

		//	Load models
		$this->load->model( 'shop/shop_model', 'shop' );
		$this->load->model( 'shop/shop_payment_gateway_model', 'payment_gateway' );
		
		// --------------------------------------------------------------------------

		//	Process POST
		if ( $this->input->post() ) :
		
			switch ( $this->input->post( 'update' ) ) :

				case 'settings' :

					$this->_settings_update_settings();

				break;

				case 'paymentgateways' :

					$this->_settings_update_paymentgateways();

				break;

				case 'currencies' :

					$this->_settings_update_currencies();

				break;

				// --------------------------------------------------------------------------

				default :

					$this->data['error'] = '<strong>Sorry,</strong> I can\'t determine what type of update you are trying to perform.';

				break;

			endswitch;
		
		endif;

		// --------------------------------------------------------------------------

		//	Get data
		$this->data['settings'] = $this->shop->settings();

		if ( $this->user->is_superuser() ) :

			$this->data['payment_gateways'] = $this->payment_gateway->get_all();

		else :

			$this->data['payment_gateways'] = $this->payment_gateway->get_all_supported();

		endif;
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'admin/shop/settings',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}


	// --------------------------------------------------------------------------


	private function _settings_update_settings()
	{
		//	Prepare update
		$_settings					= array();
		$_settings['notify_order']	= $this->input->post( 'notify_order' );

		if ( $this->shop->set_settings( $_settings ) ) :

			$this->data['success'] = '<strong>Success!</strong> Store settings have been saved.';

		else :

			$this->data['error'] = '<strong>Sorry,</strong> there was a problem saving settings.';

		endif;
	}

	// --------------------------------------------------------------------------


	private function _settings_update_paymentgateways()
	{
		//	Prepare update
		foreach( $this->input->post( 'paymentgateway' ) AS $id => $values ) :

			$_data						= new stdClass();

			if ( $this->user->is_superuser() ) :

				$_data->enabled				= (bool) $values['enabled'];
				$_data->sandbox_account_id	= $values['sandbox_account_id'];
				$_data->sandbox_api_key		= $values['sandbox_api_key'];
				$_data->sandbox_api_secret	= $values['sandbox_api_secret'];

			endif;
			$_data->account_id			= $values['account_id'];
			$_data->api_key				= $values['api_key'];
			$_data->api_secret			= $values['api_secret'];

			$this->payment_gateway->update( $id, $_data );

		endforeach;

		$this->data['success'] = '<strong>Success!</strong> Payment Gateway settings have been saved.';
	}


	// --------------------------------------------------------------------------


	private function _settings_update_currencies()
	{
		//	TODO
	}
}


/* End of file universities.php */
/* Location: ./application/modules/admin/controllers/universities.php */