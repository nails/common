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
	 * Constructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function __construct()
	{
		parent::__construct();
		
		// --------------------------------------------------------------------------
		
		//	Defaults defaults
		$this->shop_group		= FALSE;
		$this->shop_where		= array();
		$this->shop_actions		= array();
		$this->shop_sortfields	= array();
		
		// --------------------------------------------------------------------------
		
		$this->shop_sortfields[] = array( 'label' => 'ID',				'col' => 'o.id' );
		$this->shop_sortfields[] = array( 'label' => 'Date Placed',		'col' => 'o.created' );
		$this->shop_sortfields[] = array( 'label' => 'Last Modified',	'col' => 'o.modified' );

		// --------------------------------------------------------------------------

		//	Load the helper and base model
		$this->load->helper( 'shop' );
		$this->load->model( 'shop/shop_model', 'shop' );
		$this->load->model( 'shop/shop_currency_model', 'currency' );

		
		// --------------------------------------------------------------------------
		
		//	Set the currency constants
		$_base = $this->shop->get_base_currency();
		
		//	Shop's base currency (i.e what the products are listed in etc)
		define( 'SHOP_BASE_CURRENCY_SYMBOL',	$_base->symbol );
		define( 'SHOP_BASE_CURRENCY_PRECISION',	$_base->decimal_precision );
		define( 'SHOP_BASE_CURRENCY_CODE',		$_base->code );
		define( 'SHOP_BASE_CURRENCY_ID',		$_base->id );
		
		//	User's preferred currency
		//	TODO: Same as default just now
		define( 'SHOP_USER_CURRENCY_SYMBOL',	$_base->symbol );
		define( 'SHOP_USER_CURRENCY_PRECISION',	$_base->decimal_precision );
		define( 'SHOP_USER_CURRENCY_CODE',		$_base->code );
		define( 'SHOP_USER_CURRENCY_ID',		$_base->id );
		
		//	Exchange rate betweent the two currencies
		//	TODO: Hardcoded GBP just now
		define( 'SHOP_USER_CURRENCY_EXCHANGE',	1 );
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

		//	Searching, sorting, ordering and paginating.
		$_hash = 'search_' . md5( uri_string() ) . '_';
		
		if ( $this->input->get( 'reset' ) ) :
		
			$this->session->unset_userdata( $_hash . 'per_page' );
			$this->session->unset_userdata( $_hash . 'sort' );
			$this->session->unset_userdata( $_hash . 'order' );
		
		endif;
		
		$_default_per_page	= $this->session->userdata( $_hash . 'per_page' ) ? $this->session->userdata( $_hash . 'per_page' ) : 50;
		$_default_sort		= $this->session->userdata( $_hash . 'sort' ) ? 	$this->session->userdata( $_hash . 'sort' ) : 'o.id';
		$_default_order		= $this->session->userdata( $_hash . 'order' ) ? 	$this->session->userdata( $_hash . 'order' ) : 'ASC';
		
		//	Define vars
		$_search			= array( 'keywords' => $this->input->get( 'search' ), 'columns' => array() );
		
		foreach ( $this->shop_sortfields AS $field ) :
		
			$_search['columns'][strtolower( $field['label'] )] = $field['col'];
		
		endforeach;
		
		$_limit		= array(
						$this->input->get( 'per_page' ) ? $this->input->get( 'per_page' ) : $_default_per_page,
						$this->input->get( 'offset' ) ? $this->input->get( 'offset' ) : 0
					);
		$_order		= array(
						$this->input->get( 'sort' ) ? $this->input->get( 'sort' ) : $_default_sort,
						$this->input->get( 'order' ) ? $this->input->get( 'order' ) : $_default_order
					);
					
		//	Set sorting and ordering info in session data so it's remembered for when user returns
		$this->session->set_userdata( $_hash . 'per_page', $_limit[0] );
		$this->session->set_userdata( $_hash . 'sort', $_order[0] );
		$this->session->set_userdata( $_hash . 'order', $_order[1] );
		
		//	Set values for the page
		$this->data['search']			= new stdClass();
		$this->data['search']->per_page	= $_limit[0];
		$this->data['search']->sort		= $_order[0];
		$this->data['search']->order	= $_order[1];
		$this->data['search']->show		= $this->input->get( 'show' );

		// --------------------------------------------------------------------------

		//	Prepare the where
		if ( $this->data['search']->show ) :

			$_where = '( `o`.`status` IN (';

				$_statuses = array_keys( $this->data['search']->show );
				foreach ( $_statuses AS &$stat ) :

					$stat = strtoupper( $stat );

				endforeach;
				$_where .= "'" . implode( "','", $_statuses ) . "'";

			$_where .= '))';

		else :

			$_where = NULL;

		endif;

		// --------------------------------------------------------------------------
		
		//	Pass any extra data to the view
		$this->data['actions']		= $this->shop_actions;
		$this->data['sortfields']	= $this->shop_sortfields;
		
		// --------------------------------------------------------------------------
		
		//	Fetch orders
		$this->load->model( 'shop/shop_order_model', 'order' );

		$this->data['orders']		= new stdClass();
		$this->data['orders']->data = $this->order->get_all( $_order, $_limit, $_where, $_search );

		//	Work out pagination
		$this->data['orders']->pagination					= new stdClass();
		$this->data['orders']->pagination->total_results	= $this->order->count_orders( $_where, $_search );
		
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