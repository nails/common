<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Admin - Utilities
 *
 * Description:	-
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
 
class NAILS_Utilities extends NAILS_Admin_Controller
{
	protected $_export_sources;
	protected $_export_formats;


	// --------------------------------------------------------------------------


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
		get_instance()->lang->load( 'admin_utilities', RENDER_LANG_SLUG );
		
		// --------------------------------------------------------------------------
		
		//	Configurations
		$d->name = lang( 'utilities_module_name' );
		
		// --------------------------------------------------------------------------
		
		//	Navigation options
		$d->funcs					= array();
		$d->funcs['test_email']		= lang( 'utilities_nav_test_email' );
		$d->funcs['user_access']	= lang( 'utilities_nav_user_access' );
		$d->funcs['languages']		= lang( 'utilities_nav_languages' );
		$d->funcs['export']			= lang( 'utilities_nav_export' );

		
		// --------------------------------------------------------------------------
		
		//	Only announce the controller if the user has permisison to know about it
		return self::_can_access( $d, __FILE__ );
	}



	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Default export sources
		$_acl						= active_user( 'acl' );
		$this->_export_sources		= array();

		if ( $this->user->is_superuser() || isset( $_acl['admin']['accounts']['index'] ) )
		$this->_export_sources[]	= array( 'Members: All', 'Export a list of all the site\'s registered users (basic info).', 'users_all' );

		if ( module_is_enabled( 'shop' ) ) :

			if ( $this->user->is_superuser() || isset( $_acl['admin']['shop']['inventory'] ) )
			$this->_export_sources[]	= array( 'Shop: Inventory', 'Export a list of the shop\'s inventory.', 'shop_inventory' );

			if ( $this->user->is_superuser() || isset( $_acl['admin']['shop']['orders'] ) )
			$this->_export_sources[]	= array( 'Shop: Orders', 'Export a list of all shop orders.', 'shop_orders' );

			if ( $this->user->is_superuser() || isset( $_acl['admin']['shop']['vouchers'] ) )
			$this->_export_sources[]	= array( 'Shop: Vouchers', 'Export a list of all shop vouchers.', 'shop_vouchers' );

		endif;

		// --------------------------------------------------------------------------

		//	Default export formats
		$this->_export_formats		= array();
		$this->_export_formats[]	= array( 'CSV', 'Easily imports to many software packages, including Microsoft Excel.', 'csv' );
		$this->_export_formats[]	= array( 'HTML', 'Produces an HTML table containing the data', 'html' );
		$this->_export_formats[]	= array( 'PHP Serialize', 'Export as an object serialized using PHP\'s serialize() function', 'serialize' );
		$this->_export_formats[]	= array( 'JSON', 'Export as a JSON array', 'json' );
	}
	
	// --------------------------------------------------------------------------
	

	/**
	 * Send test email
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function test_email()
	{
		//	Page Title
		$this->data['page']->title = lang ( 'utilities_test_email_title' );
		
		// --------------------------------------------------------------------------
		
		if ( $this->input->post() ) :
		
			//	Form validation and update
			$this->load->library( 'form_validation' );
			
			//	Define rules
			$this->form_validation->set_rules( 'recipient',	lang( 'utilities_test_email_field_name' ), 'xss_clean|required|valid_email' );
			
			//	Set Messages
			$this->form_validation->set_message( 'required',	lang( 'fv_required' ) );
			$this->form_validation->set_message( 'valid_email',	lang( 'fv_valid_email' ) );
			
			//	Execute
			if ( $this->form_validation->run() ) :
			
				//	Prepare date
				$_email				= new stdClass();
				$_email->to_email	= $this->input->post( 'recipient' );
				$_email->type		= 'test_email';
				
				//	Send the email
				$this->load->library( 'emailer' );
				
				if ( $this->emailer->send( $_email ) ) :
				
					$this->data['success'] = lang( 'utilities_test_email_success', array( $_email->to_email, date( 'Y-m-d H:i:s' ) ) );
					
				else:
				
					echo '<h1>' . lang( 'utilities_test_email_error' ) . '</h1>';
					echo $this->email->print_debugger();
					return;
				
				endif;
				
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'admin/utilities/send_test',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Manage user groups ACL's
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function user_access()
	{
		//	Page Title
		$this->data['page']->title = lang ( 'utilities_user_access_title' );
		
		// --------------------------------------------------------------------------
		
		$this->data['groups'] = $this->user->get_groups();
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'admin/utilities/user_access',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Edit a group
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function edit_group()
	{
		$_gid = $this->uri->segment( 4, NULL );
		
		// --------------------------------------------------------------------------
		
		if ( $this->input->post() ) :
		
			//	Load library
			$this->load->library( 'form_validation' );
			
			//	Define rules
			$this->form_validation->set_rules( 'display_name',			lang( 'utilities_edit_group_basic_field_label_display' ),		'xss_clean|required' );
			$this->form_validation->set_rules( 'name',					lang( 'utilities_edit_group_basic_field_label_name' ),			'xss_clean|required' );
			$this->form_validation->set_rules( 'description',			lang( 'utilities_edit_group_basic_field_label_description' ),	'xss_clean|required' );
			$this->form_validation->set_rules( 'default_homepage',		lang( 'utilities_edit_group_basic_field_label_homepage' ), 		'xss_clean|required' );
			$this->form_validation->set_rules( 'registration_redirect',	lang( 'utilities_edit_group_basic_field_label_registration' ), 	'xss_clean' );
			$this->form_validation->set_rules( 'acl[]',					lang( 'utilities_edit_group_permission_legend' ), 				'xss_clean' );
			$this->form_validation->set_rules( 'acl[superuser]',		lang( 'utilities_edit_group_permission_legend' ), 				'xss_clean' );
			$this->form_validation->set_rules( 'acl[admin]',			lang( 'utilities_edit_group_permission_legend' ), 				'xss_clean' );
			$this->form_validation->set_rules( 'acl[admin][]',			lang( 'utilities_edit_group_permission_legend' ), 				'xss_clean' );
			
			//	Set messages
			$this->form_validation->set_message( 'required', lang( 'fv_required' ) );
			
			if ( $this->form_validation->run() ) :
			
				$_data = array();
				$_data['display_name']			= $this->input->post( 'display_name' );
				$_data['name']					= url_title( $this->input->post( 'name' ), 'dash', TRUE );
				$_data['description']			= $this->input->post( 'description' );
				$_data['default_homepage']		= $this->input->post( 'default_homepage' );
				$_data['registration_redirect']	= $this->input->post( 'registration_redirect' );
				
				//	Parse ACL's
				$_acl = $this->input->post( 'acl' );
				
				if ( isset( $_acl['admin'] ) ) :
				
					//	Remove ACLs which have no enabled methods - pointless	
					$_acl['admin'] = array_filter( $_acl['admin'] );
				
				endif;
				
				$_data['acl']				= serialize( $_acl );
				
				$this->user->update_group( $_gid, $_data );
				
				$this->session->set_flashdata( 'success', '<strong>Huzzah!</strong> Group updated successfully!' );
				redirect( 'admin/utilities/user_access' );
				return;
				
			else :
			
				$this->data['error'] = validation_errors();
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->data['group'] = $this->user->get_group( $_gid );
		
		if ( ! $this->data['group'] ) :
		
			$this->session->set_flashdata( 'error', 'Group does not exist.' );
			redirect( 'admin/utilities/user_access' );
		
		endif;
		
		$this->data['admin_modules'] = $this->_loaded_modules;
		
		// --------------------------------------------------------------------------
		
		//	Page title
		$this->data['page']->title = lang( 'utilities_edit_group_title', $this->data['group']->display_name );
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'admin/utilities/edit_group',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}


	// --------------------------------------------------------------------------


	public function languages()
	{
		//	Page Title
		$this->data['page']->title = lang ( 'utilities_languages_title' );
		
		// --------------------------------------------------------------------------
		
		$this->data['languages'] = $this->language->get_all();
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->load->view( 'structure/header',					$this->data );
		$this->load->view( 'admin/utilities/languages/index',	$this->data );
		$this->load->view( 'structure/footer',					$this->data );
	}


	// --------------------------------------------------------------------------


	public function mark_lang_supported()
	{
		$_id = $this->uri->segment( 4 );

		if ( $this->language->mark_supported( $_id ) ) :

			$this->session->set_flashdata( 'success', lang( 'utilities_languages_mark_supported_ok' ) );

		else :

			$this->session->set_flashdata( 'success', lang( 'utilities_languages_mark_supported_fail' ) );

		endif;

		redirect( 'admin/utilities/languages' );
	}


	// --------------------------------------------------------------------------


	public function mark_lang_unsupported()
	{
		$_id = $this->uri->segment( 4 );

		if ( $this->language->mark_unsupported( $_id ) ) :

			$this->session->set_flashdata( 'success', lang( 'utilities_languages_mark_unsupported_ok' ) );

		else :

			$this->session->set_flashdata( 'success', lang( 'utilities_languages_mark_unsupported_fail' ) );

		endif;

		redirect( 'admin/utilities/languages' );
	}


	// --------------------------------------------------------------------------


	public function export()
	{
		if ( $this->input->post() ) :

			//	Form validation and update
			$this->load->library( 'form_validation' );
			
			//	Define rules
			$this->form_validation->set_rules( 'source',	lang( 'utilities_export_field_source' ), 'xss_clean|required' );
			$this->form_validation->set_rules( 'format',	lang( 'utilities_export_field_format' ), 'xss_clean|required' );
			
			//	Set Messages
			$this->form_validation->set_message( 'required',	lang( 'fv_required' ) );
			
			//	Execute
			if ( $this->form_validation->run() && isset( $this->_export_sources[$this->input->post( 'source' )] ) && isset( $this->_export_formats[$this->input->post( 'format' )] ) ) :

				$_source = $this->_export_sources[$this->input->post( 'source' )];
				$_format = $this->_export_formats[$this->input->post( 'format' )];

				if ( ! method_exists( $this, '_export_source_' . $_source[2] ) ) :

					$this->data['error'] = lang( 'utilities_export_error_source_notexist' );

				elseif ( ! method_exists( $this, '_export_format_' . $_format[2] ) ) :

					$this->data['error'] = lang( 'utilities_export_error_format_notexist' );

				else :

					//	All seems well, export data!
					$_data = $this->{'_export_source_' . $_source[2]}();
					$this->{'_export_format_' . $_format[2]}( $_data, $_source[2] );
					return;

				endif;


			elseif ( ! isset( $this->_export_sources[ $this->input->post( 'source' ) ] ) ) :

				$this->data['error'] = lang( 'utilities_export_error_source' );

			elseif ( ! isset( $this->_export_formats[ $this->input->post( 'format' ) ] ) ) :

				$this->data['error'] = lang( 'utilities_export_error_format' );

			else: 

				$this->data['error'] = lang( 'fv_there_were_errors' );

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Set view data
		$this->data['page']->title	= lang( 'utilities_export_title' );
		$this->data['sources']		= $this->_export_sources;
		$this->data['formats']		= $this->_export_formats;

		// --------------------------------------------------------------------------

		//	Load views
		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'admin/utilities/export/index',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}


	// --------------------------------------------------------------------------


	protected function _export_source_users_all()
	{
		if ( ! $this->user->is_superuser() && ! isset( $_acl['admin']['accounts']['index'] ) ) :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> you do not have permission to export that data.' );
			redirect( 'admin/utilities/export' );
			return;

		endif;

		// --------------------------------------------------------------------------

		$_out			= new stdClass();
		$_out->fields	= array();
		$_out->data		= array();

		// --------------------------------------------------------------------------

		//	Fetch all users
		$this->db->select( 'u.id,u.group_id,ug.display_name group_name,u.username,u.email,u.created,u.last_login,u.last_seen,u.last_update,u.is_verified,u.is_suspended,u.login_count,u.salutation,u.first_name,u.last_name' );
		$this->db->order_by( 'u.id' );
		$this->db->join( 'user_group ug', 'ug.id = u.group_id' );
		$_out->data = $this->db->get( 'user u' )->result_array();

		if ( $_out->data ) :

			$_out->fields = array_keys( $_out->data[0] );

		endif;

		return $_out;
	}


	// --------------------------------------------------------------------------


	protected function _export_source_shop_inventory()
	{
		if ( ! $this->user->is_superuser() && ! isset( $_acl['admin']['shop']['inventory'] ) ) :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> you do not have permission to export that data.' );
			redirect( 'admin/utilities/export' );
			return;

		endif;

		// --------------------------------------------------------------------------

		$_out			= new stdClass();
		$_out->fields	= array( 'TODO' );
		$_out->data		= array( array( 'TODO' ) );

		// --------------------------------------------------------------------------

		return $_out;
	}


	// --------------------------------------------------------------------------


	protected function _export_source_shop_orders()
	{
		if ( ! $this->user->is_superuser() && ! isset( $_acl['admin']['shop']['orders'] ) ) :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> you do not have permission to export that data.' );
			redirect( 'admin/utilities/export' );
			return;

		endif;

		// --------------------------------------------------------------------------

		$_out			= new stdClass();
		$_out->fields	= array();
		$_out->data		= array();

		// --------------------------------------------------------------------------

		//	Fetch all orders
		$this->db->select( 'o.id,o.ref,o.user_id,o.user_email,o.user_first_name,o.user_last_name,o.status' );
		$this->db->select( 'o.requires_shipping,o.fulfilment_status,o.created,o.modified,o.fulfilled,o.exchange_rate' ) ;
		$this->db->select( 'o.shipping_total,o.sub_total,o.tax_shipping,o.tax_items,o.discount_shipping,o.discount_items' );
		$this->db->select( 'o.grand_total,o.fees_deducted' );
		$_out->data = $this->db->get( 'shop_order o' )->result_array();
		
		if ( $_out->data ) :

			$_out->fields = array_keys( $_out->data[0] );

		endif;

		// --------------------------------------------------------------------------

		return $_out;
	}


	// --------------------------------------------------------------------------


	protected function _export_source_shop_vouchers()
	{
		if ( ! $this->user->is_superuser() && ! isset( $_acl['admin']['shop']['vouchers'] ) ) :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> you do not have permission to export that data.' );
			redirect( 'admin/utilities/export' );
			return;

		endif;

		// --------------------------------------------------------------------------

		$_out			= new stdClass();
		$_out->fields	= array();
		$_out->data		= array();

		// --------------------------------------------------------------------------

		//	Fetch all vouchers
		$this->db->select( 'v.id,v.code,v.type,v.discount_type,v.discount_value,v.discount_application,v.label,v.valid_from' );
		$this->db->select( 'v.valid_to,v.use_count,v.limited_use_limit,v.gift_card_balance,v.product_type_id,v.created' );
		$this->db->select( 'v.modified,v.is_active,v.is_deleted' );
		$_out->data = $this->db->get( 'shop_voucher v' )->result_array();
		
		if ( $_out->data ) :

			$_out->fields = array_keys( $_out->data[0] );

		endif;

		// --------------------------------------------------------------------------

		return $_out;
	}


	// --------------------------------------------------------------------------


	protected function _export_format_csv( $data, $filename )
	{
		//	Send header
		$this->output->set_header( 'Pragma: public' );
		$this->output->set_header( 'Expires: 0' );
		$this->output->set_header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		$this->output->set_header( 'Cache-Control: private', FALSE );
		$this->output->set_header( 'Content-Type: application/octet-stream' );
		$this->output->set_header( 'Content-Disposition: attachment; filename=data-export-' . $filename . '-' . date( 'Y-m-d_h-i-s' ) . '.csv;' );
		$this->output->set_header( 'Content-Transfer-Encoding: binary' );

		// --------------------------------------------------------------------------

		//	Set view data
		$this->data['fields']	= $data->fields;
		$this->data['data']		= $data->data;

		// --------------------------------------------------------------------------

		//	Load view
		$this->load->view( 'admin/utilities/export/csv', $this->data );
	}


	// --------------------------------------------------------------------------


	protected function _export_format_html( $data, $filename )
	{
		//	Send header
		$this->output->set_header( 'Pragma: public' );
		$this->output->set_header( 'Expires: 0' );
		$this->output->set_header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		$this->output->set_header( 'Cache-Control: private', FALSE );
		$this->output->set_header( 'Content-Type: application/octet-stream' );
		$this->output->set_header( 'Content-Disposition: attachment; filename=data-export-' . $filename . '-' . date( 'Y-m-d_h-i-s' ) . '.html;' );
		$this->output->set_header( 'Content-Transfer-Encoding: binary' );

		// --------------------------------------------------------------------------

		//	Set view data
		$this->data['fields']	= $data->fields;
		$this->data['data']		= $data->data;

		// --------------------------------------------------------------------------

		//	Load view
		$this->load->view( 'admin/utilities/export/html', $this->data );
	}


	// --------------------------------------------------------------------------


	protected function _export_format_serialize( $data, $filename )
	{
		//	Send header
		$this->output->set_header( 'Pragma: public' );
		$this->output->set_header( 'Expires: 0' );
		$this->output->set_header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		$this->output->set_header( 'Cache-Control: private', FALSE );
		$this->output->set_header( 'Content-Type: application/octet-stream' );
		$this->output->set_header( 'Content-Disposition: attachment; filename=data-export-' . $filename . '-' . date( 'Y-m-d_h-i-s' ) . '.txt;' );
		$this->output->set_header( 'Content-Transfer-Encoding: binary' );

		// --------------------------------------------------------------------------

		//	Set view data
		$this->data['data']		= $data;

		// --------------------------------------------------------------------------

		//	Load view
		$this->load->view( 'admin/utilities/export/serialize', $this->data );
	}


	// --------------------------------------------------------------------------


	protected function _export_format_json( $data, $filename )
	{
		//	Send header
		$this->output->set_header( 'Pragma: public' );
		$this->output->set_header( 'Expires: 0' );
		$this->output->set_header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		$this->output->set_header( 'Cache-Control: private', FALSE );
		$this->output->set_header( 'Content-Type: application/octet-stream' );
		$this->output->set_header( 'Content-Disposition: attachment; filename=data-export-' . $filename . '-' . date( 'Y-m-d_h-i-s' ) . '.json;' );
		$this->output->set_header( 'Content-Transfer-Encoding: binary' );

		// --------------------------------------------------------------------------

		//	Set view data
		$this->data['data']		= $data;

		// --------------------------------------------------------------------------

		//	Load view
		$this->load->view( 'admin/utilities/export/json', $this->data );
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
 
if ( ! defined( 'NAILS_ALLOW_EXTENSION_UTILITIES' ) ) :

	class Utilities extends NAILS_Utilities
	{
	}

endif;


/* End of file faq.php */
/* Location: ./application/modules/admin/controllers/faq.php */