<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Accounts
*
* Docs:			-
*
* Created:		14/10/2010
* Modified:		24/03/2011
*
* Description:	-
* 
*/


//	Include Admin_Controller; executes common admin functionality.
require_once '_admin.php';

class Accounts extends Admin_Controller {

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
		//	Configurations
		$d->name				= 'Members';					//	Display name.
		
		// --------------------------------------------------------------------------
		
		//	Navigation options
		$d->funcs['index']			= 'All Members';			//	Sub-nav function.
		
		// --------------------------------------------------------------------------
		
		//	Only announce the controller if the user has permisison to know about it
		$_acl = active_user( 'acl' );
		if ( ! isset( $_acl['admin'] ) || array_search( basename( __FILE__, '.php' ), $_acl['admin'] ) === FALSE )
			return NULL;
		
		// --------------------------------------------------------------------------
		
		//	Hey user! Pick me! Pick me!
		return $d;
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
		$this->data['page']->title		= ( ! empty( $this->data['page']->title ) ) ? $this->data['page']->title : 'Members';
		
		// --------------------------------------------------------------------------
		
		$_search = $this->input->get( 'search' );
		
		// --------------------------------------------------------------------------
			
		//	First lot of pagination data
		//	Done like this due to the double call to get_users() - need to apply conditionals.
		
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
		
		//	Load views
		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'accounts/overview',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Show all the interns
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function interns()
	{
		$this->data['page']->title = 'Members: Interns';
		
		//	Set intern group conditional
		$this->group = 2;
		
		//	Execute main logic
		$this->index();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Show all the employers
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function employers()
	{
		$this->data['page']->title = 'Members: Employer Managers';
		
		//	Set employer_manager group conditional
		$this->group = 3;
		
		//	Execute main logic
		$this->index();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Show all the employer team members
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function employers_team()
	{
		$this->data['page']->title = 'Members: Employer Team Members';
		
		//	Set intern group conditional
		$this->group = 5;
		
		//	Execute main logic
		$this->index();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Control which smart list function we're rendering
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function smart_lists()
	{
		$this->load->model( 'admin_smartlist_model' );
		
		switch( $this->uri->segment( 4 ) ) :
			
			case 'run':		$this->_smartlist_run();	break;
			case 'export':	$this->_smartlist_export();	break;
			case 'email':	$this->_smartlist_email();	break;
			case 'edit':	$this->_smartlist_edit();	break;
			case 'delete':	$this->_smartlist_delete();	break;
			case 'create':	$this->_smartlist_create();	break;
			
			//	Default to showing a list of all smart lists
			default:		$this->_smartlist_index();	break;
			
		endswitch;
		
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Smart List: List all smart lists
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	private function _smartlist_index()
	{
		//	Load all required models
		$this->load->model( 'sector_model' );
		$this->load->model( 'location_model' );
		$this->load->model( 'institution_model' );
			
		//	Get saved smart lists
		$this->data['smartlists'] 		= $this->admin_smartlist_model->get_all();
	
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'accounts/smartlists/index',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Smart List: Run search
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	private function _smartlist_run()
	{
		$this->data['smartlist']	= $this->admin_smartlist_model->get_smartlist( $this->uri->segment( 5 ) );
		$this->data['users']		= $this->admin_smartlist_model->get_users_for_smartlist( $this->data['smartlist']->id );
		
		$this->data['page']->download	= ( $this->input->get( 'download' ) !== FALSE ) ? TRUE : FALSE;
		
		if ( $this->data['page']->download ) :
		
			$this->load->view( 'accounts/download',	$this->data );
			
		else:
		
			//	Load views
			$this->load->view( 'structure/header',			$this->data );
			$this->load->view( 'accounts/smartlists/run',	$this->data );
			$this->load->view( 'structure/footer',			$this->data );
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Smart List: Send email to smart list
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	private function _smartlist_email()
	{
		//	Get data
		$this->data['smartlist']	= $this->admin_smartlist_model->get_smartlist( $this->uri->segment( 5 ) );
		
		//	Process email
		if ( $this->input->post() ) :
		
			//	Validate
			$this->load->library( 'form_validation' );
			$this->form_validation->set_rules( 'subject',	'Subject',		'xss_clean|required' );
			$this->form_validation->set_rules( 'body',		'Email Body',	'xss_clean|required' );
			
			if ( $this->form_validation->run() ) :
				
				//	Prep the data
				$this->load->library( 'emailer' );
				$subject	= strip_tags( $this->input->post( 'subject' ) );
				
				if ( $this->input->post( 'auto_html' ) ) :
				
					$this->load->library( 'typography' );
					$body = auto_typography( strip_tags( $this->input->post( 'body' ), '<a><b><i><u><strike><h1></h1><img>' ) );
					
				else :
				
					$body = strip_tags( $this->input->post( 'body' ), '<a><b><i><u><strike><h1></h1><img>' );
				
				endif;
				
				//	Get the users and queue up the email.
				$users = $this->admin_smartlist_model->get_users_for_smartlist( $this->data['smartlist']->id );
				foreach ( $users AS $u ) :
				
					$email			= NULL;
					$email->to		= $u->email;
					$email->type	= 'adhoc';
					$email->data	= array(
					
						//'login_url'				=> 'auth/login/with_hashes/' . md5( $u->user_id ) . '/' . md5( $u->password ),
						//'unsubscribe_url'			=> urlencode( 'intern/cv/email_prefs' ),
						'hide_signoff'				=> TRUE,
						'user_id'					=> $u->user_id,
						'user_password'				=> $u->password,
						'user_email'				=> $u->email,
						'user_first_name'			=> $u->first_name,
						'user_last_name'			=> $u->last_name,
						'email_body'				=> $body,
						'email_subject'				=> $subject
					);
					
					$this->emailer->queue( $email, 'instant' );
				
				endforeach;
				
				//	Redirect
				$this->session->set_flashdata( 'success', 'Email successfully sent to '.count($users).' matched members from the "'.$this->data['smartlist']->title.' smart list!"' );
				redirect( 'admin/accounts/smart_lists' );
				return;
				
			endif;
		
		endif;
		
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'accounts/smartlists/email',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Smart List: Export smart list
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	private function _smartlist_export()
	{
		//	Clearly TODO
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Smart List: Edit a smart list
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	private function _smartlist_edit()
	{
		//	Clearly TODO
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Smart List: Delete a smart list
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	private function _smartlist_delete()
	{
		if ( $this->admin_smartlist_model->delete( $this->uri->segment( 5 ) ) ) :
		
			$this->session->set_flashdata( 'success', 'Smart list deleted successfully.' );
			redirect( 'admin/accounts/smart_lists' );
		
		else :
		
			$this->session->set_flashdata( 'error', 'Unable to delete smart list.' );
			redirect( 'admin/accounts/smart_lists' );
			
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	/**
	 * Smart List: Create a new smart list
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	private function _smartlist_create()
	{
		if ( $this->input->post() ) :
		
			if ( $this->admin_smartlist_model->create() ) :
			
				$this->session->set_flashdata( 'success', 'Smart List created successfully' );
			
			else :
			
				$this->session->set_flashdata( 'success', 'There was a problem creating the Smart List' );
			
			endif;
			
			redirect( 'admin/accounts/smart_lists' );
			return;
		
		endif;
		
		//	Get data for view
		$this->data['tables']		= $this->admin_smartlist_model->get_tables();
		$this->data['cols']			= $this->admin_smartlist_model->get_columns( $this->data['tables'] );
		$this->data['operators']	= $this->admin_smartlist_model->get_operators();
		
		//	Load views
		$this->load->view( 'structure/header',				$this->data );
		$this->load->view( 'accounts/smartlists/create',	$this->data );
		$this->load->view( 'structure/footer',				$this->data );
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
			if ( $this->form_validation->run() && $_admins ) :here();
			
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
		
		//	Load views
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'accounts/edit/index',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Delete a user's profile image
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function delete_profile_img()
	{
		$_uid = $this->uri->segment( 4 );
		
		if ( $this->user->delete_profile_image( $_uid ) ) :
		
			$this->session->set_flashdata( 'success', 'Successfully Removed!' );
			
		else:
		
			$this->session->set_flashdata( 'error', 'Error!' );
			
		endif;
		
		redirect( 'admin/accounts/edit/' . $_uid );
	
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Delete a user's CV
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function delete_user_cv()
	{
	
		$_uid = $this->uri->segment( 4 );
		
		if ( $this->user->delete_cv( $_uid ) ) :
		
			$this->session->set_flashdata( 'success', 'Successfully Removed!' );
			
		else:
		
			$this->session->set_flashdata( 'error', 'Error!' );
			
		endif;
		
		redirect( 'admin/accounts/edit/' . $_uid );
	
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
	 * Helper func to direct actions to the right method
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	public function direct()
	{
		//	Return To var
		$return_to = $this->input->get( 'return_to' );
		$return_to = ( empty( $return_to ) ) ? 'admin/accounts' : $return_to;
		
		//	Check we have something to play with
		$ids = $this->input->post( 'user_list' );
		if ( $ids === FALSE ) :
			$this->session->set_flashdata( 'error', lang( 'direct_no_ids' ) );
			redirect( $return_to );
		else :
			
			if ( ! is_array( $ids ) ) :
				$this->session->set_flashdata( 'error', lang( 'direct_bad_data' ) );
				redirect( $return_to );
			else :
				
				//Woohoo! Let's do this
				$this->session->set_flashdata( 'ids', $ids );
				switch( $this->input->post( 'submit' ) ) :
					
					case lang( 'actions_edit' ) :	redirect( 'admin/accounts/edit/multiple?return_to='.urlencode( $return_to ) );		break;
					case lang('actions_delete') :	redirect( 'admin/accounts/delete/multiple?return_to='.urlencode( $return_to ) );	break;
					
					default:
						$this->session->set_flashdata( 'error', sprintf( lang( 'direct_unknown_action' ), $this->input->post( 'submit' ) ) );
						redirect( $return_to );
					break;
					
				endswitch;
			endif;
		endif;
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
	 
	 
	 public function sessions()
	 {
	 	switch ( $this->uri->segment( 4 ) ) :
	 	
	 		case 'force_logout' :	$this->_session_force_logout();	break;
	 		default :				$this->_session_index();		break;
	 	
	 	endswitch;
	 }
	 
	 
	 // --------------------------------------------------------------------------
	 
	 
	 public function rename_user_emails()
	 {
		if ( ENVIRONMENT == 'production' )
			show_error( '<strong>WHAT ARE YOU DOING?!</strong><br /><br />Don\'t run this method on production servers.' );
		
		// --------------------------------------------------------------------------
		
	 	//	This function makes all email addresses random@shedcollective.org. KEEP COMMENTED UNLESS TESTING!
 		if ( $this->input->post( 'go' ) ) :
 		
 			//	Load helper
 			$this->load->helper( 'string' );
 			
 			// --------------------------------------------------------------------------
 			
 			$this->db->where('group_id = 2 OR group_id = 3');
 			$users = $this->db->get( 'user' )->result();
 			
 			// --------------------------------------------------------------------------
 			
 			foreach( $users AS $u ) :
 			
 				$rand = random_string( 'alnum', 10 ) . '@gsdd.co.uk';
 				
 				$this->db->where( 'id', $u->id );
 				$this->db->update( 'user', array( 'email' => $rand ) );
 				
 			endforeach;
 			
 			echo 'Done.';
 		
 		else:
 		
 			echo 'Are you sure?! This is a destructive update.';
 			echo '<hr /><form method="post"><input type="submit" name="go" value="YES, I know what I\'m doing."></form>';
 		
 		endif;
	 }
	 
	 
	 // --------------------------------------------------------------------------
	 
	 
	 //	Updates the score of every user in the DB, use with caution on larger databases.
	 public function force_score_calculation()
	 {	
 		if ( $this->input->post( 'go' ) ) :
 			
 			$this->db->select( 'id' );
 			$this->db->where( 'group_id', 2 );
 			$users = $this->db->get( 'user' )->result();
 			
 			// --------------------------------------------------------------------------
 			
 			foreach( $users AS $u ) :
 			
 				$this->user->update_profile_score( $u->id );
 				
 			endforeach;
 			
 			echo 'Done.';
 		
 		else:
 		
 			echo 'This can take some time to complete. Please wait for the update to complete before leaving the page.';
 			echo '<hr /><form method="post"><input type="submit" name="go" value="Gotya. I know what I\'m doing."></form>';
 		
 		endif;
	 }
	 
	 
	 // --------------------------------------------------------------------------
	 
	 
	 private function _session_index()
	 {
	 	//	Get all sessions from DB
	 	$this->db->order_by( 'last_activity', 'DESC' );
	 	$this->data['sessions'] = $this->db->get( 'ci_sessions' )->result();
	 	
	 	// --------------------------------------------------------------------------
	 	
	 	//	Load libraries
	 	$this->load->library( 'user_agent' );
	 	
	 	// --------------------------------------------------------------------------
	 	
		//	Load views
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'accounts/session/index',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	 }
	 
	 
	 // --------------------------------------------------------------------------
	 
	 
	 private function _session_force_logout()
	 {
		//	Remove all sessions except our own
		$this->db->where( 'session_id !=', $this->session->userdata('session_id') );
		$this->db->delete( 'ci_sessions' );
		
		//	Clear everyone's remember me code except our own
		$this->db->set( 'remember_code', NULL );
		$this->db->where( 'id !=', active_user( 'id' ) );
		$this->db->update( 'user' );
		
		//	Redirect happy
		$this->session->set_flashdata( 'success', '<strong>Boom!</strong> All users have been logged out of the system. It\'s just you now. Bit lonely, innit?' );
		redirect( 'admin/accounts/sessions' );
	 }
}

/* End of file accounts.php */
/* Location: ./application/modules/admin/controllers/accounts.php */