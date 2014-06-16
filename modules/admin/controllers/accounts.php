<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Admin: Accounts
 * Description:	Browse and edit user accounts
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

class NAILS_Accounts extends NAILS_Admin_Controller
{

	protected $accounts_group;
	protected $accounts_where;
	protected $accounts_columns;
	protected $accounts_actions;
	protected $accounts_sortfields;


	// --------------------------------------------------------------------------


	/**
	 * Announces this module's details to anyone who asks.
	 *
	 * @access	static
	 * @param	none
	 * @return	mixed
	 **/
	static function announce()
	{
		$d = new stdClass();

		// --------------------------------------------------------------------------

		//	Load the laguage file
		get_instance()->lang->load( 'admin_accounts', RENDER_LANG_SLUG );

		// --------------------------------------------------------------------------

		//	Configurations
		$d->name = lang( 'accounts_module_name' );

		// --------------------------------------------------------------------------

		//	Navigation options
		$d->funcs			= array();
		$d->funcs['index']	= lang( 'accounts_nav_index' );
		$d->funcs['create']	= lang( 'accounts_nav_create' );
		$d->funcs['groups']	= 'Manage User Groups';

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
	 * @return	array
	 **/
	static function notifications()
	{
		$_ci =& get_instance();
		$_notifications = array();

		// --------------------------------------------------------------------------

		$_notifications['index']			= array();
		$_notifications['index']['value']	= $_ci->db->count_all( NAILS_DB_PREFIX . 'user' );

		// --------------------------------------------------------------------------

		return $_notifications;
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns an array of extra permissions which can be specified
	 *
	 * @access	static
	 * @param	none
	 * @return	array
	 **/
	static function permissions()
	{
		$_permissions = array();

		// --------------------------------------------------------------------------

		//	Define some basic extra permissions
		$_permissions['can_login_as']		= 'Can log in as another user';
		$_permissions['can_edit_others']	= 'Can edit other users';
		$_permissions['can_create_group']		= 'Can create user groups';
		$_permissions['can_edit_group']			= 'Can edit user groups';
		$_permissions['can_delete_group']		= 'Can delete user groups';

		// --------------------------------------------------------------------------

		return $_permissions;
	}


	// --------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Defaults defaults
		$this->accounts_group		= FALSE;
		$this->accounts_where		= array();
		$this->accounts_columns		= array();
		$this->accounts_actions		= array();
		$this->accounts_sortfields	= array();

		// --------------------------------------------------------------------------

		$this->accounts_sortfields[] = array( 'label' => lang( 'accounts_sort_id' ),		'col' => 'u.id' );
		$this->accounts_sortfields[] = array( 'label' => lang( 'accounts_sort_group_id' ),	'col' => 'u.group_id' );
		$this->accounts_sortfields[] = array( 'label' => lang( 'accounts_sort_first' ),		'col' => 'u.first_name' );
		$this->accounts_sortfields[] = array( 'label' => lang( 'accounts_sort_last' ),		'col' => 'u.last_name' );
		$this->accounts_sortfields[] = array( 'label' => lang( 'accounts_sort_email' ),		'col' => 'ue.email' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Accounts homepage / dashboard
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function index()
	{
		//	Searching, sorting, ordering and paginating.
		$_hash = 'search_' . md5( uri_string() ) . '_';

		if ( $this->input->get( 'reset' ) ) :

			$this->session->unset_userdata( $_hash . 'per_page' );
			$this->session->unset_userdata( $_hash . 'sort' );
			$this->session->unset_userdata( $_hash . 'order' );

		endif;

		$_default_per_page	= $this->session->userdata( $_hash . 'per_page' ) ? $this->session->userdata( $_hash . 'per_page' ) : 50;
		$_default_sort		= $this->session->userdata( $_hash . 'sort' ) ? 	$this->session->userdata( $_hash . 'sort' ) : 'u.id';
		$_default_order		= $this->session->userdata( $_hash . 'order' ) ? 	$this->session->userdata( $_hash . 'order' ) : 'ASC';

		//	Define vars
		$_search			= array( 'keywords' => $this->input->get( 'search' ), 'columns' => array() );

		foreach ( $this->accounts_sortfields AS $field ) :

			$_search['columns'][strtolower( $field['label'] )] = $field['col'];

		endforeach;

		//	Add any other permenantly searchable fields here
		$_search['columns']['name']		= array( ' ', 'u.first_name', 'u.last_name' );
		$_search['columns']['gender']	= 'u.gender';

		$_limit		= array(
						$this->input->get( 'per_page' ) ? $this->input->get( 'per_page' ) : $_default_per_page,
						$this->input->get( 'offset' ) ? $this->input->get( 'offset' ) : 0
					);
		$_order		= array(
						$this->input->get( 'sort' ) ? $this->input->get( 'sort' ) : $_default_sort,
						$this->input->get( 'order' ) ? $this->input->get( 'order' ) : $_default_order
					);

		$this->accounts_group = ! empty( $this->accounts_group ) ? $this->accounts_group : $this->input->get( 'filter' );

		//	Set sorting and ordering info in session data so it's remembered for when user returns
		$this->session->set_userdata( $_hash . 'per_page', $_limit[0] );
		$this->session->set_userdata( $_hash . 'sort', $_order[0] );
		$this->session->set_userdata( $_hash . 'order', $_order[1] );

		//	Set values for the page
		$this->data['search']			= new stdClass();
		$this->data['search']->per_page	= $_limit[0];
		$this->data['search']->sort		= $_order[0];
		$this->data['search']->order	= $_order[1];

		// --------------------------------------------------------------------------

		//	Is a group set?
		if ( $this->accounts_group ) :

			$this->accounts_where['u.group_id'] = $this->accounts_group;

		endif;

		// --------------------------------------------------------------------------

		//	Get the accounts
		$this->data['users']		= new stdClass();
		$this->data['users']->data	= $this->user_model->get_all( FALSE, $_order, $_limit, $this->accounts_where, $_search );

		//	Work out pagination
		$this->data['users']->pagination				= new stdClass();
		$this->data['users']->pagination->total_results	= $this->user_model->count_all( $this->accounts_where, $_search );

		// --------------------------------------------------------------------------

		//	Override the title (used when loading this method from one of the other methods)
		$this->data['page']->title	 = ( ! empty( $this->data['page']->title ) ) ? $this->data['page']->title : lang( 'accounts_index_title' );

		if ( $_search['keywords'] ) :

			$this->data['page']->title	.= ' (' . lang( 'accounts_index_search_results', array( $_search['keywords'], number_format( $this->data['users']->pagination->total_results ) ) ) . ')';

		else :

			$this->data['page']->title	.= ' (' . number_format( $this->data['users']->pagination->total_results ) . ')';

		endif;

		// --------------------------------------------------------------------------

		//	Pass any columns and actions to the view
		$this->data['columns']		= $this->accounts_columns;
		$this->data['actions']		= $this->accounts_actions;
		$this->data['sortfields']	= $this->accounts_sortfields;

		// --------------------------------------------------------------------------

		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'admin/accounts/overview',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	// --------------------------------------------------------------------------


	public function create()
	{
		//	Page Title
		$this->data['page']->title = lang( 'accounts_create_title' );

		// --------------------------------------------------------------------------

		//	Attempt to create the new user account
		if ( $this->input->post() ) :

			$this->load->library( 'form_validation' );

			//	Set rules
			$this->form_validation->set_rules( 'group_id',			'',	'xss_clean|required|is_natural_no_zero' );
			$this->form_validation->set_rules( 'password',			'',	'xss_clean' );
			$this->form_validation->set_rules( 'send_activation',	'',	'xss_clean' );
			$this->form_validation->set_rules( 'temp_pw',			'',	'xss_clean' );
			$this->form_validation->set_rules( 'first_name',		'',	'xss_clean|required' );
			$this->form_validation->set_rules( 'last_name',			'',	'xss_clean|required' );

			if ( APP_NATIVE_LOGIN_USING == 'EMAIL' ) :

				$this->form_validation->set_rules( 'email',			'',	'xss_clean|required|valid_email|is_unique[' . NAILS_DB_PREFIX . 'user_email.email]' );

				if ( $this->input->post( 'username' ) ) :

					$this->form_validation->set_rules( 'username',	'',	'xss_clean|is_unique[' . NAILS_DB_PREFIX . 'user.username]' );

				else :

					$this->form_validation->set_rules( 'username',		'',	'xss_clean' );

				endif;

			elseif ( APP_NATIVE_LOGIN_USING == 'USERNAME' ) :

				$this->form_validation->set_rules( 'username',		'',	'xss_clean|required|valid_email|is_unique[' . NAILS_DB_PREFIX . 'user_email.email]' );

				if ( $this->input->post( 'email' ) ) :

					$this->form_validation->set_rules( 'email',		'',	'xss_clean|valid_email|is_unique[' . NAILS_DB_PREFIX . 'user_email.email]' );

				else :

					$this->form_validation->set_rules( 'email',		'',	'xss_clean' );

				endif;

			elseif ( APP_NATIVE_LOGIN_USING == 'BOTH' ) :

				if ( $this->input->post( 'email' ) ) :

					$this->form_validation->set_rules( 'email',		'',	'xss_clean|valid_email|is_unique[' . NAILS_DB_PREFIX . 'user_email.email]' );

				else :

					$this->form_validation->set_rules( 'email',		'',	'xss_clean' );

				endif;

				if ( $this->input->post( 'username' ) ) :

					$this->form_validation->set_rules( 'username',	'',	'xss_clean|is_unique[' . NAILS_DB_PREFIX . 'user.username]' );

				else :

					$this->form_validation->set_rules( 'username',		'',	'xss_clean' );

				endif;

			endif;

			//	Set messages
			$this->form_validation->set_message( 'required',			lang( 'fv_required' ) );
			$this->form_validation->set_message( 'is_natural_no_zero',	lang( 'fv_required' ) );
			$this->form_validation->set_message( 'valid_email',			lang( 'fv_valid_email' ) );
			$this->form_validation->set_message( 'is_unique',			lang( 'fv_email_already_registered' ) );

			//	Execute
			if ( $this->form_validation->run() ) :

				//	Success
				$_data				= array();
				$_data['group_id']	= (int) $this->input->post( 'group_id' );
				$_data['password']	= trim( $this->input->post( 'password' ) );

				if ( ! $_data['password'] ) :

					//	Password isn't set, generate one
					$_data['password'] = $this->user_password_model->generate_password();

				endif;

				if ( $this->input->post( 'email' ) ) :

					$_data['email'] = $this->input->post( 'email' );

				endif;

				if ( $this->input->post( 'username' ) ) :

					$_data['username'] = $this->input->post( 'username' );

				endif;

				$_data['first_name']		= $this->input->post( 'first_name' );
				$_data['last_name']			= $this->input->post( 'last_name' );
				$_data['temp_pw']			= string_to_boolean( $this->input->post( 'temp_pw' ) );
				$_data['inform_user_pw']	= TRUE;

				$_new_user = $this->user_model->create( $_data, string_to_boolean( $this->input->post( 'send_activation' ) ) );

				if ( $_new_user ) :

					//	Any errors happen? While the user can be created successfully other problems might happen along the way
					if ( $this->user_model->get_errors() ) :

						$_message  = '<strong>Please Note,</strong> while the user was created successfully, the following issues were encountered:';
						$_message .= '<ul><li>' . implode( '</li><li>', $this->user_model->get_errors() ) . '</li></ul>';

						$this->session->set_flashdata( 'message', $_message );

					endif;

					// --------------------------------------------------------------------------

					//	Add item to admin changelog
					$_name = '#' . number_format( $_new_user->id );

					if ( $_new_user->first_name ) :

						$_name .= ' ' . $_new_user->first_name;

					endif;

					if ( $_new_user->last_name ) :

						$_name .= ' ' . $_new_user->last_name;

					endif;

					_ADMIN_CHANGE_ADD( 'created', 'a', 'user', $_new_user->id,  $_name, 'admin/accounts/edit/' . $_new_user->id );

					// --------------------------------------------------------------------------

					$this->session->set_flashdata( 'success', '<strong>Success!</strong> A user account was created for <strong>' . $_new_user->first_name . '</strong>, update their details now.' );
					redirect( 'admin/accounts/edit/' . $_new_user->id );

				else :

					$this->data['error'] = '<strong>Sorry,</strong> there was an error when creating the user account:<br />&rsaquo; ' . implode( '<br />&rsaquo; ', $this->user_model->get_errors() );

				endif;

			else :

				$this->data['error'] = '<strong>Sorry,</strong> there was an error when creating the user account. ' . $this->user_model->last_error();

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Get the groups
		$this->data['groups'] = $this->user_group_model->get_all();

		// --------------------------------------------------------------------------

		//	Load views
		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'admin/accounts/create/index',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Edit an existing user account
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function edit()
	{
		//	Get the user's data; loaded early because it's required for the user_meta_cols
		//	(we need to know the group of the user so we can pull up the correct cols/rules)

		$_user = $this->user_model->get_by_id( $this->uri->segment( 4 ) );

		if ( ! $_user ) :

			$this->session->set_flashdata( 'error', lang( 'accounts_edit_error_unknown_id' ) );
			redirect( $this->input->get( 'return_to' ) );
			return;

		endif;

		//	Non-superusers editing superusers is not cool
		if ( ! $this->user_model->is_superuser() && user_has_permission( 'superuser', $_user ) ) :

			$this->session->set_flashdata( 'error', lang( 'accounts_edit_error_noteditable' ) );
			$_return_to = $this->input->get( 'return_to' ) ? $this->input->get( 'return_to' ) : 'admin/dashboard';
			redirect( $_return_to );
			return;

		endif;

		//	Is this user editing someone other than themselves? If so, do they have permission?
		if ( active_user( 'id' ) != $_user->id && ! user_has_permission( 'admin.accounts.can_edit_others' ) ) :

			$this->session->set_flashdata( 'error', lang( 'accounts_edit_error_noteditable' ) );
			$_return_to = $this->input->get( 'return_to' ) ? $this->input->get( 'return_to' ) : 'admin/dashboard';
			redirect( $_return_to );
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Load helpers
		$this->load->helper( 'date' );

		// --------------------------------------------------------------------------

		//	Load the user_meta_cols; loaded here because it's needed for both the view
		//	and the form validation

		$_user_meta_cols	= $this->config->item( 'user_meta_cols' );
		$_group_id			= $this->input->post( 'group_id' ) ? $this->input->post( 'group_id' ) : $_user->group_id;

		if ( isset( $_user_meta_cols[$_group_id] ) ) :

			$this->data['user_meta_cols'] = $_user_meta_cols[$_user->group_id];

		else :

			$this->data['user_meta_cols'] = NULL;

		endif;

		//	Set fields to ignore by default
		$this->data['ignored_fields'] = array();
		$this->data['ignored_fields'][] = 'id';
		$this->data['ignored_fields'][] = 'user_id';

		//	If no cols were found, DESCRIBE the user_meta table - where possible
		//	you should manually set columns, including datatypes

		if ( NULL === $this->data['user_meta_cols'] ) :

			$_describe = $this->db->query( 'DESCRIBE `' . NAILS_DB_PREFIX . 'user_meta`' )->result();
			$this->data['user_meta_cols'] = array();

			foreach ( $_describe AS $col ) :

				//	Always ignore some fields
				if ( array_search( $col->Field, $this->data['ignored_fields'] ) !== FALSE )
					continue;

				// --------------------------------------------------------------------------

				//	Attempt to detect datatype
				$_datatype	= 'string';
				$_type		= 'text';

				switch( strtolower( $col->Type ) ) :

					case 'text' :					$_type = 'textarea';	break;
					case 'date' :					$_datatype = 'date';	break;
					case 'tinyint(1) unsigned' :	$_datatype = 'bool';	break;

				endswitch;

				// --------------------------------------------------------------------------

				$this->data['user_meta_cols'][$col->Field] = array(
					'datatype'		=> $_datatype,
					'type'			=> $_type,
					'label'			=> ucwords( str_replace( '_', ' ', $col->Field ) )
				);

			endforeach;

		endif;

		// --------------------------------------------------------------------------

		//	Validate if we're saving, otherwise get the data and display the edit form
		if ( $this->input->post() ) :

			//	Load validation library
			$this->load->library( 'form_validation' );

			// --------------------------------------------------------------------------

			//	Define user table rules
			$this->form_validation->set_rules( 'username',					'',	'xss_clean|alpha_dash|min_length[2]|unique_if_diff[' . NAILS_DB_PREFIX . 'user.username.' . $this->input->post( 'username_orig' ) . ']' );
			$this->form_validation->set_rules( 'first_name',				'',	'xss_clean|required' );
			$this->form_validation->set_rules( 'last_name',					'',	'xss_clean|required' );
			$this->form_validation->set_rules( 'gender',					'',	'xss_clean|required' );
			$this->form_validation->set_rules( 'timezone',					'',	'xss_clean|required' );
			$this->form_validation->set_rules( 'date_format_date_id',		'',	'xss_clean|required' );
			$this->form_validation->set_rules( 'date_format_time_id',		'',	'xss_clean|required' );
			$this->form_validation->set_rules( 'language_id',				'',	'xss_clean|required' );
			$this->form_validation->set_rules( 'password',					'',	'xss_clean' );
			$this->form_validation->set_rules( 'temp_pw',					'',	'xss_clean' );
			$this->form_validation->set_rules( 'reset_security_questions',	'',	'xss_clean' );

			// --------------------------------------------------------------------------

			//	Define user_meta table rules
			foreach ( $this->data['user_meta_cols'] AS $col => $value ) :

				$_datatype	= isset( $value['datatype'] )	? $value['datatype'] : 'string';
				$_label		= isset( $value['label'] )		? $value['label'] : ucwords( str_replace( '_', ' ', $col ) );

				//	Some data types require different handling
				switch ( $_datatype ) :

					case 'date' :

						//	Dates must validate
						if ( isset( $value['validation'] ) ) :

							$this->form_validation->set_rules( $col, $_label, 'xss_clean|' . $value['validation'] . '|valid_date[' . $col . ']' );

						else :

							$this->form_validation->set_rules( $col, $_label, 'xss_clean|valid_date[' . $col . ']' );

						endif;

					break;

					// --------------------------------------------------------------------------

					case 'file' :
					case 'upload' :
					case 'string' :
					default :

						if ( isset( $value['validation'] ) ) :

							$this->form_validation->set_rules( $col, $_label, 'xss_clean|' . $value['validation'] );

						else :

							$this->form_validation->set_rules( $col, $_label, 'xss_clean' );

						endif;

					break;

				endswitch;

			endforeach;

			// --------------------------------------------------------------------------

			//	Set messages
			$this->form_validation->set_message( 'required',			lang( 'fv_required' ) );
			$this->form_validation->set_message( 'is_natural_no_zero',	lang( 'fv_required' ) );
			$this->form_validation->set_message( 'valid_date',			lang( 'fv_valid_date' ) );
			$this->form_validation->set_message( 'valid_datetime',		lang( 'fv_valid_datetime' ) );

			// --------------------------------------------------------------------------

			//	Data is valid; ALL GOOD :]
			if ( $this->form_validation->run( $this ) ) :

				//	Define the data var
				$_data = array();

				// --------------------------------------------------------------------------

				//	If we have a profile image, attempt to upload it
				if ( isset( $_FILES['profile_img'] ) && $_FILES['profile_img']['error'] != 4 ) :

					$_object = $this->cdn->object_replace( $_user->profile_img, 'profile-images', 'profile_img' );

					if ( $_object ) :

						$_data['profile_img'] = $_object->id;

					else :

						$this->data['upload_error']	= $this->cdn->get_errors();
						$this->data['error']		= lang( 'accounts_edit_error_profile_img' );

					endif;

				endif;

				// --------------------------------------------------------------------------

				if ( ! isset( $this->data['upload_error'] ) ) :

					//	Set basic data
					$_data['temp_pw']					= string_to_boolean( $this->input->post( 'temp_pw' ) );
					$_data['reset_security_questions']	= string_to_boolean( $this->input->post( 'reset_security_questions' ) );
					$_data['first_name']				= $this->input->post( 'first_name' );
					$_data['last_name']					= $this->input->post( 'last_name' );
					$_data['username']					= $this->input->post( 'username' );
					$_data['gender']					= $this->input->post( 'gender' );
					$_data['timezone']					= $this->input->post( 'timezone' );
					$_data['date_format_date_id']		= $this->input->post( 'date_format_date_id' );
					$_data['date_format_time_id']		= $this->input->post( 'date_format_time_id' );
					$_data['language_id']				= $this->input->post( 'language_id' );

					if ( $this->input->post( 'password' ) ) :

						$_data['password']	= $this->input->post( 'password' );

					endif;

					//	Set meta data
					foreach ( $this->data['user_meta_cols'] AS $col => $value ) :

						switch ( $value['datatype'] ) :

							case 'bool' :
							case 'boolean' :

								//	Convert all to boolean from string
								$_data[$col] = string_to_boolean( $this->input->post( $col ) );

							break;

							// --------------------------------------------------------------------------

							default :

								$_data[$col] = $this->input->post( $col );

							break;

						endswitch;

					endforeach;

					// --------------------------------------------------------------------------

					//	Update account
					if ( $this->user_model->update( $this->input->post( 'id' ), $_data ) ) :

						$_name = $this->input->post(  'first_name' ) . ' ' . $this->input->post( 'last_name' );
						$this->data['success'] = lang( 'accounts_edit_ok', array( title_case( $_name ) ) );

						// --------------------------------------------------------------------------

						//	Set Admin changelogs
						$_name = '#' . number_format( $this->input->post( 'id' ) );

						if ( $_data['first_name'] ) :

							$_name .= ' ' . $_data['first_name'];

						endif;

						if ( $_data['last_name'] ) :

							$_name .= ' ' . $_data['last_name'];

						endif;

						foreach ( $_data AS $field => $value ) :

							if ( isset( $_user->$field ) ) :

								_ADMIN_CHANGE_ADD( 'updated', 'a', 'user', $this->input->post( 'id' ),  $_name, 'admin/accounts/edit/' . $this->input->post( 'id' ), $field, $_user->$field, $value, FALSE );

							endif;

						endforeach;

						// --------------------------------------------------------------------------

						//	refresh the user object
						$_user = $this->user_model->get_by_id( $this->input->post( 'id' ) );

					//	The account failed to update, feedback to user
					else:

						$this->data['error'] = lang( 'accounts_edit_fail', implode( ', ', $this->user_model->get_errors() ) );

					endif;

				endif;

			//	Update failed for another reason
			else:

				$this->data['error'] = lang( 'fv_there_were_errors' );

			endif;

		endif;
		//	End POST() check

		// --------------------------------------------------------------------------

		//	Get the user's meta data
		if ( $this->data['user_meta_cols'] ) :

			$this->db->select( implode( ',', array_keys( $this->data['user_meta_cols'] ) ) );
			$this->db->where( 'user_id', $_user->id );
			$_user_meta = $this->db->get( NAILS_DB_PREFIX . 'user_meta' )->row();

		else :

			$_user_meta = array();

		endif;

		// --------------------------------------------------------------------------

		//	Get the user's email addresses
		$this->data['user_emails'] = $this->user_model->get_emails_for_user( $_user->id );

		// --------------------------------------------------------------------------

		$this->data['user_edit']	= $_user;
		$this->data['user_meta']	= $_user_meta;

		//	Page Title
		$this->data['page']->title = lang( 'accounts_edit_title', title_case( $_user->first_name . ' ' . $_user->last_name ) );

		//	Get the groups, timezones and languages
		$this->data['groups']		= $this->user_group_model->get_all();
		$this->data['timezones']	= $this->datetime_model->get_all_timezone_flat();
		$this->data['date_formats']	= $this->datetime_model->get_all_date_format_flat();
		$this->data['time_formats']	= $this->datetime_model->get_all_time_format_flat();
		$this->data['languages']	= $this->language_model->get_all_flat();

		//	Fetch any user uploads
		if ( module_is_enabled( 'cdn' ) ) :

			$this->data['user_uploads'] = $this->cdn->get_objects_for_user( $_user->id );

		endif;

		// --------------------------------------------------------------------------

		if ( active_user( 'id' ) == $_user->id ) :

			switch ( active_user( 'gender' ) ) :

				case 'male' :

					$this->data['notice'] = lang( 'accounts_edit_editing_self_m' );

				break;

				case 'female' :

					$this->data['notice'] = lang( 'accounts_edit_editing_self_f' );

				break;

				default :

					$this->data['notice'] = lang( 'accounts_edit_editing_self_u' );

				break;

			endswitch;

		endif;

		// --------------------------------------------------------------------------

		//	Load views
		if ( $this->input->get( 'inline' ) || $this->input->get( 'is_fancybox' ) ) :

			$this->data['header_override'] = 'structure/header/blank';
			$this->data['footer_override'] = 'structure/footer/blank';

		endif;

		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'admin/accounts/edit/index',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Suspend a user
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function suspend()
	{
		//	Get the user's details
		$_uid		= $this->uri->segment( 4 );
		$_user		= $this->user_model->get_by_id( $_uid );
		$_old_value = $_user->is_suspended;

		// --------------------------------------------------------------------------

		//	Non-superusers editing superusers is not cool
		if ( ! $this->user_model->is_superuser() && user_has_permission( 'superuser', $_user ) ) :

			$this->session->set_flashdata( 'error', lang( 'accounts_edit_error_noteditable' ) );
			redirect( $this->input->get( 'return_to' ) );
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Suspend user
		$this->user_model->suspend( $_uid );

		// --------------------------------------------------------------------------

		//	Get the user's details, again
		$_user		= $this->user_model->get_by_id( $_uid );
		$_new_value	= $_user->is_suspended;


		// --------------------------------------------------------------------------

		//	Define messages
		if ( ! $_user->is_suspended ) :

			$this->session->set_flashdata( 'error', lang( 'accounts_suspend_error', title_case( $_user->first_name . ' ' . $_user->last_name ) ) );

		else :

			$this->session->set_flashdata( 'success', lang( 'accounts_suspend_success', title_case( $_user->first_name . ' ' . $_user->last_name ) ) );

		endif;

		// --------------------------------------------------------------------------

		//	Update admin changelog
		_ADMIN_CHANGE_ADD( 'suspended', 'a', 'user', $_uid,  '#' . number_format( $_uid ) . ' ' . $_user->first_name . ' ' . $_user->last_name, 'admin/accounts/edit/' . $_uid, 'is_suspended', $_old_value, $_new_value, FALSE );

		// --------------------------------------------------------------------------

		redirect( $this->input->get( 'return_to' ) );
	}


	// --------------------------------------------------------------------------


	/**
	 * Unsuspends a user
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function unsuspend()
	{
		//	Get the user's details
		$_uid		= $this->uri->segment( 4 );
		$_user		= $this->user_model->get_by_id( $_uid );
		$_old_value	= $_user->is_suspended;

		// --------------------------------------------------------------------------

		//	Non-superusers editing superusers is not cool
		if ( ! $this->user_model->is_superuser() && user_has_permission( 'superuser', $_user ) ) :

			$this->session->set_flashdata( 'error', lang( 'accounts_edit_error_noteditable' ) );
			redirect( $this->input->get( 'return_to' ) );
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Unsuspend user
		$this->user_model->unsuspend( $_uid );

		// --------------------------------------------------------------------------

		//	Get the user's details, again
		$_user		= $this->user_model->get_by_id( $_uid );
		$_new_value	= $_user->is_suspended;

		// --------------------------------------------------------------------------

		//	Define messages
		if ( $_user->is_suspended ) :

			$this->session->set_flashdata( 'error', lang( 'accounts_unsuspend_error', title_case( $_user->first_name . ' ' . $_user->last_name ) ) );

		else :

			$this->session->set_flashdata( 'success', lang( 'accounts_unsuspend_success', title_case( $_user->first_name . ' ' . $_user->last_name ) ) );

		endif;

		// --------------------------------------------------------------------------

		//	Update admin changelog
		_ADMIN_CHANGE_ADD( 'unsuspended', 'a', 'user', $_uid,  '#' . number_format( $_uid ) . ' ' . $_user->first_name . ' ' . $_user->last_name, 'admin/accounts/edit/' . $_uid, 'is_suspended', $_old_value, $_new_value, FALSE );

		// --------------------------------------------------------------------------

		redirect( $this->input->get( 'return_to' ) );
	}


	// --------------------------------------------------------------------------


	/**
	 * Deletes a user
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 **/
	public function delete()
	{
		//	Get the user's details
		$_uid	= $this->uri->segment( 4 );
		$_user	= $this->user_model->get_by_id( $_uid );

		// --------------------------------------------------------------------------

		//	Non-superusers editing superusers is not cool
		if ( ! $this->user_model->is_superuser() && user_has_permission( 'superuser', $_user ) ) :

			$this->session->set_flashdata( 'error', lang( 'accounts_edit_error_noteditable' ) );
			redirect( $this->input->get( 'return_to' ) );
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Delete user
		$_user = $this->user_model->get_by_id( $_uid );

		if ( ! $_user ) :

			$this->session->set_flashdata( 'error', lang( 'accounts_edit_error_unknown_id' ) );
			redirect( $this->input->get( 'return_to' ) );
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Define messages
		if ( $this->user_model->destroy( $_uid ) ) :

			$this->session->set_flashdata( 'success', lang( 'accounts_delete_success', title_case( $_user->first_name . ' ' . $_user->last_name ) ) );

			//	Update admin changelog
			_ADMIN_CHANGE_ADD( 'deleted', 'a', 'user', $_uid,  '#' . number_format( $_uid ) . ' ' . $_user->first_name . ' ' . $_user->last_name );

		else :

			$this->session->set_flashdata( 'error', lang( 'accounts_delete_error', title_case( $_user->first_name . ' ' . $_user->last_name ) ) );

		endif;

		// --------------------------------------------------------------------------

		redirect( $this->input->get( 'return_to' ) );
	}


	// --------------------------------------------------------------------------


	public function delete_profile_img()
	{
		$_uid		= $this->uri->segment( 4 );
		$_user		= $this->user_model->get_by_id( $_uid );
		$_return_to	= $this->input->get( 'return_to' ) ? $this->input->get( 'return_to' ) : 'admin/accounts/edit/' . $_uid;

		// --------------------------------------------------------------------------

		if ( ! $_user ) :

			$this->session->set_flashdata( 'error', lang( 'accounts_delete_img_error_noid' ) );
			redirect( 'admin/accounts' );

		else :

			//	Non-superusers editing superusers is not cool
			if ( ! $this->user_model->is_superuser() && user_has_permission( 'superuser', $_user ) ) :

				$this->session->set_flashdata( 'error', lang( 'accounts_edit_error_noteditable' ) );
				redirect( $_return_to );
				return;

			endif;

			// --------------------------------------------------------------------------

			if ( $_user->profile_img ) :

				if ( $this->cdn->object_delete( $_user->profile_img, 'profile-images' ) ) :

					//	Update the user
					$_data = array();
					$_data['profile_img'] = NULL;

					$this->user_model->update( $_uid, $_data );

					// --------------------------------------------------------------------------

					$this->session->set_flashdata( 'success', lang( 'accounts_delete_img_success' ) );

				else :

					$this->session->set_flashdata( 'error', lang( 'accounts_delete_img_error', implode( '", "', $this->cdn->get_errors() ) ) );

				endif;

			else :

				$this->session->set_flashdata( 'notice', lang( 'accounts_delete_img_error_noimg' ) );

			endif;

			// --------------------------------------------------------------------------

			redirect( $_return_to );

		endif;
	}


	// --------------------------------------------------------------------------


	public function groups()
	{
		$_method = $this->uri->segment( 4 ) ? $this->uri->segment( 4 ) : 'index';

		if ( method_exists( $this, '_groups_' . $_method ) ) :

			$this->{'_groups_' . $_method}();

		else :

			show_404();

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _groups_index()
	{
		$this->data['page']->title = 'Manage User Groups';

		// --------------------------------------------------------------------------


		$this->data['groups'] = $this->user_group_model->get_all();

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'admin/accounts/groups/index',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}


	// --------------------------------------------------------------------------


	protected function _groups_create()
	{
		if ( ! user_has_permission( 'admin.accounts.can_create_group' ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		$this->session->set_flashdata( 'message', '<strong>Coming soon!</strong> The ability to dynamically create groups is on the roadmap.' );
		redirect( 'admin/accounts/groups' );
	}


	// --------------------------------------------------------------------------


	protected function _groups_edit()
	{
		if ( ! user_has_permission( 'admin.accounts.can_edit_group' ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		$_gid = $this->uri->segment( 5, NULL );

		$this->data['group'] = $this->user_group_model->get_by_id( $_gid );

		if ( ! $this->data['group'] ) :

			$this->session->set_flashdata( 'error', 'Group does not exist.' );
			redirect( 'admin/accounts/groups' );

		endif;

		// --------------------------------------------------------------------------

		if ( $this->input->post() ) :

			//	Load library
			$this->load->library( 'form_validation' );

			//	Define rules
			$this->form_validation->set_rules( 'slug',					'', 'xss_clean|unique_if_diff[' . NAILS_DB_PREFIX . 'user_group.slug.' . $this->data['group']->slug . ']' );
			$this->form_validation->set_rules( 'label',					'', 'xss_clean|required' );
			$this->form_validation->set_rules( 'description',			'', 'xss_clean|required' );
			$this->form_validation->set_rules( 'default_homepage',		'', 'xss_clean|required' );
			$this->form_validation->set_rules( 'registration_redirect',	'', 'xss_clean' );
			$this->form_validation->set_rules( 'acl[]',					'', 'xss_clean' );
			$this->form_validation->set_rules( 'acl[superuser]',		'', 'xss_clean' );
			$this->form_validation->set_rules( 'acl[admin]',			'', 'xss_clean' );
			$this->form_validation->set_rules( 'acl[admin][]',			'', 'xss_clean' );

			//	Set messages
			$this->form_validation->set_message( 'required', lang( 'fv_required' ) );
			$this->form_validation->set_message( 'required', lang( 'fv_unique_if_diff' ) );

			if ( $this->form_validation->run() ) :

				$_data							= array();
				$_data['slug']					= $this->input->post( 'slug' );
				$_data['label']					= $this->input->post( 'label' );
				$_data['description']			= $this->input->post( 'description' );
				$_data['default_homepage']		= $this->input->post( 'default_homepage' );
				$_data['registration_redirect']	= $this->input->post( 'registration_redirect' );

				//	Parse ACL's
				$_acl = $this->input->post( 'acl' );

				if ( isset( $_acl['admin'] ) ) :

					//	Remove ACLs which have no enabled methods - pointless
					$_acl['admin'] = array_filter( $_acl['admin'] );

				endif;

				$_data['acl'] = serialize( $_acl );

				if ( $this->user_group_model->update( $_gid, $_data ) ) :

					$this->session->set_flashdata( 'success', '<strong>Huzzah!</strong> Group updated successfully!' );
					redirect( 'admin/accounts/groups' );

				else :

					$this->data['error'] = '<strong>Sorry,</strong> I was unable to update the group. ' . $this->user_group_model->last_error();

				endif;

			else :

				$this->data['error'] = lang( 'fv_there_were_errors' );

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Page title
		$this->data['page']->title = lang( 'accounts_groups_edit_title', $this->data['group']->label );

		// --------------------------------------------------------------------------

		//	Load views
		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'admin/accounts/groups/edit',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
	}


	// --------------------------------------------------------------------------


	protected function _groups_delete()
	{
		if ( ! user_has_permission( 'admin.accounts.can_delete_group' ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		$this->session->set_flashdata( 'message', '<strong>Coming soon!</strong> The ability to delete groups is on the roadmap.' );
		redirect( 'admin/accounts/groups' );
	}


	// --------------------------------------------------------------------------


	protected function _groups_set_default()
	{
		if ( ! user_has_permission( 'admin.accounts.can_set_default_group' ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		if ( $this->user_group_model->set_as_default( $this->uri->segment( 5 ) ) ) :

			$this->session->set_flashdata( 'success', '<strong>Success!</strong> Group set as default successfully.' );

		else :

			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> I could not set that group as the default user group. ' . $this->user_group_model->last_error() );

		endif;
		redirect( 'admin/accounts/groups' );
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
 * CodeIgniter instantiate a class with the same name as the file, therefore
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_ACCOUNTS' ) ) :

	class Accounts extends NAILS_Accounts
	{
	}

endif;

/* End of file accounts.php */
/* Location: ./modules/admin/controllers/accounts.php */