<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Admin: Utilities
 * Description:	Various admin utilities
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
	 **/
	static function announce()
	{
		$d = new stdClass();

		// --------------------------------------------------------------------------

		//	Load the laguage file
		get_instance()->lang->load( 'admin_utilities' );

		// --------------------------------------------------------------------------

		//	Configurations
		$d->name = lang( 'utilities_module_name' );

		// --------------------------------------------------------------------------

		//	Navigation options
		$d->funcs					= array();
		$d->funcs['test_email']		= lang( 'utilities_nav_test_email' );
		$d->funcs['export']			= lang( 'utilities_nav_export' );

		if ( module_is_enabled( 'cdn' ) ) :

			$d->funcs['cdn/orphans']	= 'CDN: Find orphaned objects';

		endif;

		// --------------------------------------------------------------------------

		//	Only announce the controller if the user has permisison to know about it
		return self::_can_access( $d, __FILE__ );
	}


	// --------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Default export sources
		$_acl						= active_user( 'acl' );
		$this->_export_sources		= array();

		if ( $this->user_model->is_superuser() || isset( $_acl['admin']['accounts']['index'] ) )
		$this->_export_sources[]	= array( 'Members: All', 'Export a list of all the site\'s registered users and their meta data.', 'users_all' );

		if ( module_is_enabled( 'shop' ) ) :

			if ( $this->user_model->is_superuser() || isset( $_acl['admin']['shop']['inventory'] ) )
			$this->_export_sources[]	= array( 'Shop: Inventory', 'Export a list of the shop\'s inventory.', 'shop_inventory' );

			if ( $this->user_model->is_superuser() || isset( $_acl['admin']['shop']['orders'] ) )
			$this->_export_sources[]	= array( 'Shop: Orders', 'Export a list of all shop orders and their products.', 'shop_orders' );

			if ( $this->user_model->is_superuser() || isset( $_acl['admin']['shop']['vouchers'] ) )
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
				$_email->data		= array();

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

					//	if $_data is an array then we need to write multiple files to a zip
					if ( is_array( $_data ) ) :

						//	Load Zip class
						$this->load->library( 'zip' );

						//	Process each file
						foreach( $_data AS $data ) :

							$_file = $this->{'_export_format_' . $_format[2]}( $data, TRUE );

							$this->zip->add_data( $_file[0], $_file[1] );

						endforeach;

						$this->zip->download( 'data-export-' . $_source[2] . '-' . date( 'Y-m-d_H-i-s' ) );

					else :

						$this->{'_export_format_' . $_format[2]}( $_data );

					endif;

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


	protected function _export_source_users_all( $out = array() )
	{
		$_acl = active_user( 'acl' );
		if ( ! $this->user_model->is_superuser() && ! isset( $_acl['admin']['accounts']['index'] ) ) :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> you do not have permission to export that data.' );
			redirect( 'admin/utilities/export' );
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Prepare our out array
		$_out		= $out;
		$_counter	= count( $_out );

		//	User
		$_out[$_counter]			= new stdClass();
		$_out[$_counter]->filename	= NAILS_DB_PREFIX . 'user';
		$_out[$_counter]->fields	= array();
		$_out[$_counter]->data		= array();
		$_counter++;

		//	user_group
		$_out[$_counter]			= new stdClass();
		$_out[$_counter]->filename	= NAILS_DB_PREFIX . 'user_group';
		$_out[$_counter]->fields	= array();
		$_out[$_counter]->data		= array();
		$_counter++;

		//	user_auth_method
		$_out[$_counter]			= new stdClass();
		$_out[$_counter]->filename	= NAILS_DB_PREFIX . 'user_auth_method';
		$_out[$_counter]->fields	= array();
		$_out[$_counter]->data		= array();
		$_counter++;

		//	user_meta
		$_out[$_counter]			= new stdClass();
		$_out[$_counter]->filename	= NAILS_DB_PREFIX . 'user_meta';
		$_out[$_counter]->fields	= array();
		$_out[$_counter]->data		= array();
		$_counter++;

		//	Nails user_meta_* tables
		$_tables = $this->db->query( 'SHOW TABLES LIKE \'' . NAILS_DB_PREFIX . 'user_meta_%\'' )->result();
		foreach( $_tables AS $table ) :

			$_table = array_values( (array) $table );

			$_out[$_counter]			= new stdClass();
			$_out[$_counter]->filename	= $_table[0];
			$_out[$_counter]->fields	= array();
			$_out[$_counter]->data		= array();

			$_counter++;

		endforeach;

		//	All other user_meta_* tables
		$_tables = $this->db->query( 'SHOW TABLES LIKE \'user_meta_%\'' )->result();
		foreach( $_tables AS $table ) :

			$_table = array_values( (array) $table );

			$_out[$_counter]			= new stdClass();
			$_out[$_counter]->filename	= $_table[0];
			$_out[$_counter]->fields	= array();
			$_out[$_counter]->data		= array();

			$_counter++;

		endforeach;

		// --------------------------------------------------------------------------

		//	Fetch data
		foreach( $_out AS &$out ) :

			$_fields = $this->db->query( 'DESCRIBE ' . $out->filename )->result();
			foreach ( $_fields AS $field ) :

				$out->fields[] = $field->Field;

			endforeach;

			$out->data	= $this->db->get( $out->filename )->result_array();

		endforeach;

		// --------------------------------------------------------------------------

		return $_out;
	}


	// --------------------------------------------------------------------------


	protected function _export_source_shop_inventory()
	{
		$_acl = active_user( 'acl' );
		if ( ! $this->user_model->is_superuser() && ! isset( $_acl['admin']['shop']['inventory'] ) ) :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> you do not have permission to export that data.' );
			redirect( 'admin/utilities/export' );
			return;

		endif;

		// --------------------------------------------------------------------------

		$_out			= new stdClass();
		$_out->filename	= 'shop_inventory';
		$_out->fields	= array( 'TODO' );
		$_out->data		= array( array( 'TODO' ) );

		// --------------------------------------------------------------------------

		return $_out;
	}


	// --------------------------------------------------------------------------


	protected function _export_source_shop_orders()
	{
		$_acl = active_user( 'acl' );
		if ( ! $this->user_model->is_superuser() && ! isset( $_acl['admin']['shop']['orders'] ) ) :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> you do not have permission to export that data.' );
			redirect( 'admin/utilities/export' );
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Prepare the $_out var; give each table its own index

		//	Orders
		$_out				= array();
		$_out[0]			= new stdClass();
		$_out[0]->filename	= NAILS_DB_PREFIX . 'shop_orders';
		$_out[0]->fields	= array();
		$_out[0]->data		= array();

		//	Order_products
		$_out[1]			= new stdClass();
		$_out[1]->filename	= NAILS_DB_PREFIX . 'shop_order_products';
		$_out[1]->fields	= array();
		$_out[1]->data		= array();

		// --------------------------------------------------------------------------

		//	Fetch all orders
		$this->db->select( 'o.id,o.ref,o.user_id,o.user_email,o.user_first_name,o.user_last_name,o.status' );
		$this->db->select( 'o.requires_shipping,o.fulfilment_status,o.created,o.modified,o.fulfilled,o.exchange_rate' ) ;
		$this->db->select( 'o.shipping_total,o.sub_total,o.tax_shipping,o.tax_items,o.discount_shipping,o.discount_items' );
		$this->db->select( 'o.grand_total,o.fees_deducted,o.payment_gateway_id, pg.label payment_gateway_label' );
		$this->db->select( 'o.shipping_method_id, sm.courier shipping_method_courier, sm.method shipping_method_method' );
		$this->db->select( 'o.shipping_addressee,o.shipping_line_1,o.shipping_line_2,o.shipping_town,o.shipping_postcode,o.shipping_country,o.shipping_state' );
		$this->db->select( 'o.voucher_id,v.code voucher_code,v.type voucher_type,v.discount_type voucher_discount_type' );
		$this->db->select( 'v.discount_value voucher_discount_value,v.discount_application voucher_discount_application' );
		$this->db->select( 'v.label voucher_label, v.valid_from voucher_valid_from,v.valid_to voucher_valid_to, v.use_count voucher_use_count' );

		$this->db->join( NAILS_DB_PREFIX . 'shop_payment_gateway pg', 'pg.id = o.payment_gateway_id', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'shop_shipping_method sm', 'sm.id = o.shipping_method_id', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'shop_voucher v', 'v.id = o.voucher_id', 'LEFT' );

		$_out[0]->data = $this->db->get( NAILS_DB_PREFIX . 'shop_order o' )->result_array();

		if ( $_out[0]->data ) :

			$_out[0]->fields = array_keys( $_out[0]->data[0] );

		endif;

		// --------------------------------------------------------------------------

		//	Fetch all order_products
		$this->db->select( 'op.id, op.order_id, op.product_id, op.title product_title, pt.label product_type, op.price,op.sale_price' );
		$this->db->select( 'op.was_on_sale,op.shipping,op.tax,op.shipping_tax,op.total,tr.label tax_rate_label,tr.rate tax_rate,op.processed' );
		$this->db->select( 'op.refunded,op.refunded_date,op.extra_data' );

		$this->db->join( NAILS_DB_PREFIX . 'shop_product p', 'p.id = op.product_id' );
		$this->db->join( NAILS_DB_PREFIX . 'shop_product_type pt', 'pt.id = p.type_id' );
		$this->db->join( NAILS_DB_PREFIX . 'shop_tax_rate tr', 'tr.id = p.tax_rate_id', 'LEFT' );

		$_out[1]->data = $this->db->get( NAILS_DB_PREFIX . 'shop_order_product op' )->result_array();

		if ( $_out[1]->data ) :

			$_out[1]->fields = array_keys( $_out[1]->data[0] );

		endif;

		// --------------------------------------------------------------------------

		return $_out;
	}


	// --------------------------------------------------------------------------


	protected function _export_source_shop_vouchers()
	{
		$_acl = active_user( 'acl' );
		if ( ! $this->user_model->is_superuser() && ! isset( $_acl['admin']['shop']['vouchers'] ) ) :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> you do not have permission to export that data.' );
			redirect( 'admin/utilities/export' );
			return;

		endif;

		// --------------------------------------------------------------------------

		$_out			= new stdClass();
		$_out->filename	= NAILS_DB_PREFIX . 'shop_vouchers';
		$_out->fields	= array();
		$_out->data		= array();

		// --------------------------------------------------------------------------

		//	Fetch all vouchers
		$this->db->select( 'v.id,v.code,v.type,v.discount_type,v.discount_value,v.discount_application,v.label,v.valid_from' );
		$this->db->select( 'v.valid_to,v.use_count,v.limited_use_limit,v.gift_card_balance,v.product_type_id,v.created' );
		$this->db->select( 'v.modified,v.is_active,v.is_deleted' );
		$_out->data = $this->db->get( NAILS_DB_PREFIX . 'shop_voucher v' )->result_array();

		if ( $_out->data ) :

			$_out->fields = array_keys( $_out->data[0] );

		endif;

		// --------------------------------------------------------------------------

		return $_out;
	}


	// --------------------------------------------------------------------------


	protected function _export_format_csv( $data, $return_data = FALSE )
	{
		//	Send header
		if ( ! $return_data ) :

			$this->output->set_content_type( 'application/octet-stream' );
			$this->output->set_header( 'Pragma: public' );
			$this->output->set_header( 'Expires: 0' );
			$this->output->set_header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
			$this->output->set_header( 'Cache-Control: private', FALSE );
			$this->output->set_header( 'Content-Disposition: attachment; filename=data-export-' . $data->filename . '-' . date( 'Y-m-d_H-i-s' ) . '.csv;' );
			$this->output->set_header( 'Content-Transfer-Encoding: binary' );

		endif;

		// --------------------------------------------------------------------------

		//	Set view data
		$this->data['fields']	= $data->fields;
		$this->data['data']		= $data->data;

		// --------------------------------------------------------------------------

			//	Load view
		if ( ! $return_data ) :

			$this->load->view( 'admin/utilities/export/csv', $this->data );

		else :

			$_out	= array();
			$_out[]	= $data->filename . '.csv';
			$_out[]	= $this->load->view( 'admin/utilities/export/csv', $this->data, TRUE );

			return $_out;

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _export_format_html( $data, $return_data = FALSE )
	{
		//	Send header
		if ( ! $return_data ) :

			$this->output->set_content_type( 'application/octet-stream' );
			$this->output->set_header( 'Pragma: public' );
			$this->output->set_header( 'Expires: 0' );
			$this->output->set_header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
			$this->output->set_header( 'Cache-Control: private', FALSE );
			$this->output->set_header( 'Content-Disposition: attachment; filename=data-export-' . $data->filename . '-' . date( 'Y-m-d_H-i-s' ) . '.html;' );
			$this->output->set_header( 'Content-Transfer-Encoding: binary' );

		endif;

		// --------------------------------------------------------------------------

		//	Set view data
		$this->data['fields']	= $data->fields;
		$this->data['data']		= $data->data;

		// --------------------------------------------------------------------------

		//	Load view
		if ( ! $return_data ) :

			$this->load->view( 'admin/utilities/export/html', $this->data );

		else :

			$_out	= array();
			$_out[]	= $data->filename . '.html';
			$_out[]	= $this->load->view( 'admin/utilities/export/html', $this->data, TRUE );

			return $_out;

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _export_format_serialize( $data, $return_data = FALSE )
	{
		//	Send header
		if ( ! $return_data ) :

			$this->output->set_content_type( 'application/octet-stream' );
			$this->output->set_header( 'Pragma: public' );
			$this->output->set_header( 'Expires: 0' );
			$this->output->set_header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
			$this->output->set_header( 'Cache-Control: private', FALSE );
			$this->output->set_header( 'Content-Disposition: attachment; filename=data-export-' . $data->filename . '-' . date( 'Y-m-d_H-i-s' ) . '.txt;' );
			$this->output->set_header( 'Content-Transfer-Encoding: binary' );

		endif;

		// --------------------------------------------------------------------------

		//	Set view data
		$this->data['data'] = $data;

		// --------------------------------------------------------------------------

		//	Load view
		if ( ! $return_data ) :

			$this->load->view( 'admin/utilities/export/serialize', $this->data );

		else :

			$_out	= array();
			$_out[]	= $data->filename . '.txt';
			$_out[]	= $this->load->view( 'admin/utilities/export/serialize', $this->data, TRUE );

			return $_out;

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _export_format_json( $data, $return_data = FALSE )
	{
		//	Send header
		if ( ! $return_data ) :

			$this->output->set_content_type( 'application/octet-stream' );
			$this->output->set_header( 'Pragma: public' );
			$this->output->set_header( 'Expires: 0' );
			$this->output->set_header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
			$this->output->set_header( 'Cache-Control: private', FALSE );
			$this->output->set_header( 'Content-Disposition: attachment; filename=data-export-' . $data->filename . '-' . date( 'Y-m-d_H-i-s' ) . '.json;' );
			$this->output->set_header( 'Content-Transfer-Encoding: binary' );

		endif;

		// --------------------------------------------------------------------------

		//	Set view data
		$this->data['data']		= $data;

		// --------------------------------------------------------------------------

		//	Load view
		if ( ! $return_data ) :

			$this->load->view( 'admin/utilities/export/json', $this->data );

		else :

			$_out	= array();
			$_out[]	= $data->filename . '.json';
			$_out[]	= $this->load->view( 'admin/utilities/export/json', $this->data, TRUE );

			return $_out;

		endif;
	}


	// --------------------------------------------------------------------------


	public function cdn()
	{
		switch ( $this->uri->segment( 4 ) ) :

			case 'orphans' :	$this->_cdn_orphans();	break;
			default :			show_404();				break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	protected function _cdn_orphans()
	{
		if ( $this->input->is_cli_request() ) :

			return $this->_cdn_orphans_cli();

		endif;

		// --------------------------------------------------------------------------

		if ( $this->input->post() ) :

			//	A little form validation
			$_type		= $this->input->post( 'type' );
			$_parser	= $this->input->post( 'parser' );
			$_pass		= TRUE;

			if ( $_type == 'db' && $_parser == 'create' ) :

				$_pass	= FALSE;
				$_error	= 'Cannot use "Add to database" results parser when finding orphaned database objects.';

			endif;


			if ( $_pass ) :

				switch( $_type ) :

					case 'db'	:	$this->data['orphans']	= $this->cdn->find_orphaned_objects();				break;

					//	TODO
					case 'file'	:	$this->data['message']	= '<strong>TODO:</strong> find orphaned files.';	break;

					//	Invalid request
					default		:	$this->data['error']	= '<strong>Sorry,</strong> invalid search type.';	break;

				endswitch;

				if ( isset( $this->data['orphans'] ) ) :

					switch( $_parser ) :

						case 'list'		:	$this->data['success'] = '<strong>Search complete!</strong> your results are show below.';								break;

						//	TODO: keep the unset(), it prevents the table from rendering
						case 'purge'	:	$this->data['message']	= '<strong>TODO:</strong> purge results.'; unset( $this->data['orphans'] );						break;
						case 'create'	:	$this->data['message']	= '<strong>TODO:</strong> create objects using results.'; unset( $this->data['orphans'] );		break;

						//	Invalid request
						default			:	$this->data['error']	= '<strong>Sorry,</strong> invalid result parse selected.'; unset( $this->data['orphans'] );	break;

					endswitch;

				endif;

			else :

				$this->data['error'] = '<strong>Sorry,</strong> an error occurred. ' . $_error;

			endif;

		endif;

		// --------------------------------------------------------------------------

		$this->data['page']->title = 'CDN: Find Orphaned Objects';

		// --------------------------------------------------------------------------

		$this->asset->load( 'nails.admin.utilities.cdn.orphans.min.js', TRUE );

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'admin/utilities/cdn/orphans',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}


	// --------------------------------------------------------------------------


	protected function _cdn_orphans_cli()
	{
		echo 'Sorry, this functionality is not complete yet. If you are experiencing timeouts please increase the timeout limit for PHP.';
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_UTILITIES' ) ) :

	class Utilities extends NAILS_Utilities
	{
	}

endif;


/* End of file utilities.php */
/* Location: ./modules/admin/controllers/utilities.php */