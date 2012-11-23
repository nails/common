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
	protected $accounts_group;
	protected $accounts_where;
	
	
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
		
		// --------------------------------------------------------------------------
		
		//	Defaults defaults
		$this->accounts_group = FALSE;
		$this->accounts_where = array();
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
			
		//	Work out the limits
		$_per_page	= 25;
		$_page		= $this->uri->segment( 4, 0 );
		$_offset	= $_page * $_per_page;
		$_limit		= array(
						$_per_page,
						$_offset
					);
		$_order		= array(
						'u.id',
						'DESC'
					);
		
		// --------------------------------------------------------------------------
			
		//	Is a group set?
		if ( $this->accounts_group ) :
		
			$this->where['u.group_id'] = $this->group;
			
		else :
		
			$this->where = NULL;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Work out the total number of pages
		if ( $this->accounts_where ) :
		
			$this->db->where( $this->accounts_where );
			
			//	Join the user meta table in case we're searching in here
			$this->db->join( 'user_meta um', 'um.user_id = u.id' );
		
		endif;
		
		$this->data['total_pages'] = ceil( $this->db->count_all_results( 'user u' ) / $_per_page );
		
		// --------------------------------------------------------------------------
		
		//	Get the accounts
		$this->data['users'] = $this->user->get_users( FALSE, $_order, $_limit, $this->accounts_where, $_search );
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->nails->load_view( 'admin/structure/header',	'modules/admin/views/structure/header',		$this->data );
		$this->nails->load_view( 'admin/accounts/overview',	'modules/admin/views/accounts/overview',	$this->data );
		$this->nails->load_view( 'admin/structure/footer',	'modules/admin/views/structure/footer',		$this->data );
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
		//	Get the user's data; loaded early because it's required for the user_meta_cols
		//	(we need to know the group of the user so we can pull up the correct cols/rules)
		
		$_user = $this->user->get_user( $this->uri->segment( 4 ) );
		
		if ( ! $_user ) :
		
			$this->session->set_flashdata( 'error', 'Unknown user' );
			redirect( $return_to );
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
		$this->data['ignored_fields'][] = 'user_id';
		$this->data['ignored_fields'][] = 'referral';
		$this->data['ignored_fields'][] = 'referred_by';
		$this->data['ignored_fields'][] = 'first_name';
		$this->data['ignored_fields'][] = 'last_name';
		$this->data['ignored_fields'][] = 'profile_img';
		
		//	If no cols were found, DESCRIBE the user_meta table - where possible
		//	you should manually set columns, including datatypes
		
		if ( is_null( $this->data['user_meta_cols'] ) ) :
			
			$_describe = $this->db->query( 'DESCRIBE `user_meta`' )->result();
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
		
			$_post = $this->input->post();
			
			//	Load validation library
			$this->load->library( 'form_validation' );
			
			//	Define user table rules 
			$this->form_validation->set_rules( 'group_id',		'Account Type',	'xss_clean|required|is_natural_no_zero' );			
			$this->form_validation->set_rules( 'email',			'Email',		'xss_clean|required|valid_email|unique_if_diff[user.email.' . $_post['email_orig'] . ']' );
			$this->form_validation->set_rules( 'username',		'Username',		'xss_clean|alpha_dash|min_length[2]|unique_if_diff[user.username.' . $_post['username_orig'] . ']' );
			$this->form_validation->set_rules( 'first_name',	'First Name',	'xss_clean|required' );
			$this->form_validation->set_rules( 'last_name',		'Last Name',	'xss_clean|required' );
			$this->form_validation->set_rules( 'password',		'Password',		'xss_clean' );
			$this->form_validation->set_rules( 'temp_pw',		'Temp PW',		'xss_clean' );
			
			//	Define user_meta table rules
			foreach ( $this->data['user_meta_cols'] AS $col => $value ) :
			
				$_datatype	= isset( $value['datatype'] )	? $value['datatype'] :  'string';
				$_label		= isset( $value['label'] )		? $value['label'] : ucwords( str_replace( '_', ' ', $col ) );
				
				//	Some data types require different handling
				switch ( $_datatype ) :
				
					case 'date' :
					
						//	Dates must validate
						if ( isset( $value['validation'] ) ) :
						
							$this->form_validation->set_rules( $col . '_day', $_label, 'xss_clean|' . $value['validation'] . '|valid_date[' . $col . ']' );
							
						else :
						
							$this->form_validation->set_rules( $col . '_day', $_label, 'xss_clean|valid_date[' . $col . ']' );
						
						endif;
					
					break;
					
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
			$this->form_validation->set_message( 'required',			'This field is required.' );
			$this->form_validation->set_message( 'is_natural_no_zero',	'This field is required.' );
			
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
			if ( $this->form_validation->run( $this ) && $_admins ) :
			
				//	Define the data var
				$_data = array();
				
				// --------------------------------------------------------------------------
				
				//	If we have a profile image, attempt to upload it
				if ( isset( $_FILES['profile_img'] ) && $_FILES['profile_img']['error'] != 4 ) :
					
					$this->load->library( 'cdn' );
					
					$_options					= array();
					$_options['allowed_types']	= 'jpg|png|gif';
					$_options['max_size']		= 2097152;	//	2Mb
					
					$_filename = $this->cdn->replace( $_user->profile_img, 'profile-images', 'profile_img', $_options );
					
					if ( $_filename ) :
					
						$_data['profile_img'] = $_filename;
					
					else :
					
						$this->data['upload_error']	= $this->cdn->errors();
						$this->data['error']		= '<strong>Update error:</strong> There was a problem uploading the Profile Image.';
					
					endif;
						
				endif;
				
				// --------------------------------------------------------------------------
				
				if ( ! isset( $this->data['upload_error'] ) ) :
				
					//	Set `user` data
					$_data['group_id']		= $_post['group_id'];
					$_data['temp_pw']		= string_to_boolean( $_post['temp_pw'] );
					$_data['first_name']	= $_post['first_name'];
					$_data['last_name']		= $_post['last_name'];
					$_data['email']			= $_post['email'];
					$_data['username']		= $_post['username'];
					
					if ( $_post['password'] ) :
					
						$_data['password']	= $_post['password'];
					
					endif;
					
					//	Set `user_meta` data
					foreach ( $this->data['user_meta_cols'] AS $col => $value ) :
					
						switch ( $value['datatype'] ) :
						
							case 'date' :
							
								$_data[$col] = $_post[$col . '_year'] . '-' . $_post[$col . '_month'] . '-' . $_post[$col . '_day'];
							
							break;
							
							// --------------------------------------------------------------------------
							
							case 'bool' :
							case 'boolean' :
							
								//	Convert all to boolean from string
								$_data[$col] = string_to_boolean( $_post[$col] );
							
							break;
							
							// --------------------------------------------------------------------------
							
							default :
							
								$_data[$col] = $_post[$col];
								
							break;
							
						endswitch;
						
					endforeach;
					
					// --------------------------------------------------------------------------
					
					//	Update account
					if ( $this->user->update( $_post['id'], $_data ) ) :
						
						$this->data['success'] = '<strong>Success!</strong> Updated user ' . title_case( $_post['first_name'] . ' ' . $_post['last_name'] ) . ' (' . $_post['email'] . ')';	
						
						//	refresh the user object
						$_user = $this->user->get_user( $_post['id'] );
					
					//	The account failed to update, feedback to user
					else:
					
						$this->data['error'] = '<strong>Update error:</strong> There was a problem updating the user.';
						
					endif;
				
				endif;
				
			
			//	Update has failed, update will render the system admin-less
			elseif ( $_admins === FALSE ) :
			
				$this->data['error'] = '<strong>Update Failed:</strong> The update would leave the system without any amdinistrators.';
				
			//	Update failed for another reason
			else:
			
				$this->data['error'] = '<strong>Update error:</strong> There was a problem updating the user.';
				
			endif;
			
		endif;
		//	End POST() check
		
		// --------------------------------------------------------------------------
		
		//	Get the user's meta data
		if ( $this->data['user_meta_cols'] ) :
		
			$this->db->select( implode( ',', array_keys( $this->data['user_meta_cols'] ) ) );	
			$this->db->where( 'user_id', $_user->id );
			$_user_meta = $this->db->get( 'user_meta' )->row();
			
		else :
		
			$_user_meta = array();
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->data['user_edit']	= $_user;
		$this->data['user_meta']	= $_user_meta;
		$this->data['page']->title	= 'Edit User ('.title_case( $_user->first_name . ' ' . $_user->last_name ) . ')';
		
		//	Get the groups
		$this->data['groups']		= $this->user->get_groups_flat();
		
		// --------------------------------------------------------------------------
		
		$this->data['return_string']	= '?return_to=' . urlencode( $this->input->get( 'return_to' ) );
		$this->data['notice']			= active_user( 'id' ) == $_user->id ? '<strong>Hello there!</strong> You are currently editing your own account.' : FALSE;
		
		// --------------------------------------------------------------------------
		
		//	Load views
		$this->nails->load_view( 'admin/structure/header',		'modules/admin/views/structure/header',		$this->data );
		$this->nails->load_view( 'admin/accounts/edit/index',	'modules/admin/views/accounts/edit/index',	$this->data );
		$this->nails->load_view( 'admin/structure/footer',		'modules/admin/views/structure/footer',		$this->data );
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
		
		// --------------------------------------------------------------------------
		
		//	Get the user's details
		$_user = $this->user->get_user( $_uid );
		
		// --------------------------------------------------------------------------
		
		//	Define messages
		if ( $_user->active != 2 ) :
		
			$this->session->set_flashdata( 'error',		'<strong>Sorry,</strong> there was a problem banning ' . title_case( $_user->first_name . ' ' . $_user->last_name ) );
			
		else :
		
			$this->session->set_flashdata( 'success',	'<strong>Success!</strong> ' . title_case( $_user->first_name . ' ' . $_user->last_name ) . ' was banned successfully.' );
			
		endif;
		
		// --------------------------------------------------------------------------
		
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
		
		// --------------------------------------------------------------------------
		
		//	Get the user's details
		$_user = $this->user->get_user( $_uid );
		
		// --------------------------------------------------------------------------
		
		//	Define messages
		if ( $_user->active != 1 ) :
		
			$this->session->set_flashdata( 'error',		'<strong>Sorry,</strong> there was a problem unbanning ' . title_case( $_user->first_name . ' ' . $_user->last_name ) );
			
		else :
		
			$this->session->set_flashdata( 'success',	'<strong>Success!</strong> ' . title_case( $_user->first_name . ' ' . $_user->last_name ) . ' was unbanned successfully.' );
			
		endif;
		
		redirect( $this->input->get( 'return_to' ) );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function delete_profile_img()
	{
		$_uid = $this->uri->segment( 4 );
		$_user = $this->user->get_user( $_uid );
		
		// --------------------------------------------------------------------------
		
		if ( ! $_user ) :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> I was unable to find a user by that ID.' );
			redirect( 'admin/accounts' );
		
		else :
		
			if ( $_user->profile_img ) :
			
				$this->load->library( 'cdn' );
				
				if ( $this->cdn->delete( $_user->profile_img, 'profile-images' ) ) :
				
					//	Update the user
					$_data = array();
					$_data['profile_img'] = NULL;
					
					$this->user->update( $_uid, $_data );
					
					// --------------------------------------------------------------------------
					
					$this->session->set_flashdata( 'success', '<strong>Success!</strong> Profile image was deleted.' );
				
				else :
				
					$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> I was unable delete this user\'s profile image. The CDN said: "' . implode( '", "', $this->cdn->errors() ) . '"' );
				
				endif;
			
			else :
			
				$this->session->set_flashdata( 'notice', '<strong>Hey!</strong> This user doesn\'t have a profile image to delete.' );
			
			endif;
			
			// --------------------------------------------------------------------------
			
			redirect( 'admin/accounts/edit/' . $_uid );
		
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