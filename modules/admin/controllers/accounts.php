<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			Accounts
 *
 * Created:		14/10/2010
 * Modified:		24/03/2011
 *
 * Description:	-
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

class NAILS_Accounts extends Admin_Controller {

	private $group;
	
	
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
		
		//	Configurations
		$d->name				= 'Members';					//	Display name.
		
		// --------------------------------------------------------------------------
		
		//	Navigation options
		$d->funcs['index']			= 'View All Members';			//	Sub-nav function.
		$d->funcs['groups']			= 'Manage User Groups';		//	Sub-nav function.
		$d->funcs['user_access']	= 'Manage User Access';		//	Sub-nav function.

		
		// --------------------------------------------------------------------------
		
		//	Only announce the controller if the user has permisison to know about it
		return self::_can_access( $d, __FILE__ );
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
		
		//	Defaults defaults
		$this->group = FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Accounts homepage / dashboard
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function index()
	{
		//	Set method info
		$this->data['page']->admin_m	= 'index';
		
		//	Override the title (used when loading this method from one of the other methods)
		$this->data['page']->title		= ( ! empty( $this->data['page']->title ) ) ? $this->data['page']->title : 'View All Members';
		
		// --------------------------------------------------------------------------
		
		$_search = $this->input->get( 'search' );
		
		// --------------------------------------------------------------------------
			
		//	First lot of pagination data
		//	Done like this due to the double call to get_users() - need to apply conditionals.
		
		$_page						= new stdClass();
		$_page->order				= new stdClass();
		$_page->order->column		= ( $this->uri->segment( 4 ) !== FALSE )		? $this->uri->segment( 4 ) : 'u.id';
		$_page->order->direction	= ( $this->uri->segment( 5 ) !== FALSE )		? $this->uri->segment( 5 ) : 'desc';
		$_page->per_page			= 25;
		$_page->page				= $this->uri->segment( 6, 0 );
		$_page->offset				= $_page->page * $_page->per_page;
		
			//	Set some query helper data
			$_order = NULL;
			$_limit = NULL;
			$_where = NULL;
			
			//	Set Order
			$_order[0] = $_page->order->column;
			$_order[1] = $_page->order->direction;
			
			//	Set limits	
			$_limit[0] = $_page->per_page;
			$_limit[1] = $_page->offset;
			
			//	Is a group set?
			if ( $this->group )
				$_where['u.group_id'] = $this->group;
		
		//	Second lot of pagination data; no, no, nonono no, no, no there's no $_limit!
		//	http://toaty.co.uk/limits
		
		$_page->total				= count( $this->user->get_users( FALSE, $_order, NULL, $_where, $_search ) );
		$_page->num_pages			= ceil( $_page->total / $_page->per_page );
		
		$this->data['pagination']	= $_page;
		
		// --------------------------------------------------------------------------
		
		//	Get the accounts

		$this->data['users'] = $this->user->get_users( FALSE, $_order, $_limit, $_where, $_search );
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->nails->load_view( 'admin/structure/header',	'modules/admin/views/structure/header',		$this->data );
		$this->nails->load_view( 'admin/accounts/overview',	'modules/admin/views/accounts/overview',	$this->data );
		$this->nails->load_view( 'admin/structure/footer',	'modules/admin/views/structure/footer',		$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	

	/**
	 * Create a new user account
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function create()
	{
		//	Method details
		$this->data['page']->admin_m	= 'create';
		$this->data['page']->title		= lang( 'accounts_title_create' );
		
		//	Get data
		$this->data['groups']			= $this->groups_model->get_groups();
		$this->data['meta_structure']	= $this->auth->get_meta_structure();
		$this->data['user_structure']	= $this->auth->get_user_structure();
		
		//	Form validation if submitting
		if ( $this->input->post( 'save' ) ) :
		
			//	Load validation library
			$this->load->library( 'form_validation' );
			
			//	Define base rules common to all groups
			$this->form_validation->set_rules( 'group_id',		'Account Type',	'xss_clean|required|is_natural' );
			$this->form_validation->set_rules( 'email',			'email',		'xss_clean|required|valid_email|unique[user.email]' );
			$this->form_validation->set_rules( 'username',		'username',		'xss_clean|min_length[2]|alpha_dash|unique[user.username]' );
			
			$this->form_validation->set_rules( 'auto_password',	'auto_password',	'xss_clean' );
			if ( ! $this->input->post( 'auto_password' ) ) :
			
				$this->form_validation->set_rules( 'password',			'Password',			'xss_clean|required|matches[password_confirm]' );
				$this->form_validation->set_rules( 'password_confirm',	'Password confirm',	'xss_clean|required|matches[password]' );
				
			endif;
			
			//	Quickly run through meta fields and set a xss_clean rule so they appear again for set_value()
			foreach ( $this->data['meta_structure'] AS $r ) :
			
				if ( array_search( $r->Field, $this->data['user_structure'] ) !== FALSE )
					continue;
						
				$this->form_validation->set_rules( $r->Field, $r->Field, 'xss_clean' );
				
			endforeach;
			
			//	Change default messages
			$this->form_validation->set_message( 'is_natural',			lang( 'required_field' ) );
			$this->form_validation->set_message( 'required',			lang( 'required_field' ) );
			$this->form_validation->set_message( 'valid_email',			lang( 'valid_email' ) );
			$this->form_validation->set_message( 'alpha_dash',			lang( 'alpha_dash' ) );
			$this->form_validation->set_message( 'alpha_dash_noaccent',	lang( 'alpha_dash_noaccent' ) );
			$this->form_validation->set_message( 'matches',				lang( 'matches' ) );
			$this->form_validation->set_message( 'unique',				lang( 'unique' ) );
			
			if ( $this->form_validation->run() == TRUE ) :
			
				//	Data validated, create account - core fields
				$u = $this->input->post( 'username' );
				$e = $this->input->post( 'email' );
				
				//	Prep data
				$data['first_name']	= $this->input->post( 'first_name' );
				$data['last_name']	= $this->input->post( 'last_name' );
				$data['group_id']	= $this->input->post( 'group' );
				
				//	What's the password situation? Generating or specified?
				if ( ! $this->input->post( 'auto_password' ) ) :
				
					//	User specified password, use that
					$p = $this->input->post( 'password' );
					$update_temp_flag = FALSE;
					
				else :
				
					//	Randomly generated string
					$this->load->helper( 'string' );
					$p = random_string( 'alpha', 6 );
					//	Remind ourselves to update the temp flag on this user
					$update_temp_flag = TRUE;
					
				endif;
				
				//	Attempt to register, set 5th parameter to prevent the library
				//	sending the activation email, we'll do that manually later.
				$uid = $this->auth->register( $e, $p, $u, $data, FALSE );
				if ( $uid ) :
					
					$u = $this->auth->get_user( $uid );
					
					//	Did we generate the password ourselves? I can't remember...
					if ( $update_temp_flag === TRUE ) :
					
						//	We did! make sure the table reflects this
						$data['email']						= $e;
						$data['temp_pw']					= 1;
						$data['forgotten_password_code']	= $key = $this->auth->salt();
						$this->auth->update_user( $u->id, $data );
						
					endif;
					
					//	This is admin and we trust our admins, so activate the user by default
					$this->auth->activate( $uid );
					
					//	Load up custom email library, we'll need it!
					$this->load->library( 'emailer' );
					
					//	Send user their welcome email
					$data['to']			= $e;
					$data['subject']	= lang( 'email_subjects_welcome' );
					$data['template']	= 'admin_new_user/welcome';
					
					$data['data']['first_name']	= title_case( $data['first_name'] );
					$data['data']['email']		= $e;
					$data['data']['password']	= $p;
					$data['data']['admin_name']	= title_case( $this->auth->get_user()->first_name.' '.$this->auth->get_user()->last_name );
					
					$e = $this->emailer->send( $data );
					
					if ( $e === FALSE )
						$this->session->set_flashdata( 'message', sprintf( lang( 'user_created_ok_noemail' ), $p ) );
					
					//	Redirect...
					$this->session->set_flashdata( 'success', sprintf( lang( 'user_created_ok' ), title_case( $data['first_name'].' '.$data['last_name'] ).' ('.$this->input->post( 'email' ).')' ) );
					redirect( $this->admin_model->admin_module_name.'/accounts/' );
					return;
				
				else :
				
					log_message( 'error', $this->auth->errors() );
					
				endif;
			
			endif;
		
		endif;
		
		
		//	Load views
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'accounts/create',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	

	/**
	 * Edit an existing user account
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function edit()
	{	
		//	Return To var
		$return_to = $this->input->get( 'return_to' );
		$return_to = ( empty( $return_to ) ) ? 'admin/accounts' : $return_to;
		
		// --------------------------------------------------------------------------
		
		//	Validate if we're saving, otherwise get the data and display the edit form
		if ( $this->input->post() ) :
		
			$_post = $this->input->post();
		
			//	Load validation library
			$this->load->library( 'form_validation' );
			
			//	Define rules for each user being updated
			$this->form_validation->set_rules( 'group_id',	'Account Type',	'xss_clean|required|is_natural' );			
			$this->form_validation->set_rules( 'email',		'Email',		'xss_clean|required|valid_email|unique_if_diff[user.email.' . $_post['email_orig'] . ']' );
			$this->form_validation->set_rules( 'username',	'Username',		'xss_clean|required|alpha_dash|min_length[2]|unique_if_diff[user.username.' . $_post['username_orig'] . ']' );
			
			//	Define rules for password banter
			$this->form_validation->set_rules( 'reset_pass',	'reset password',		'xss_clean' );
			$this->form_validation->set_rules( 'temp_pw',		'temporary password',	'xss_clean' );
			if ( isset( $u['reset_pass'] ) ) :
			
				$this->form_validation->set_rules( 'password',	'Password',		'xss_clean|required' );
				
			endif;
			
			// --------------------------------------------------------------------------
			
			//	Will there be any admins left after this update?
			//	If current update is either super users or admin then no DB check is nessecary
			if ( $_post['group_id'] != 1 && $_post['group_id'] != 2 ) :
			
				$this->db->where( 'group_id', 1 );
				$this->db->or_where( 'group_id', 2 );
				$_admins = ( $this->db->count_all_results( 'user' ) ) ? TRUE : FALSE;
				
			else :
			
				$_admins = TRUE;
				
			endif;
			
			// --------------------------------------------------------------------------
			
			//	Data is valid and there'll be some form of admin after the update; ALL GOOD :]
			if ( $this->form_validation->run() && $_admins ) :
			
				//	Attempt update, unset some helper info...
				$_uid = $_post['id'];
				unset( $_post['id'] );
				unset( $_post['email_orig'] );
				unset( $_post['username_orig'] );
				
				//	Password fun
				if ( isset( $_post['reset_pass'] ) && ! empty( $_post['password'] ) ) :
				
					//	We are resetting the password
					unset( $_post['reset_pass'] );
					$pw_reset = TRUE;	//	Set a flag to remind ourselves to inform the user
					
				else :
				
					//	Leave the password alone
					unset( $_post['password'] );
					unset( $_post['temp_pw'] );
					unset( $_post['reset_pass'] );
					$pw_reset = FALSE;
					
				endif;
				
				//	Quickly unset some of the readonly data
				unset( $_post['auth_method'] );
				unset( $_post['created_on'] );
				unset( $_post['last_login'] );
				unset( $_post['ip_address'] );
				unset( $_post['last_ip'] );
				unset( $_post['auth_token'] );
				unset( $_post['package_id'] );
				unset( $_post['package_name'] );
				unset( $_post['employer_name'] );
				unset( $_post['referred_by'] );
				
				//	The account has been updated!
				if ( $this->user->update( $_uid, $_post ) ) :
					
					//	If we are resetting the password we should probably tell the user...
					if ( $pw_reset ) :
						show_error( 'Account has been saved but no password reset email has been sent to the user due to incomplete method. Using other means ensure to tell them that their new password is: ' . $_post['password'] );
						//	Load up custom email library, we'll need it!
						$this->load->library( 'emailer' );
						
						//	Send user their password reset email
						$data['to']					= $_post['email'];
						$data['subject']			= lang( 'email_subjects_password' );
						$data['template']			= 'admin_edit_user/pw_reset';
						
						$data['data']['first_name']	= title_case( $_post['first_name'] );
						$data['data']['email']		= $_post['email'];
						$data['data']['password']	= $_post['password'];
						$data['data']['admin_name']	= active_user( 'first_name,last_name' );
						
						$e = $this->emailer->send( $data );
						
					endif;
					
					$this->session->set_flashdata( 'success', lang( 'user_edit_ok', title_case( $_post['first_name'] . ' ' . $_post['last_name'] ) . ' (' . $_post['email'] . ')' ) );
									
					//	All done? Send user on their way
					redirect( $return_to );
				
				
				//	The account failed to update, feedback to user
				else:
				
					$this->session->set_flashdata( 'error', sprintf( lang( 'user_edit_fail' ), title_case( $u['first_name'] . ' ' . $u['last_name'] ) . ' (' . $u['email'] . ')' ) );
					
				endif;
				
			
			//	Update has failed, update will render the system admin-less
			elseif ( $_admins === FALSE ) :
			
				$this->data['error'] = lang( 'user_edit_no_admins' );
				
			//	Update failed for another reason
			else:
			
				$this->data['error'] = '<strong>Update error.</strong> There was a problem updating the user.';
				
			endif;
			
		endif;
		//	End POST() check
		
		// --------------------------------------------------------------------------
		
		
		//	Get the user's data
		$_user = $this->user->get_user( $this->uri->segment( 4 ) );
		
		if ( ! $_user ) :
		
			$this->session->set_flashdata( 'error', 'Unknown user' );
			redirect( $return_to );
		
		endif;
		
		$this->data['user_edit']	= $_user;
		$this->data['page']->title	= lang( 'accounts_title_edit' ).' ('.title_case( $_user->first_name . ' ' . $_user->last_name ) . ')';
		
		//	Get the groups
		$this->data['groups']		= $this->user->get_groups();
		
		
		// --------------------------------------------------------------------------
		
		
		$this->data['return_string']  = '?return_to=' . urlencode( $this->input->get( 'return_to' ) );
		
		$this->data['notice']	= ( active_user( 'id' ) == $_user->id ) ? lang( 'account_edit_thisisyou' ) : FALSE;
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->nails->load_view( 'admin/structure/header',		'modules/admin/views/structure/header',		$this->data );
		$this->nails->load_view( 'admin/accounts/edit/index',	'modules/admin/views/accounts/edit/index',	$this->data );
		$this->nails->load_view( 'admin/structure/footer',		'modules/admin/views/structure/footer',		$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Delete a user account
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function delete()
	{
		//	Method details
		$this->data['page']->admin_m = "delete";
		
		//	Return To var
		$return_to = $this->input->get( 'return_to' );
		$return_to = ( empty( $return_to ) ) ? 'admin/accounts' : $return_to;
		
		//	Get users...
		$users = $this->accounts_model->get_users( $return_to );
		
		//	Decide what to do
		if ( $this->uri->segment( 5 ) == 'confirm' ) :	
			
			//	Check we aint doing anything stupid.
			$ok_ids = array();
			foreach ( $users AS $user_id ) :
			
				$u = $this->auth->get_user( $user_id['id'] );
				
				if ( $u->id == $this->auth->get_user()->id ) :
				
					if ( count( $users ) > 1 ) :
						$this->session->set_flashdata( 'error', lang( 'user_no_suicide_multiple' ) );
					else :
						$this->session->set_flashdata( 'error', lang( 'user_no_suicide' ) );
					endif;
					redirect( $return_to );
					
				elseif ( ! $u ) :
				
					if ( count( $users ) > 1 ) :
						$this->session->set_flashdata( 'error', lang( 'unknown_user_id_multiple' ) );
					else :
						$this->session->set_flashdata( 'error', lang( 'unknown_user_id' ) );
					endif;
					redirect( $return_to );
					
				else :
					
					// Let's do this thing
					$ok_ids[] = $u->id;
					
				endif;
				
			endforeach;
			
			//	Will there be any admins left after this delete?
			$admins = FALSE;
			$ids	= array();
			foreach( $users AS $u ) :
				$ids[] = $u['id'];
			endforeach;
			
			//	See if there are any others in the database.
			if ( $this->accounts_model->admins_left( $ids ) ) :
				$admins = TRUE;
			endif;
				
			if ( $admins === FALSE ) :
				$this->session->set_flashdata( 'error', lang( 'user_edit_no_admins' ) );
				redirect( $return_to );
			endif;
			
			//	If it's just one user get their details (for displaying in success message)
			if ( count( $ok_ids ) == 1 ) :
				$u = $this->auth->get_user( $ok_ids[0] );
			endif;
			
			//	Delete all our users
			$fail	= array();
			$pass	= array();
			$total	= count( $ok_ids );
			
			foreach ( $ok_ids AS $id ) :
			
				if ( $this->auth->delete_user( $id ) ) :
					$pass[] = $id;
				else :
					$fail[] = $id;
				endif;
				
			endforeach;
			
			//	Redirect to overview
			if ( count( $pass ) == $total ) :
				
				//	All accounts were deleted
				if ($total == 1) :
					$this->session->set_flashdata( 'success', sprintf( lang( 'user_delete_ok' ), title_case( $u->first_name . ' ' . $u->last_name ) . ' (' . $u->email . ')' ) );
				else :
					$this->session->set_flashdata( 'success', sprintf( lang( 'user_delete_ok_multiple_all' ) ) );
				endif;
				
			elseif ( count( $fail ) == $total ) :
			
				//	All accounts failed to delete
				if ( $total == 1 ) :
					$this->session->set_flashdata( 'error', sprintf( lang( 'user_delete_fail' ) ) );
				else :
					$this->session->set_flashdata( 'error', sprintf( lang( 'user_delete_fail_multiple_all' ) ) );
				endif;
				
			else :
			
				//	Some deleted, some didn't, set appropriate messages
				$fail = implode( ', ', $fail );
				$pass = implode( ', ', $pass );
				$this->session->set_flashdata( 'success',	sprintf( lang( 'user_delete_ok_multiple_some' ),	$pass ) );
				$this->session->set_flashdata( 'error',		sprintf( lang( 'user_delete_fail_multiple_some' ),	$fail ) );
				
			endif;
	
			//	All done? Send user on their way
			redirect( $return_to );
		
		else :
			
			$error = false;

			foreach( $users AS $user ) :
			
				//	Get this user's details
				$u = $this->auth->get_user( $user['id'] );
				
				//	Basic validation
				if ( $u->id == $this->auth->get_user()->id ) :
					if ( count( $users ) > 1 ) :
						$this->session->set_flashdata( 'error', lang( 'user_no_suicide_multiple' ) );
					else:
						$this->session->set_flashdata( 'error', lang( 'user_no_suicide' ) );
					endif;
					redirect( $return_to );
				elseif ( ! $u ) :
					if ( count( $users ) > 1 ) :
						$this->session->set_flashdata( 'error', lang( 'unknown_user_id_multiple' ) );
					else:
						$this->session->set_flashdata( 'error', lang( 'unknown_user_id' ) );
					endif;
					redirect( $return_to );
				else :
					$this->data['users'][] = $u;
				endif;
			endforeach;

		endif;
		
		if ( count( $users ) > 1 ) :
			$this->data['page']->title		= lang( 'accounts_title_delete_plural' );
		else: 
			$this->data['page']->title		= lang( 'accounts_title_delete' ) . ' (' . title_case( $u->first_name . ' ' . $u->last_name ) . ')';
		endif;
		
		//	Load views
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'accounts/delete',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Forecully activate a user
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function activate()
	{
		//	Activate user
		$activated = $this->user->activate( $this->uri->segment( 4 ) );
		
		//	Get the user's details
		$u = $this->user->get_user( $this->uri->segment( 4 ) );
		
		//	Define messages
		if ($activated === FALSE) :
			$this->session->set_flashdata( 'error',		sprintf( lang( 'action_activate_fail' ),	title_case( $u->first_name . ' ' . $u->last_name ) ) );
		else :
			$this->session->set_flashdata( 'success',	sprintf( lang( 'action_activate_ok' ),		title_case( $u->first_name . ' ' . $u->last_name ) ) );
		endif;
		
		redirect( $this->input->get( 'return_to' ) );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Forecully deactivate a user
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function deactivate()
	{
		//	Deactivate user
		$deactivated = $this->auth->deactivate( $this->uri->segment( 4 ) );
		
		//	Get the user's details
		$u = $this->auth->get_user($this->uri->segment(4));
		
		//	Define messages
		if ($deactivated === FALSE) :
			$this->session->set_flashdata( 'error',		sprintf( lang( 'action_deactivate_fail' ),	title_case( $u->first_name . ' ' . $u->last_name ) ) );
		else :
			$this->session->set_flashdata( 'success',	sprintf( lang( 'action_deactivate_ok' ),	title_case( $u->first_name . ' ' . $u->last_name ) ) );
		endif;
		
		redirect( $this->input->get( 'return_to' ) );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Ban a user
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function ban()
	{
		//	Ban user
		$_uid = $this->uri->segment( 4 );
		$this->user->ban( $_uid );
		
		//	Get the user's details
		$_user = $this->user->get_user( $_uid );
		
		//	Define messages
		if ( $_user->active != 2 ) :
		
			$this->session->set_flashdata( 'error',		lang( 'action_ban_fail', title_case( $_user->first_name . ' ' . $_user->last_name ) ) );
			
		else :
		
			$this->session->set_flashdata( 'success',	lang( 'action_ban_ok', title_case( $_user->first_name . ' ' . $_user->last_name ) ) );
			
		endif;
		
		redirect( $this->input->get( 'return_to' ) );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Unbans a user
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function unban()
	{
		//	Unan user
		$_uid = $this->uri->segment( 4 );
		$this->user->unban( $_uid );
		
		//	Get the user's details
		$_user = $this->user->get_user( $_uid );
		
		//	Define messages
		if ( $_user->active != 1 ) :
		
			$this->session->set_flashdata( 'error',		lang( 'action_unban_fail', title_case( $_user->first_name . ' ' . $_user->last_name ) ) );
			
		else :
		
			$this->session->set_flashdata( 'success',	lang( 'action_unban_ok', title_case( $_user->first_name . ' ' . $_user->last_name ) ) );
			
		endif;
		
		redirect( $this->input->get( 'return_to' ) );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function groups()
	{
		//	Set method info
		$this->data['page']->admin_m	= 'groups';
		$this->data['page']->title		= 'Manage User Groups';
		
		// --------------------------------------------------------------------------
		
		$this->nails->load_view( 'admin/structure/header',	'modules/admin/views/structure/header',	$this->data );
		$this->load->view( 'admin/coming_soon',		$this->data );
		$this->nails->load_view( 'admin/structure/footer',	'modules/admin/views/structure/footer',	$this->data );
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
		//	Set method info
		$this->data['page']->admin_m	= 'edit_groups';
		$this->data['page']->title		= 'Edit Group';
		
		// --------------------------------------------------------------------------
		
		$_gid = $this->uri->segment( 4, NULL );
		
		// --------------------------------------------------------------------------
		
		if ( $this->input->post() ) :
		
			$this->load->library( 'form_validation' );
			
			$this->form_validation->set_rules( 'display_name',			'Display Name',		'xss_clean|required' );
			$this->form_validation->set_rules( 'name',					'Slug',				'xss_clean|required' );
			$this->form_validation->set_rules( 'description',			'Description',		'xss_clean|required' );
			$this->form_validation->set_rules( 'default_homepage',		'Default Homepage', 'xss_clean|required' );
			$this->form_validation->set_rules( 'acl[]',					'Permissions', 		'xss_clean' );
			$this->form_validation->set_rules( 'acl[superuser]',		'Permissions', 		'xss_clean' );
			$this->form_validation->set_rules( 'acl[admin]',			'Permissions', 		'xss_clean' );
			$this->form_validation->set_rules( 'acl[intern]',			'Permissions', 		'xss_clean' );
			$this->form_validation->set_rules( 'acl[employer_manager]',	'Permissions', 		'xss_clean' );
			$this->form_validation->set_rules( 'acl[employer_team]',	'Permissions', 		'xss_clean' );
			$this->form_validation->set_rules( 'acl[admin][]',			'Permissions', 		'xss_clean' );
			
			if ( $this->form_validation->run() ) :
			
				$_data = array();
				$_data['display_name']		= $this->input->post( 'display_name' );
				$_data['name']				= $this->input->post( 'name' );
				$_data['description']		= $this->input->post( 'description' );
				$_data['default_homepage']	= $this->input->post( 'default_homepage' );
				$_data['acl']				= serialize( $this->input->post( 'acl' ) );
				
				$this->user->update_group( $_gid, $_data );
				
				$this->session->set_flashdata( 'success', '<strong>Huzzah!</strong> Group updated successfully!' );
				redirect( 'admin/accounts/user_access' );
				return;
				
			else :
			
				$this->data['error'] = validation_errors();
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->data['group'] = $this->user->get_group( $_gid );
		
		if ( ! $this->data['group'] ) :
		
			$this->session->set_flashdata( 'error', 'Group does not exist.' );
			redirect( 'admin/accounts/user_access' );
		
		endif;
		
		$this->data['admin_modules'] = $this->_loaded_modules;
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->nails->load_view( 'admin/structure/header',		'modules/admin/views/structure/header',		$this->data );
		$this->nails->load_view( 'admin/accounts/edit_group',	'modules/admin/views/accounts/edit_group',	$this->data );
		$this->nails->load_view( 'admin/structure/footer',		'modules/admin/views/structure/footer',		$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Manage suer ACL's
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Gary
	 **/
	public function user_access()
	{
		//	Set method info
		$this->data['page']->admin_m	= 'user_access';
		$this->data['page']->title		= 'Manage User Access';
		
		// --------------------------------------------------------------------------
		
		$this->data['groups'] = $this->user->get_groups();
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->nails->load_view( 'admin/structure/header',		'modules/admin/views/structure/header',		$this->data );
		$this->nails->load_view( 'admin/accounts/user_access',	'modules/admin/views/accounts/user_access',	$this->data );
		$this->nails->load_view( 'admin/structure/footer',		'modules/admin/views/structure/footer',		$this->data );
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
 * CodeIgniter  instanciate a class with the same name as the file, therefore
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
 
if ( ! defined( 'NAILS_ALLOW_EXTENSION_ACCOUNTS' ) ) :

	class Accounts extends NAILS_Accounts
	{
	}

endif;

/* End of file accounts.php */
/* Location: ./application/modules/admin/controllers/accounts.php */