<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Admin: Settings
 * Description:	A holder for all site settings
 *
 **/

//	Include Admin_Controller; executes common admin functionality.
require_once '_admin.php';

/**
 * OVERLOADING NAILS' ADMIN MODULES
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Settings extends NAILS_Admin_Controller
{


	/**
	 * Announces this module's details to anyone who asks.
	 *
	 * @access	static
	 * @param	none
	 * @return	void
	 **/
	static function announce()
	{
		$d = new stdClass();

		// --------------------------------------------------------------------------

		//	Load the laguage file
		get_instance()->lang->load( 'admin_settings', RENDER_LANG_SLUG );

		// --------------------------------------------------------------------------

		//	Configurations
		$d->name = lang( 'settings_module_name' );

		// --------------------------------------------------------------------------

		//	Navigation options
		$d->funcs = array();
		$d->funcs['site']	= lang( 'settings_nav_site' );

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
	public function site()
	{
		//	Set method info
		$this->data['page']->title = lang( 'settings_site_title' );

		// --------------------------------------------------------------------------

		//	Load models
		$this->load->model( 'system/site_model', 'site' );

		// --------------------------------------------------------------------------

		//	Process POST
		if ( $this->input->post() ) :

			switch ( $this->input->post( 'update' ) ) :

				case 'analytics' :

					$this->_site_update_analytics();

				break;

				// --------------------------------------------------------------------------

				default :

					$this->data['error'] = '<strong>Sorry,</strong> I can\'t determine what type of update you are trying to perform.';

				break;

			endswitch;

		endif;

		// --------------------------------------------------------------------------

		//	Get data
		$this->data['settings'] = $this->site->get_settings( NULL, TRUE );

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'admin/settings/site',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}


	// --------------------------------------------------------------------------


	protected function _site_update_analytics()
	{
		//	Prepare update
		$_settings								= array();
		$_settings['google_analytics_account']	= $this->input->post( 'google_analytics_account' );

		// --------------------------------------------------------------------------

		//	Save
		if ( $this->site->set_settings( $_settings ) ) :

			$this->data['success'] = '<strong>Success!</strong> Site settings have been saved.';

		else :

			$this->data['error'] = '<strong>Sorry,</strong> there was a problem saving settings.';

		endif;
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

				// --------------------------------------------------------------------------

				case 'commenting' :

					$this->_blog_update_commenting();

				break;

				// --------------------------------------------------------------------------

				case 'social' :

					$this->_blog_update_social();

				break;

				// --------------------------------------------------------------------------

				case 'blog_sidebar' :

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
		$this->data['settings'] = $this->blog->get_settings( NULL, TRUE );

		// --------------------------------------------------------------------------

		//	Load assets
		$this->asset->load( 'nails.admin.blog.settings.min.js', TRUE );

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'admin/settings/blog',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}


	// --------------------------------------------------------------------------


	protected function _blog_update_settings()
	{
		//	Prepare update
		$_settings							= array();
		$_settings['blog_url']				= $this->input->post( 'blog_url' );
		$_settings['use_excerpts']			= (bool) $this->input->post( 'use_excerpts' );
		$_settings['categories_enabled']	= (bool) $this->input->post( 'categories_enabled' );
		$_settings['tags_enabled']			= (bool) $this->input->post( 'tags_enabled' );
		$_settings['rss_enabled']			= (bool) $this->input->post( 'rss_enabled' );

		// --------------------------------------------------------------------------

		//	Sanitize blog url
		$_settings['blog_url'] .= substr( $_settings['blog_url'], -1 ) != '/' ? '/' : '';

		// --------------------------------------------------------------------------

		//	Save
		if ( $this->blog->set_settings( $_settings ) ) :

			$this->data['success'] = '<strong>Success!</strong> Blog settings have been saved.';

			$this->load->model( 'system/routes_model' );
			if ( ! $this->routes_model->update( 'shop' ) ) :

				$this->data['warning'] = '<strong>Warning:</strong> while the blog settings were updated, the routes file could not be updated. The blog may not behave as expected,';

			endif;

		else :

			$this->data['error'] = '<strong>Sorry,</strong> there was a problem saving settings.';

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _blog_update_commenting()
	{
		//	Prepare update
		$_settings								= array();
		$_settings['comments_enabled']			= $this->input->post( 'comments_enabled' );
		$_settings['comments_engine']			= $this->input->post( 'comments_engine' );
		$_settings['comments_disqus_shortname']	= $this->input->post( 'comments_disqus_shortname' );

		// --------------------------------------------------------------------------

		//	Save
		if ( $this->blog->set_settings( $_settings ) ) :

			$this->data['success'] = '<strong>Success!</strong> Blog commenting settings have been saved.';

		else :

			$this->data['error'] = '<strong>Sorry,</strong> there was a problem saving commenting settings.';

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _blog_update_social()
	{
		//	Prepare update
		$_settings								= array();
		$_settings['social_facebook_enabled']	= (bool) $this->input->post( 'social_facebook_enabled' );
		$_settings['social_twitter_enabled']	= (bool) $this->input->post( 'social_twitter_enabled' );
		$_settings['social_twitter_via']		= $this->input->post( 'social_twitter_via' );
		$_settings['social_googleplus_enabled']	= (bool) $this->input->post( 'social_googleplus_enabled' );
		$_settings['social_pinterest_enabled']	= (bool) $this->input->post( 'social_pinterest_enabled' );
		$_settings['social_skin']				= $this->input->post( 'social_skin' );
		$_settings['social_layout']				= $this->input->post( 'social_layout' );
		$_settings['social_layout_single_text']	= $this->input->post( 'social_layout_single_text' );
		$_settings['social_counters']			= (bool) $this->input->post( 'social_counters' );

		//	If any of the above are enabled, then social is enabled.
		$_settings['social_enabled'] = $_settings['social_facebook_enabled'] || $_settings['social_twitter_enabled'] || $_settings['social_googleplus_enabled'] || $_settings['social_pinterest_enabled'];

		// --------------------------------------------------------------------------

		//	Save
		if ( $this->blog->set_settings( $_settings ) ) :

			$this->data['success'] = '<strong>Success!</strong> Blog social settings have been saved.';

		else :

			$this->data['error'] = '<strong>Sorry,</strong> there was a problem saving social settings.';

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _blog_update_sidebar()
	{
		//	Prepare update
		$_settings								= array();
		$_settings['sidebar_latest_posts']		= (bool) $this->input->post( 'sidebar_latest_posts' );
		$_settings['sidebar_categories']		= (bool) $this->input->post( 'sidebar_categories' );
		$_settings['sidebar_tags']				= (bool) $this->input->post( 'sidebar_tags' );
		$_settings['sidebar_popular_posts']		= (bool) $this->input->post( 'sidebar_popular_posts' );

		//	TODO: Associations

		// --------------------------------------------------------------------------

		//	Save
		if ( $this->blog->set_settings( $_settings ) ) :

			$this->data['success'] = '<strong>Success!</strong> Blog sidebar settings have been saved.';

		else :

			$this->data['error'] = '<strong>Sorry,</strong> there was a problem saving sidebar settings.';

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
		$this->load->model( 'shop/shop_model',					'shop' );
		$this->load->model( 'shop/shop_payment_gateway_model',	'payment_gateway' );
		$this->load->model( 'shop/shop_currency_model',			'currency' );
		$this->load->model( 'shop/shop_shipping_model',			'shipping' );
		$this->load->model( 'shop/shop_tax_model',				'tax' );

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

				case 'shipping_methods' :

					$this->_shop_shipping_methods();

				break;

				case 'tax_rates' :

					$this->_shop_tax_rates();

				break;

				// --------------------------------------------------------------------------

				default :

					$this->data['error'] = '<strong>Sorry,</strong> I can\'t determine what type of update you are trying to perform.';

				break;

			endswitch;

		endif;

		// --------------------------------------------------------------------------

		//	Get data
		$this->data['settings'] = $this->shop->get_settings( NULL, TRUE );

		if ( $this->user->is_superuser() ) :

			$this->data['payment_gateways'] = $this->payment_gateway->get_all();

		else :

			$this->data['payment_gateways'] = $this->payment_gateway->get_all_supported();

		endif;

		$this->data['currencies_all_flat']		= $this->currency->get_all( FALSE );
		$this->data['currencies_active_flat']	= $this->currency->get_all_flat();
		$this->data['shipping_methods']			= $this->shipping->get_all( FALSE );
		$this->data['tax_rates']				= $this->tax->get_all();
		$this->data['tax_rates_flat']			= $this->tax->get_all_flat();
		array_unshift( $this->data['tax_rates_flat'], 'No Tax');

		// --------------------------------------------------------------------------

		//	Load assets
		$this->asset->load( 'nails.admin.shop.settings.min.js',	TRUE );
		$this->asset->load( 'mustache/mustache.js',				'BOWER' );

		// --------------------------------------------------------------------------

		//	Load views
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'admin/settings/shop',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}


	// --------------------------------------------------------------------------


	protected function _shop_update_settings()
	{
		//	Prepare update
		$_settings									= array();
		$_settings['notify_order']					= $this->input->post( 'notify_order' );
		$_settings['shop_url']						= $this->input->post( 'shop_url' );
		$_settings['free_shipping_threshold']		= (float) $this->input->post( 'free_shipping_threshold' );
		$_settings['warehouse_collection_enabled']	= (bool) $this->input->post( 'warehouse_collection_enabled' );
		$_settings['warehouse_addr_addressee']		= $this->input->post( 'warehouse_addr_addressee' );
		$_settings['warehouse_addr_line1']			= $this->input->post( 'warehouse_addr_line1' );
		$_settings['warehouse_addr_line2']			= $this->input->post( 'warehouse_addr_line2' );
		$_settings['warehouse_addr_town']			= $this->input->post( 'warehouse_addr_town' );
		$_settings['warehouse_addr_postcode']		= $this->input->post( 'warehouse_addr_postcode' );
		$_settings['warehouse_addr_state']			= $this->input->post( 'warehouse_addr_state' );
		$_settings['warehouse_addr_country']		= $this->input->post( 'warehouse_addr_country' );
		$_settings['invoice_company']				= $this->input->post( 'invoice_company' );
		$_settings['invoice_address']				= $this->input->post( 'invoice_address' );
		$_settings['invoice_vat_no']				= $this->input->post( 'invoice_vat_no' );
		$_settings['invoice_company_no']			= $this->input->post( 'invoice_company_no' );
		$_settings['page_brand_listing']			= $this->input->post( 'page_brand_listing' );
		$_settings['page_category_listing']			= $this->input->post( 'page_category_listing' );
		$_settings['page_collection_listing']		= $this->input->post( 'page_collection_listing' );
		$_settings['page_range_listing']			= $this->input->post( 'page_range_listing' );
		$_settings['page_sale_listing']				= $this->input->post( 'page_sale_listing' );
		$_settings['page_tag_listing']				= $this->input->post( 'page_tag_listing' );


		// --------------------------------------------------------------------------

		//	Sanitize shop url
		$_settings['shop_url'] .= substr( $_settings['shop_url'], -1 ) != '/' ? '/' : '';

		// --------------------------------------------------------------------------

		if ( $this->shop->set_settings( $_settings ) ) :

			$this->data['success'] = '<strong>Success!</strong> Store settings have been saved.';

			// --------------------------------------------------------------------------

			//	Rewrite routes
			$this->load->model( 'system/routes_model' );
			if ( ! $this->routes_model->update( 'shop' ) ) :

				$this->data['warning'] = '<strong>Warning:</strong> while the shop settings were updated, the routes file could not be updated. The shop may not behave as expected,';

			endif;

		else :

			$this->data['error'] = '<strong>Sorry,</strong> there was a problem saving settings.';

		endif;
	}

	// --------------------------------------------------------------------------


	protected function _shop_update_paymentgateways()
	{
		//	Prepare update
		foreach( $this->input->post( 'paymentgateway' ) AS $id => $values ) :

			$_data						= new stdClass();

			if ( $this->user->is_superuser() ) :

				$_data->enabled				= isset( $values['enabled'] ) ? (bool) $values['enabled'] : FALSE;
				$_data->sandbox_account_id	= isset( $values['sandbox_account_id'] ) ? $values['sandbox_account_id'] : NULL;
				$_data->sandbox_api_key		= isset( $values['sandbox_api_key'] ) ? $values['sandbox_api_key'] : NULL;
				$_data->sandbox_api_secret	= isset( $values['sandbox_api_secret'] ) ? $values['sandbox_api_secret'] : NULL;

			endif;
			$_data->account_id			= isset( $values['account_id'] ) ? $values['account_id'] : NULL;
			$_data->api_key				= isset( $values['api_key'] ) ? $values['api_key'] : NULL;
			$_data->api_secret			= isset( $values['api_secret'] ) ? $values['api_secret'] : NULL;

			$this->payment_gateway->update( $id, $_data );

		endforeach;

		$this->data['success'] = '<strong>Success!</strong> Payment Gateway settings have been saved.';
	}


	// --------------------------------------------------------------------------


	protected function _shop_update_currencies()
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


	// --------------------------------------------------------------------------


	protected function _shop_shipping_methods()
	{
		$_methods	= $this->input->post( 'methods' );
		$_ids		= array();

		foreach( $_methods AS $counter => &$method ) :

			//	If there's an ID we'll be updating (remove for safety)
			$_id = isset( $method['id'] ) ? $method['id'] : NULL;
			unset( $method['id'] );

			//	Correctly define the Tax Rate ID
			$method['tax_rate_id']	= $method['tax_rate_id'] ? $method['tax_rate_id'] : NULL;

			//	And is this itemthe default?
			$method['is_default']	= $counter == $this->input->post( 'default' ) ? TRUE : FALSE;

			//	Active?
			$method['is_active']	= isset( $method['is_active'] ) ? TRUE : FALSE;

			if ( $_id ) :

				$this->shipping->update( $_id, $method );
				$_ids[] = $_id;

			else :

				$_ids[] = $this->shipping->create( $method );

			endif;

		endforeach;

		// --------------------------------------------------------------------------

		//	Mark any items not in the $_ids array as is_deleted
		$this->db->set( 'is_deleted', TRUE );

		if ( $_ids ) :

			$this->db->where_not_in( 'id', $_ids );

		endif;

		$this->db->update( NAILS_DB_PREFIX . 'shop_shipping_method' );

		// --------------------------------------------------------------------------

		$this->data['success'] = '<strong>Success!</strong> Shipping Methods have been updated.';
	}


	// --------------------------------------------------------------------------


	protected function _shop_tax_rates()
	{
		$_rates	= $this->input->post( 'rates' );
		$_ids	= array();

		if ( is_array( $_rates ) ) :

			foreach( $_rates AS $counter => &$rate ) :

				//	If there's an ID we'll be updating (remove for safety)
				$_id = isset( $rate['id'] ) ? $rate['id'] : NULL;
				unset( $rate['id'] );

				if ( $_id ) :

					$_data			= new stdClass();
					$_data->label	= $rate['label'];
					$_data->rate	= $rate['rate'];

					if ( $this->tax->update( $_id, $_data ) ) :

						$_ids[] = $_id;

					endif;

				else :

					$_data			= new stdClass();
					$_data->label	= $rate['label'];
					$_data->rate	= $rate['rate'];

					$_result = $this->tax->create( $_data );

					if ( $_result ) :

						$_ids[] = $_result;

					endif;

				endif;

			endforeach;

		endif;

		// --------------------------------------------------------------------------

		//	Mark any items not in the $_ids array as is_deleted
		$this->db->set( 'is_deleted', TRUE );

		if ( $_ids ) :

			$this->db->where_not_in( 'id', $_ids );

		endif;

		$this->db->update( NAILS_DB_PREFIX . 'shop_tax_rate' );

		// --------------------------------------------------------------------------

		$this->data['success'] = '<strong>Success!</strong> Tax Rates have been updated.';
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' ADMIN MODULES
 *
 * The following block of code makes it simple to extend one of the core admin
 * controllers. Some might argue it's a little hacky but it's a simple 'fix'
 * which negates the need to massively extend the CodeIgniter Loader class
 * even further (in all honesty I just can't face understanding the whole
 * Loader class well enough to change it 'properly').
 *
 * Here's how it works:
 *
 * CodeIgniter instantiates a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclare class X' errors
 * and if we call our overloading class something else it will never get instantiated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instantiated et voila.
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


/* End of file settings.php */
/* Location: ./modules/admin/controllers/settings.php */