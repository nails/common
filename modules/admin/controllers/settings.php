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

		//	Process POST
		if ( $this->input->post() ) :

			$_method =  $this->input->post( 'update' );

			if ( method_exists( $this, '_site_update_' . $_method ) ) :

				$this->{'_site_update_' . $_method}();

			else :

				$this->data['error'] = '<strong>Sorry,</strong> I can\'t determine what type of update you are trying to perform.';

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Get data
		$this->data['settings'] = app_setting( NULL, 'app', TRUE );

		// --------------------------------------------------------------------------

		//	Load assets
		$this->asset->load( 'nails.admin.site.settings.min.js', TRUE );
		$this->asset->inline( '<script>_nails_settings = new NAILS_Admin_Site_Settings();</script>' );

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
		if ( $this->app_setting_model->set( $_settings, 'app' ) ) :

			$this->data['success'] = '<strong>Success!</strong> Site settings have been saved.';

		else :

			$this->data['error'] = '<strong>Sorry,</strong> there was a problem saving settings.';

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _site_update_auth()
	{
		//	Prepare update
		$_settings										= array();
		$_settings['user_registration_enabled']			= $this->input->post( 'user_registration_enabled' );
		$_settings['social_signin_fb_enabled']			= $this->input->post( 'social_signin_fb_enabled' );
		$_settings['social_signin_fb_app_id']			= $this->input->post( 'social_signin_fb_app_id' );
		$_settings['social_signin_fb_app_secret']		= $this->input->post( 'social_signin_fb_app_secret' );
		$_settings['social_signin_fb_app_scope']		= array_filter( explode( ',', $this->input->post( 'social_signin_fb_app_scope' ) ) );
		$_settings['social_signin_fb_settings_page']	= $this->input->post( 'social_signin_fb_settings_page' );
		$_settings['social_signin_tw_enabled']			= $this->input->post( 'social_signin_tw_enabled' );
		$_settings['social_signin_tw_app_key']			= $this->input->post( 'social_signin_tw_app_key' );
		$_settings['social_signin_tw_app_secret']		= $this->input->post( 'social_signin_tw_app_secret' );
		$_settings['social_signin_tw_settings_page']	= $this->input->post( 'social_signin_tw_settings_page' );
		$_settings['social_signin_li_enabled']			= $this->input->post( 'social_signin_li_enabled' );
		$_settings['social_signin_li_app_key']			= $this->input->post( 'social_signin_li_app_key' );
		$_settings['social_signin_li_app_secret']		= $this->input->post( 'social_signin_li_app_secret' );
		$_settings['social_signin_li_settings_page']	= $this->input->post( 'social_signin_li_settings_page' );

		if ( $_settings['social_signin_fb_enabled'] || $_settings['social_signin_tw_enabled'] || $_settings['social_signin_li_enabled'] ) :

			$_settings['social_signin_enabled'] = TRUE;

		else :

			$_settings['social_signin_enabled'] = FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Encryptsecrets
		if ( $_settings['social_signin_fb_app_secret'] ) :

			$_settings['social_signin_fb_app_secret'] = $this->encrypt->encode( $_settings['social_signin_fb_app_secret'], APP_PRIVATE_KEY );

		endif;

		if ( $_settings['social_signin_tw_app_secret'] ) :

			$_settings['social_signin_tw_app_secret'] = $this->encrypt->encode( $_settings['social_signin_tw_app_secret'], APP_PRIVATE_KEY );

		endif;

		if ( $_settings['social_signin_li_app_secret'] ) :

			$_settings['social_signin_li_app_secret'] = $this->encrypt->encode( $_settings['social_signin_li_app_secret'], APP_PRIVATE_KEY );

		endif;

		// --------------------------------------------------------------------------

		//	Save
		if ( $this->app_setting_model->set( $_settings, 'app' ) ) :

			$this->data['success'] = '<strong>Success!</strong> Site authentication settings have been saved.';

		else :

			$this->data['error'] = '<strong>Sorry,</strong> there was a problem saving authentication settings.';

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
		$this->load->model( 'blog/blog_skin_model' );

		// --------------------------------------------------------------------------

		//	Process POST
		if ( $this->input->post() ) :

			$_method =  $this->input->post( 'update' );

			if ( method_exists( $this, '_blog_update_' . $_method ) ) :

				$this->{'_blog_update_' . $_method}();

			else :

				$this->data['error'] = '<strong>Sorry,</strong> I can\'t determine what type of update you are trying to perform.';

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Get data
		$this->data['settings'] = app_setting( NULL, 'blog', TRUE );
		$this->data['skins']	= $this->blog_skin_model->get_available();

		// --------------------------------------------------------------------------

		//	Load assets
		$this->asset->load( 'nails.admin.blog.settings.min.js', TRUE );
		$this->asset->inline( '<script>_nails_settings = new NAILS_Admin_Blog_Settings();</script>' );

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
		$_settings['name']					= $this->input->post( 'name' );
		$_settings['url']					= $this->input->post( 'url' );
		$_settings['use_excerpts']			= (bool) $this->input->post( 'use_excerpts' );
		$_settings['categories_enabled']	= (bool) $this->input->post( 'categories_enabled' );
		$_settings['tags_enabled']			= (bool) $this->input->post( 'tags_enabled' );
		$_settings['rss_enabled']			= (bool) $this->input->post( 'rss_enabled' );

		// --------------------------------------------------------------------------

		//	Sanitize blog url
		$_settings['url'] .= substr( $_settings['url'], -1 ) != '/' ? '/' : '';

		// --------------------------------------------------------------------------

		//	Save
		if ( $this->app_setting_model->set( $_settings, 'blog' ) ) :

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


	protected function _blog_update_skin()
	{
		//	Prepare update
		$_settings			= array();
		$_settings['skin']	= $this->input->post( 'skin' );

		// --------------------------------------------------------------------------

		if ( $this->app_setting_model->set( $_settings, 'blog' ) ) :

			$this->data['success'] = '<strong>Success!</strong> Skin settings have been saved.';

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
		if ( $this->app_setting_model->set( $_settings, 'blog' ) ) :

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
		if ( $this->app_setting_model->set( $_settings, 'blog' ) ) :

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
		if ( $this->app_setting_model->set( $_settings, 'blog' ) ) :

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
		$this->load->model( 'shop/shop_model' );
		$this->load->model( 'shop/shop_currency_model' );
		$this->load->model( 'shop/shop_shipping_model' );
		$this->load->model( 'shop/shop_tax_model' );
		$this->load->model( 'shop/shop_skin_model' );

		// --------------------------------------------------------------------------

		//	Process POST
		if ( $this->input->post() ) :

			$_method =  $this->input->post( 'update' );

			if ( method_exists( $this, '_shop_update_' . $_method ) ) :

				$this->{'_shop_update_' . $_method}();

			else :

				$this->data['error'] = '<strong>Sorry,</strong> I can\'t determine what type of update you are trying to perform.';

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Get data
		$this->data['settings']					= app_setting( NULL, 'shop', TRUE );
		$this->data['payment_gateways']			= array();
		$this->data['shipping_modules']			= $this->shop_shipping_model->get_available();
		$this->data['skins']					= $this->shop_skin_model->get_available();
		$this->data['currencies']				= $this->shop_currency_model->get_all( FALSE );
		$this->data['currencies_active_flat']	= $this->shop_currency_model->get_all_flat();
		$this->data['tax_rates']				= $this->shop_tax_model->get_all();
		$this->data['tax_rates_flat']			= $this->shop_tax_model->get_all_flat();
		array_unshift( $this->data['tax_rates_flat'], 'No Tax');

		// --------------------------------------------------------------------------

		//	Load assets
		$this->asset->load( 'nails.admin.shop.settings.min.js',	TRUE );
		$this->asset->load( 'mustache.js/mustache.js',				'BOWER' );
		$this->asset->inline( '<script>_nails_settings = new NAILS_Admin_Shop_Settings();</script>' );

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
		$_settings['domicile']						= $this->input->post( 'domicile' );
		$_settings['name']							= $this->input->post( 'name' );
		$_settings['url']							= $this->input->post( 'url' );
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
		$_settings['url'] .= substr( $_settings['url'], -1 ) != '/' ? '/' : '';

		// --------------------------------------------------------------------------

		if ( $this->app_setting_model->set( $_settings, 'shop' ) ) :

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


	protected function _shop_update_skin()
	{
		//	Prepare update
		$_settings			= array();
		$_settings['skin']	= $this->input->post( 'skin' );

		// --------------------------------------------------------------------------

		if ( $this->app_setting_model->set( $_settings, 'shop' ) ) :

			$this->data['success'] = '<strong>Success!</strong> Skin settings have been saved.';

		else :

			$this->data['error'] = '<strong>Sorry,</strong> there was a problem saving settings.';

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _shop_update_payment_gateway()
	{
		$this->data['message'] = '<strong>TODO</strong> Handling payment gateways is in the works.';
	}


	// --------------------------------------------------------------------------


	protected function _shop_update_currencies()
	{
		//	Prepare update
		$_settings								= array();
		$_settings['base_currency']				= (int) $this->input->post( 'base_currency' );

		// --------------------------------------------------------------------------

		if ( $this->app_setting_model->set( $_settings, 'shop' ) ) :

			$this->data['success'] = '<strong>Success!</strong> Base currency has been saved.';

			//	Save the active currencies
			$_where_in = array( $_settings['base_currency'] );

			foreach ( $this->input->post( 'active_currencies' ) AS $id ) :

				$_where_in[] = $id;

			endforeach;

			if ( $this->shop_currency_model->set_active_currencies( $_where_in ) ) :

				$this->data['success'] = '<strong>Success!</strong> Currency settings have been updated.';

			else :

				$this->data['error'] = '<strong>Sorry,</strong> an error occurred while setting supported currencies.';

			endif;

		else :

			$this->data['error'] = '<strong>Sorry,</strong> there was a problem saving the base currency.';

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _shop_update_shipping()
	{
		$this->data['message'] = '<strong>TODO</strong> Handling shipping modules in the works.';
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