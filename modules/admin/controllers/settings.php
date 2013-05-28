<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Admin - Settings
 *
 * Description:	A holder for all site settings
 * 
 **/


//	Include Admin_Controller; executes common admin functionality.
require_once '_admin.php';

/**
 * OVERLOADING NAILS'S ADMIN MODULES
 * 
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 * 
 **/
 
class NAILS_Settings extends Admin_Controller {
	
	
	/**
	 * Announces this module's details to anyone who asks.
	 *
	 * @access	static
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	static function announce()
	{
		$d = new stdClass();
		
		// --------------------------------------------------------------------------
		
		//	Load the laguage file
		get_instance()->lang->load( 'admin_settings', RENDER_LANG );
		
		// --------------------------------------------------------------------------
		
		//	Configurations
		$d->name = lang( 'settings_module_name' );
		
		// --------------------------------------------------------------------------
		
		//	Navigation options
		$d->funcs = array();
		
		if ( module_is_enabled( 'blog' ) ) :

			$d->funcs['blog']	= lang( 'settings_nav_blog' );

		endif;

		if ( module_is_enabled( 'shop' ) ) :

			$d->funcs['shop']	= lang( 'settings_nav_shop' );

		endif;

		// --------------------------------------------------------------------------

		//	If there are no enabled modules which have settings then don't bother
		//	enableing the sidebar item

		if ( ! $d->funcs ) :

			return FALSE;

		endif;
		
		// --------------------------------------------------------------------------
		
		//	Only announce the controller if the user has permisison to know about it
		return self::_can_access( $d, __FILE__ );
	}


	// --------------------------------------------------------------------------


	/**
	 * Configure the blog
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function blog()
	{
		if ( ! module_is_enabled( 'blog' ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Set method info
		$this->data['page']->title = lang( 'settings_blog_title' );

		// --------------------------------------------------------------------------

		//	Load models
		$this->load->model( 'blog/blog_model', 'blog' );
		
		// --------------------------------------------------------------------------

		//	Process POST
		if ( $this->input->post() ) :
		
			switch ( $this->input->post( 'update' ) ) :

				case 'settings' :

					$this->_blog_update_settings();

				break;

				case 'sidebar' :

					$this->_blog_update_sidebar();

				break;

				// --------------------------------------------------------------------------

				default :

					$this->data['error'] = '<strong>Sorry,</strong> I can\'t determine what type of update you are trying to perform.';

				break;

			endswitch;
		
		endif;

		// --------------------------------------------------------------------------

		//	Get data
		$this->data['settings'] = $this->blog->settings( NULL, TRUE );
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'admin/settings/blog',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}


	// --------------------------------------------------------------------------


	private function _blog_update_settings()
	{
		//	Prepare update
		$_settings							= array();
		$_settings['blog_url']				= $this->input->post( 'blog_url' );
		$_settings['categories_enabled']	= (bool) $this->input->post( 'categories_enabled' );
		$_settings['tags_enabled']			= (bool) $this->input->post( 'tags_enabled' );

		// --------------------------------------------------------------------------

		//	Sanitize blog url
		$_settings['blog_url'] .= substr( $_settings['blog_url'], -1 ) != '/' ? '/' : '';

		// --------------------------------------------------------------------------

		//	Save
		if ( $this->blog->set_settings( $_settings ) ) :

			$this->data['success'] = '<strong>Success!</strong> Blog settings have been saved.';

		else :

			$this->data['error'] = '<strong>Sorry,</strong> there was a problem saving settings.';

		endif;
	}


	// --------------------------------------------------------------------------


	private function _blog_update_sidebar()
	{
		//	Prepare update
		$_settings						= array();
		$_settings['sidebar_enabled']	= (bool) $this->input->post( 'sidebar_enabled' );
		$_settings['sidebar_position']	= $this->input->post( 'sidebar_position' );

		// --------------------------------------------------------------------------

		//	Save
		if ( $this->blog->set_settings( $_settings ) ) :

			$this->data['success'] = '<strong>Success!</strong> Sidebar settings have been saved.';

		else :

			$this->data['error'] = '<strong>Sorry,</strong> there was a problem saving settings.';

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Configure the shop
	 *
	 * @access public
	 * @param none
	 * @return void
	 **/
	public function shop()
	{
		if ( ! module_is_enabled( 'shop' ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Set method info
		$this->data['page']->title = lang( 'settings_shop_title' );

		// --------------------------------------------------------------------------

		//	Load models
		$this->load->model( 'shop/shop_model', 'shop' );
		$this->load->model( 'shop/shop_payment_gateway_model', 'payment_gateway' );
		$this->load->model( 'shop/shop_currency_model',	'currency' );
		
		// --------------------------------------------------------------------------

		//	Process POST
		if ( $this->input->post() ) :
		
			switch ( $this->input->post( 'update' ) ) :

				case 'settings' :

					$this->_shop_update_settings();

				break;

				case 'paymentgateways' :

					$this->_shop_update_paymentgateways();

				break;

				case 'currencies' :

					$this->_shop_update_currencies();

				break;

				// --------------------------------------------------------------------------

				default :

					$this->data['error'] = '<strong>Sorry,</strong> I can\'t determine what type of update you are trying to perform.';

				break;

			endswitch;
		
		endif;

		// --------------------------------------------------------------------------

		//	Get data
		$this->data['settings'] = $this->shop->settings( NULL, TRUE );

		if ( $this->user->is_superuser() ) :

			$this->data['payment_gateways'] = $this->payment_gateway->get_all();

		else :

			$this->data['payment_gateways'] = $this->payment_gateway->get_all_supported();

		endif;

		$this->data['currencies_all_flat']		= $this->currency->get_all( FALSE );
		$this->data['currencies_active_flat']	= $this->currency->get_all_flat();
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'admin/settings/shop',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}


	// --------------------------------------------------------------------------


	private function _shop_update_settings()
	{
		//	Prepare update
		$_settings								= array();
		$_settings['notify_order']				= $this->input->post( 'notify_order' );
		$_settings['shop_url']					= $this->input->post( 'shop_url' );
		$_settings['free_shipping_threshold']	= (float) $this->input->post( 'free_shipping_threshold' );
		$_settings['invoice_company']			= $this->input->post( 'invoice_company' );
		$_settings['invoice_address']			= $this->input->post( 'invoice_address' );
		$_settings['invoice_vat_no']			= $this->input->post( 'invoice_vat_no' );
		$_settings['invoice_company_no']		= $this->input->post( 'invoice_company_no' );

		// --------------------------------------------------------------------------

		//	Sanitize shop url
		$_settings['shop_url'] .= substr( $_settings['shop_url'], -1 ) != '/' ? '/' : '';

		// --------------------------------------------------------------------------

		if ( $this->shop->set_settings( $_settings ) ) :

			$this->data['success'] = '<strong>Success!</strong> Store settings have been saved.';

		else :

			$this->data['error'] = '<strong>Sorry,</strong> there was a problem saving settings.';

		endif;
	}

	// --------------------------------------------------------------------------


	private function _shop_update_paymentgateways()
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


	private function _shop_update_currencies()
	{
		//	Prepare update
		$_settings								= array();
		$_settings['base_currency']				= (int) $this->input->post( 'base_currency' );

		// --------------------------------------------------------------------------

		if ( $this->shop->set_settings( $_settings ) ) :

			$this->data['success'] = '<strong>Success!</strong> Base currency has been saved.';

			//	Save the active currencies
			$_where_in = array( $_settings['base_currency'] );

			foreach ( $this->input->post( 'active_currencies' ) AS $id ) :

				$_where_in[] = $id;

			endforeach;
			
			if ( $this->currency->set_active_currencies( $_where_in ) ) :

				$this->data['success'] = '<strong>Success!</strong> Currency settings have been updated.';

			else :

				$this->data['error'] = '<strong>Sorry,</strong> an error occurred while setting supported currencies.';

			endif;

		else :

			$this->data['error'] = '<strong>Sorry,</strong> there was a problem saving the base currency.';

		endif;
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS'S ADMIN MODULES
 * 
 * The following block of code makes it simple to extend one of the core admin
 * controllers. Some might argue it's a little hacky but it's a simple 'fix'
 * which negates the need to massively extend the CodeIgniter Loader class
 * even further (in all honesty I just can't face understanding the whole
 * Loader class well enough to change it 'properly').
 * 
 * Here's how it works:
 * 
 * CodeIgniter instanciates a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclre class X' errors
 * and if we call our overloading class something else it will never get instanciated.
 * 
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instanciated et voila.
 * 
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION_CLASSNAME
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 * 
 **/
 
if ( ! defined( 'NAILS_ALLOW_EXTENSION_SETTINGS' ) ) :

	class Settings extends NAILS_Settings
	{
	}

endif;


/* End of file faq.php */
/* Location: ./application/modules/admin/controllers/faq.php */