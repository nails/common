<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		User_Model
 *
 * Description:	The user model contains all methods for interacting and
 *				querying the active user. It also contains functionality for
 *				interfacing with the database with regards user accounts.
 * 
 **/

class CORE_NAILS_User_Model extends NAILS_Model
{

	public $active_user;
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Initialise the generic user model
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	public function init()
	{
		//	Do we need to pull up the data of a remembered user?
		$this->_login_remembered_user();
		
		// --------------------------------------------------------------------------
		
		//	Refresh user's session
		$this->_refresh_session();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Checks for the rememebred user cookies, if found we need to tell the
	 * user_model class to set the data when it instanciates.
	 *
	 * @access	static
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	public function find_remembered_user()
	{
		$_ci =& get_instance();
		
		// --------------------------------------------------------------------------
		
		//	User is already logged in, nothing to do.
		if ( (bool) $_ci->session->userdata( 'email' ) )
			return;
		
		// --------------------------------------------------------------------------
			
		//	Look for a cookie
		$_ci->load->helper( 'cookie' );
		$_email	= get_cookie( 'email' );
		$_code	= get_cookie( 'remember_code' );
		
		// --------------------------------------------------------------------------
		
		//	If we're missing anything then there's nothing to do
		if ( ! $_email || ! $_code )
			return;
		
		// --------------------------------------------------------------------------
			
		//	User cookie's were found
		define( 'LOGIN_REMEMBERED_USER', $_email . '|' . $_code );
		
		// --------------------------------------------------------------------------
		
		return;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Log in a previously logged in user
	 *
	 * @access	private
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	private function _login_remembered_user()
	{
		//	Only attempt to log in a user if they are remembered.
		//	This constant is set in User_Model::find_remembered_user();
		
		if ( ! defined( 'LOGIN_REMEMBERED_USER' ) || ! LOGIN_REMEMBERED_USER )
			return;
		
		// --------------------------------------------------------------------------
		
		//	Get the credentials from the constant set earlier
		list( $_email, $_code ) = explode( '|', LOGIN_REMEMBERED_USER );
		
		// --------------------------------------------------------------------------
		
		//	Look up the user so we can cross-check the codes
		$_u = $this->get_user_by_email( $_email, TRUE );
		
		if ( $_u && $_code === $_u->remember_code ) :
		
			//	User was validated, log them in!
			$this->update_last_login( $_u->id );
			$this->set_login_data( $_u->id, $_u->email, $_u->group_id );
			
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetches a value from the active user's session data; done this way so
	 * that interfacing with active user data is consistent.
	 *
	 * @access	public
	 * @param	string	$keys		The key to look up in userdata
	 * @param	string	$delimiter	If multiple fields are requested they'll be joined by this string
	 * @return	mixed
	 * @author	Pablo
	 * 
	 **/
	public function active_user( $keys = FALSE, $delimiter = ' '  )
	{
		//	Only look for a value if we're logged in
		if ( ! $this->is_logged_in() )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		//	If $keys is FALSE just return the user object in its entirety
		if ( $keys === FALSE )
			return $this->active_user; 
		
		// --------------------------------------------------------------------------
		
		//	Only stitch items together if we have more than one key
		if ( strpos( $keys, ',' ) === FALSE ) :
			
			$_val = ( isset( $this->active_user->{$keys} ) ) ? $this->active_user->{$keys} : FALSE;
			
			//	If something is found, then use that
			if ( $_val !== FALSE ) :
			
				return $_val;
				
			else:
			
				//	Nothing was found, but if $keys matches user_meta_* then attempt an extra table look up
				if ( preg_match( '/^user_meta_(.*)/', $keys ) ) :
				
					//	Look up the extra table
					$_val = $this->extra_table_fetch( $keys, NULL, $this->active_user->id );
					
					//	Save it to active_user so that we don't do this lookup twice
					$this->active_user->{$keys} = $_val;
					
					//	...and return the data to the user.
					return $_val;
				
				endif;
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	More than one key
		$keys = explode( ',', $keys );
		$_out = array();
		
		foreach ( $keys AS $key ) :
		
			$_val = ( isset( $this->active_user->{trim( $key )} ) ) ? $this->active_user->{trim( $key )} : FALSE;
			
			//	If something is found, use that.
			if ( $_val !== FALSE ) :
			
				$_out[] = $_val;
				
			else:
			
				//	Nothing was found, but if $key matcehs user_meta_* then attempt an extra table look up
				if ( preg_match( '/^user_meta_(.*)/', $key ) ) :
				
					//	Look up the extra table
					$_val = $this->extra_table_fetch( $key, NULL, $this->active_user->id );
					
					//	Save it to active_user so that we don't do this lookup twice
					$this->active_user->{$key} = $_val;
					
					//	...and return the data to the user.
					//	(Normally doesn't really make sense as this will just return the word Array because
					//	this is being imploded into a concacted string, however if a comma is left in by 
					//	accident or the other keys fail to return data then the output will be as normal).
					
					$_out[] =  $_val;
				
				endif;
			
			endif;
		
		endforeach;
		
		//	If nothing was found, just return FALSE
		if ( empty( $_out ) )
			return FALSE;
		
		//	If we have more than 1 element then stitch them together,
		//	if not just return the single element
		
		return ( count( $_out > 1 ) ) ? implode( $delimiter, $_out ) : $_out[0];
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Sets the login data for a user
	 *
	 * @access	public
	 * @param	int		id			The user's id
	 * @param	string	$email		The user's email
	 * @param	int		$group_id	The user's group
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	public function set_login_data( $id, $email, $group_id )
	{
		$this->session->set_userdata( 'id',			$id );
		$this->session->set_userdata( 'email',		$email );
		$this->session->set_userdata( 'group_id',	$group_id );
		
		return TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Determines whether the active user is logged in or not.
	 *
	 * @access	public
	 * @return	bool
	 * @author	Pablo
	 * 
	 **/
	public function is_logged_in()
	{
		return (bool) $this->session->userdata( 'email' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Determines whether the active user is to be remembered
	 *
	 * @access	public
	 * @return	bool
	 * @author	Pablo
	 * 
	 **/
	public function is_remembered()
	{
		$this->load->helper( 'cookie' );
		return ( get_cookie( 'email' ) && get_cookie( 'remember_code' ) ) ? TRUE : FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Determines whether the active user group has admin permissions.
	 *
	 * @access	public
	 * @return	boolean
	 * @author	Pablo
	 * 
	 **/
	public function is_admin( $user = NULL )
	{
		if ( $this->is_superuser( $user = NULL ) )
			return TRUE;
		
		return $this->has_permission( 'admin', $user );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Determines whether the active user is a superuser. Extend this method to
	 * alter it's response.
	 *
	 * @access	public
	 * @return	boolean
	 * @author	Pablo
	 * 
	 **/
	public function is_superuser( $user = NULL )
	{
		return $this->has_permission( 'superuser', $user );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * When an admin 'logs in as' another user a hash is added to the session so
	 * the system can log them back in. this method is simply a quick and logical
	 * way of checking if the session variable exists.
	 *
	 * @access	public
	 * @return	boolean
	 * @author	Pablo
	 * 
	 **/
	public function was_admin()
	{
		return ( $this->session->userdata( 'admin_recovery' ) ) ? TRUE : FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Determines whether the aspecified user has a certain acl permission
	 *
	 * @access	public
	 * @param	string	$permission	The permission to check for, in the format admin.account.view
	 * @param	mixed	$user		The user to check for; if null uses active user, if numeric, fetches suer, if object uses that object
	 * @return	boolean
	 * @author	Pablo
	 * 
	 **/
	public function has_permission( $permission = NULL, $user = NULL )
	{
		//	Fetch the correct ACL
		if ( is_numeric( $user ) ) :
		
			$_user = $this->get_user( $user );
			
			if ( isset( $_user->acl ) ) :
			
				$_acl = $_user->acl;
				unset( $_user );
			
			else :
			
				return FALSE;
			
			endif;
		
		elseif ( isset( $user->acl ) ) :
		
			$_acl = $user->acl;
		
		else :
		
			$_acl = active_user( 'acl' );
		
		endif;
		
		if ( ! $_acl )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		//	Super users can do anything they damn well please
		if ( isset( $_acl['superuser'] ) && $_acl['superuser'] )
			return TRUE;
		
		// --------------------------------------------------------------------------
		
		//	Test ACL, making sure to clean any dangerous data first
		$_permission = preg_replace( '/[^a-zA-Z\_\.]/', '', $permission );
		$_permission = explode( '.', $_permission );
		eval( '$has_permission = isset( $_acl[\'' . implode( '\'][\'', $_permission ) .'\'] );' );
		return $has_permission;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Get an array of users from the database, by default only user, group and
	 * meta information is returned, request specific extra meta by specifying
	 * which tables to include as the first parameter; seperate multiple tables
	 * using a comma.
	 *
	 * @access	public
	 * @param	mixed	$extended	Specific extra tables to join, TRUE for all user_meta_*
	 * @return	string
	 * @author	Pablo
	 * 
	 **/
	public function get_users( $extended = NULL, $order = NULL, $limit = NULL, $where = NULL, $search = NULL )
	{
		//	Write selects
		$this->db->select( 'u.*' );
		$this->db->select( 'um.*' );
		$this->db->select( 'uam.type AS `auth_type`' );
		$this->db->select( 'ug.display_name AS `group_name`' );
		$this->db->select( 'ug.default_homepage AS `group_homepage`' );
		$this->db->select( 'ug.acl AS `group_acl`' );
		$this->db->select( 'utz.gmt_offset timezone_gmt_offset, utz.label timezone_label' );
		$this->db->select( 'dfd.label date_format_date_label, dfd.format date_format_date_format' );
		$this->db->select( 'dft.label date_format_time_label, dft.format date_format_time_format' );
		$this->db->select( 'ul.name language_name, ul.safe_name language_safe_name' );
		
		// --------------------------------------------------------------------------
		
		//	Set Order
		if ( is_array( $order ) )
			$this->db->order_by( $order[0], $order[1] );
		
		// --------------------------------------------------------------------------
		
		//	Set Limit
		if ( is_array( $limit ) )
			$this->db->limit( $limit[0], $limit[1] );
		
		// --------------------------------------------------------------------------
		
		//	Build conditionals
		$this->_getcount_users_common( $where, $search );
		
		// --------------------------------------------------------------------------
		
		//	Execute Query
		$q		= $this->db->get( 'user u' );
		$_user	= $q->result();
		
		// --------------------------------------------------------------------------
		
		//	Include any extra tables?
		if ( $extended ) :
			
			//	Determine which tables we're including
			if ( $extended === TRUE ) :
			
				//	If $extended is TRUE we'll just join everything
				
				//	Pull up a list of the user_meta tables
				$q = $this->db->query( "SHOW TABLES LIKE 'user_meta_%'")->result();
				
				foreach( $q AS $key => $table ) :
				
					$_tables[] = current( (array) $table );
					
				endforeach;
								
			else :
				
				//	Specific tables defined, use them
				if ( strpos( $extended, ',' ) ) :
				
					$_tables = explode( ',', $extended );
					
				else :
				
					$_tables[] = $extended;
				
				endif;
				
			endif;
			
			// --------------------------------------------------------------------------
			
			//	Add the result of each extra table to each user in our result set
			
			if ( isset( $_tables ) ) :
			
				//	Loop for each returned user
				foreach ( $_user AS $_u ) :
				
					foreach ( $_tables AS $table ) :
						
						//	Define order by for certain tables
						switch ( $table ) :
						
							case 'user_meta_school':	$this->db->order_by( 'year', 'desc' );	break;
						
						endswitch;
						
						// --------------------------------------------------------------------------
						
						$this->db->where( 'user_id', $_u->id );
						$_u->{trim( $table )} = $this->db->get( trim( $table ) )->result();
						
					endforeach;
				
				endforeach;
				
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Determine the user's ACL
		foreach ( $_user AS $user ) :
		
			$user->group_acl = unserialize( $user->group_acl );
			
			//	If the user has an ACL set then we'll need to extract and merge that
			if ( $user->user_acl ) :
			
				$user->user_acl = unserialize( $user->user_acl );
				$user->acl = array();
				
				foreach( $user->group_acl AS $key => $value ) :
				
					if ( array_key_exists( $key, $user->user_acl ) ) :
					
						//	This key DOES exist in the second array, we'll need to analyse it and see if
						//	we need to recursively search. We are overwriting the source value with the
						//	new value, if both source and target 
						
						if ( is_array( $value ) && is_array( $user->user_acl[$key] ) ) :
						
							//	Two arrays, which will need to be merged
							$user->acl[$key] = array_merge( $value, $user->user_acl[$key] );
						
						else :
						
							//	Simply overwrite the old value with the new one, no recursion required
							$user->acl[$key] = $user->user_acl[$key];
						
						endif;
					
					else :
					
						//	This key isn't in the new array, ignore and just tack this onto the $result
						$user->acl[$key] = $value;
					
					endif;
				
				endforeach;
				
			
			else :
			
				$user->acl = $user->group_acl;
			
			endif;
			
			// --------------------------------------------------------------------------
			
			//	Tidy up date/time/timezone field
			$user->date_setting					= new stdClass();
			$user->date_setting->timezone		= new stdClass();
			$user->date_setting->format			= new stdClass();
			$user->date_setting->format->date	= new stdClass();
			$user->date_setting->format->time	= new stdClass();
			
			$user->date_setting->timezone->label	= $user->timezone_label;
			$user->date_setting->timezone->offset	= $user->timezone_gmt_offset;
			
			$user->date_setting->format->date->label	= $user->date_format_date_label;
			$user->date_setting->format->date->format	= $user->date_format_date_format;
			$user->date_setting->format->time->label	= $user->date_format_time_label;
			$user->date_setting->format->time->format	= $user->date_format_time_format;
			
			unset( $user->timezone_label );
			unset( $user->timezone_gmt_offset );
			unset( $user->date_format_date_label );
			unset( $user->date_format_date_format );
			unset( $user->date_format_time_label );
			unset( $user->date_format_time_format );
			
			// --------------------------------------------------------------------------
			
			//	Tidy up langauge field
			$user->language_setting				= new stdClass();
			$user->language_setting->id			=	$user->language_id;
			$user->language_setting->name		=	$user->language_name;
			$user->language_setting->safe_name	=	$user->language_safe_name;
			
			unset( $user->language_id );
			unset( $user->language_name );
			unset( $user->language_safe_name );
			
			
		endforeach;
		
		// --------------------------------------------------------------------------
		
		//	Return the data
		return $_user;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Counts the total amount of users for a partricular query/search key. Essentially performs
	 * the same query as $this->get_users() but without limiting.
	 *
	 * @access	public
	 * @param	string	$where	An array of where conditions
	 * @param	mixed	$search	A string containing the search terms
	 * @return	int
	 * @author	Pablo
	 * 
	 **/
	public function count_users( $where = NULL, $search = NULL )
	{
		$this->_getcount_users_common( $where, $search );
		
		// --------------------------------------------------------------------------
		
		//	Execute Query
		return $this->db->count_all_results( 'user u' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	private function _getcount_users_common( $where = NULL, $search = NULL )
	{
		$this->db->join( 'user_meta um',			'u.id = um.user_id',				'left' );
		$this->db->join( 'user_auth_method uam',	'u.auth_method_id = uam.id',		'left' );
		$this->db->join( 'user_group ug',			'u.group_id = ug.id',				'left' );
		$this->db->join( 'timezone utz',			'um.timezone_id = utz.id',			'left' );
		$this->db->join( 'date_format_date dfd',	'um.date_format_date_id = dfd.id',	'left' );
		$this->db->join( 'date_format_time dft',	'um.date_format_time_id = dft.id',	'left' );
		$this->db->join( 'language ul',				'um.language_id = ul.id',			'left' );
		
		// --------------------------------------------------------------------------
		
		//	Set Where
		if ( $where ) :
		
			$this->db->where( $where );
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Set Search
		if ( $search && is_string( $search ) ) :
		
			//	Search is a simple string, no columns are being specified to search across
			//	so define a default set to search across
			
			$search							= array( 'keywords' => $search, 'columns' => array() );
			$search['columns']['id']		= 'u.id';
			$search['columns']['email']		= 'u.email';
			$search['columns']['username']	= 'u.username';
			$search['columns']['name']		= array( ' ', 'um.first_name', 'um.last_name' );
			
		endif;
		
		//	If there is a search term to use then build the search query
		if ( isset( $search[ 'keywords' ] ) && $search[ 'keywords' ] ) :
		
			//	Parse the keywords, look for specific column searches
			preg_match_all('/\(([a-zA-Z0-9\.\- ]+):([a-zA-Z0-9\.\- ]+)\)/', $search['keywords'], $_matches );
			
			if ( $_matches[1] && $_matches[2] ) :
			
				$_specifics = array_combine( $_matches[1], $_matches[2] );
				
			else :
			
				$_specifics = array();
			
			endif;
			
			//	Match the specific labels to a column
			if ( $_specifics ) :
			
				$_temp = array();
				foreach ( $_specifics AS $col => $value ) :
				
					if ( isset( $search['columns'][ strtolower( $col )] ) ) :
						
						$_temp[] = array(
							'cols'	=> $search['columns'][ strtolower( $col )],
							'value'	=> $value
						);
						
					endif;
				
				endforeach;
				$_specifics = $_temp;
				unset( $_temp );
				
				// --------------------------------------------------------------------------
				
				//	Remove controls from search string
				$search['keywords'] = preg_replace('/\(([a-zA-Z0-9\.\- ]+):([a-zA-Z0-9\.\- ]+)\)/', '', $search['keywords'] );
			
			endif;
			
			if ( $_specifics ) :
			
				//	We have some specifics
				foreach( $_specifics AS $specific ) :
				
					if ( is_array( $specific['cols'] ) ) :
					
						$_separator = array_shift( $specific['cols'] );
						$this->db->like( 'CONCAT_WS( \'' . $_separator . '\', ' . implode( ',', $specific['cols'] ) . ' )', $specific['value'] );
					
					else :
					
						$this->db->like( $specific['cols'], $specific['value'] );
					
					endif;
				
				endforeach;
			
			endif;
			
			
			// --------------------------------------------------------------------------
			
			if ( $search['keywords'] ) : 
			
				$_where  = '(';
				
				if ( isset( $search[ 'columns' ] ) && $search[ 'columns' ] ) :
				
					//	We have some specifics
					foreach( $search[ 'columns' ] AS $col ) :
					
						if ( is_array( $col ) ) :
						
							$_separator = array_shift( $col );
							$_where .= 'CONCAT_WS( \'' . $_separator . '\', ' . implode( ',', $col ) . ' ) LIKE \'%' . trim( $search['keywords'] ) . '%\' OR ';
						
						else :
						
							$_where .= $col . ' LIKE \'%' . trim( $search['keywords'] ) . '%\' OR ';
						
						endif;
					
					endforeach;
				
				endif;
				
				$this->db->where( substr( $_where, 0, -3 ) . ')' );
				
			endif;
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Get a specific user by their ID
	 *
	 * @access	public
	 * @param	string	$user_id	The user's ID
	 * @param	mixed	$extended	Specific extra tables to join, TRUE for all user_meta_*
	 * @return	object
	 * @author	Pablo
	 * 
	 **/
	public function get_user( $user_id, $extended = FALSE )
	{
		if ( ! is_numeric( $user_id ) )
			return FALSE;
		
		$this->db->where( 'u.id', $user_id );
		$user = $this->get_users( $extended );
		
		return ( empty( $user ) ) ? FALSE : $user[0];
	}
	
	
	// --------------------------------------------------------------------------
	
	
	
	
	/**
	 * Get a specific user by their email address
	 *
	 * @access	public
	 * @param	string	$email		The user's email address
	 * @param	mixed	$extended	Specific extra tables to join, TRUE for all user_meta_*
	 * @return	object
	 * @author	Pablo
	 * 
	 **/
	public function get_user_by_email( $email, $extended = FALSE )
	{
		if ( ! is_string( $email ) )
			return FALSE;
		
		$this->db->where( 'u.email', $email );
		$user = $this->get_users( $extended );
		
		return ( empty( $user ) ) ? FALSE : $user[0];
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Get a specific user by their Facebook ID
	 *
	 * @access	public
	 * @param	int		$fbid		The user's Facebook ID
	 * @param	mixed	$extended	Specific extra tables to join, TRUE for all user_meta_*
	 * @return	object
	 * @author	Pablo
	 * 
	 **/
	public function get_user_by_fbid( $fbid, $extended = FALSE )
	{
		$this->db->where( 'u.fb_id', $fbid );
		$user = $this->get_users( $extended );
		
		return ( empty( $user ) ) ? FALSE : $user[0];
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Get a specific user by their Twitter ID
	 *
	 * @access	public
	 * @param	int		$twid		The user's Twitter ID
	 * @param	mixed	$extended	Specific extra tables to join, TRUE for all user_meta_*
	 * @return	object
	 * @author	Pablo
	 * 
	 **/
	public function get_user_by_twid( $twid, $extended = FALSE )
	{
		$this->db->where( 'u.tw_id', $twid );
		$user = $this->get_users( $extended );
		
		return ( empty( $user ) ) ? FALSE : $user[0];
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Get a specific user by their LinkedIn ID
	 *
	 * @access	public
	 * @param	int		$fbid		The user's LinkedIn ID
	 * @param	mixed	$extended	Specific extra tables to join, TRUE for all user_meta_*
	 * @return	object
	 * @author	Pablo
	 * 
	 **/
	public function get_user_by_linkedinid( $linkedinid, $extended = FALSE )
	{
		$this->db->where( 'u.li_id', $linkedinid );
		$user = $this->get_users( $extended );
		
		return ( empty( $user ) ) ? FALSE : $user[0];
	}
	
	
	// --------------------------------------------------------------------------
	
	
	
	
	/**
	 * Get a specific user by the MD5 hash of their ID and password
	 *
	 * @access	public
	 * @param	string	$_hash_id	The user's id as an MD5 hash
	 * @param	mixed	$_hash_pw	The user's hashed password as an MD5 hash
	 * @return	object
	 * @author	Pablo
	 * 
	 **/
	public function get_user_by_hashes( $_hash_id, $_hash_pw, $extended = FALSE )
	{
		if ( empty( $_hash_id ) || empty( $_hash_pw ) )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		//	Set wheres
		$this->db->where( 'u.id_md5',		$_hash_id );
		$this->db->where( 'u.password_md5',	$_hash_pw );
		
		// --------------------------------------------------------------------------
		
		//	Do it
		$this->db->limit( 1 );
		$q = $this->get_users( $extended );
		
		// --------------------------------------------------------------------------
		
		return count( $q ) ? $q[0] : FALSE ;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Get a specific user by their referral code
	 *
	 * @access	public
	 * @param	string	$referral_code	The user's referral code
	 * @param	mixed	$extended		Specific extra tables to join, TRUE for all user_meta_*
	 * @return	object
	 * @author	Pablo
	 * 
	 **/
	public function get_user_by_referral( $referral_code, $extended = FALSE  )
	{
		$this->db->where( 'um.referral', $referral_code );
		$user = $this->get_users( $extended );
		
		return ( empty( $user ) ) ? FALSE : $user[0];
	}
	
	
	// --------------------------------------------------------------------------
	
	
	
	
	/**
	 * Returns recent users; ordered by ID, desc
	 *
	 * @access	public
	 * @param	int			$limit		The number of user's to return
	 * @param	boolean		$extended	Whether to include extended data or not
	 * @return	object
	 * @author	Pablo
	 * 
	 **/
	public function get_new_users( $limit = 25, $extended = FALSE )
	{
		$this->db->limit( $limit );
		$this->db->order_by( 'u.id', 'desc' );
		return $this->get_users( $extended );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Update a user, if $user_id is not set method will attempt to update the
	 * active user. If $data is passed then the method will attempt to update
	 * the user and/or user_meta tables
	 *
	 * @access	public
	 * @return	int		$id		The ID of the user to update
	 * @return	array	$data	Any data to be updated
	 * @author	Pablo
	 * 
	 **/
	public function update( $user_id = NULL, $data = NULL )
	{
		$data = (array) $data;
		
		//	Get the user ID to update
		if ( ! is_null( $user_id ) && $user_id !== FALSE ) :
		
			$_uid = $user_id;
			
		elseif ( active_user( 'id' ) ) :
		
			$_uid = active_user( 'id' );
			
		else :
		
			show_error( 'USER UPDATE ERROR: No user ID set' );
		
		endif;
		
		
		// --------------------------------------------------------------------------
		
		
		//	If there's some data we'll need to know the columns of `user`
		//	We also want to unset any 'dangerous' items then set it for the query
		
		if ( $data ) :
		
			//	Set the cols in user (rather than querying the DB)
			$_cols		= array();
			$_cols[]	= 'auth_method_id';
			$_cols[]	= 'group_id';
			$_cols[]	= 'fb_id';
			$_cols[]	= 'fb_token';
			$_cols[]	= 'tw_id';
			$_cols[]	= 'tw_token';
			$_cols[]	= 'tw_secret';
			$_cols[]	= 'li_id';
			$_cols[]	= 'li_token';
			$_cols[]	= 'li_secret';
			$_cols[]	= 'ip_address';
			$_cols[]	= 'last_ip';
			$_cols[]	= 'username';
			$_cols[]	= 'password';
			$_cols[]	= 'password_md5';
			$_cols[]	= 'salt';
			$_cols[]	= 'email';
			$_cols[]	= 'activation_code';
			$_cols[]	= 'forgotten_password_code';
			$_cols[]	= 'remember_code';
			$_cols[]	= 'created';
			$_cols[]	= 'last_login';
			$_cols[]	= 'last_seen';
			$_cols[]	= 'active';
			$_cols[]	= 'temp_pw';
			$_cols[]	= 'failed_login_count';
			$_cols[]	= 'failed_login_expires';
			$_cols[]	= 'last_update';
			$_cols[]	= 'user_acl';
			$_cols[]	= 'login_count';
			
			//	Safety first, no updating of user's ID.
			unset( $data->id );
			
			//	If we're updatig the email of a user check to see if
			//	the new email already exists; can't be having two identical
			//	emails in the user table.
			
			if (  array_key_exists( 'email', $data ) ) :
			
				//	Exclude the current user, we're only interested in other users
				$this->db->where( 'u.id !=', (int) $_uid );
				$this->db->where( 'u.email', $data['email'] );
				
				if ( $this->db->count_all_results( 'user u' ) ) :
				
					//	We found a user who isn't the current user who is already
					//	using this email address.
					
					return FALSE;
				
				endif;
			
			endif;
			
			//	If we're updating the user's password we should generate a new hash			
			if (  array_key_exists( 'password', $data ) ) :
			
				$_hash = $this->hash_password( $data['password'] );
				
				$data['password']		= $_hash[0];
				$data['password_md5']	= md5( $_hash[0] );
				$data['salt']			= $_hash[1];
			
			endif;
			
			//	Set the data
			$_data_user	= array();
			$_data_meta	= array();
			
			foreach ( $data AS $key => $val ) :
			
				//	user or user_meta?
				if ( array_search( $key, $_cols ) !== FALSE ) :
				
					$_data_user[$key] = $val;
				
				else :
				
					$_data_meta[$key] = $val;
				
				endif;
			
			endforeach;
			
			//	Update the user table
			$this->db->where( 'id', (int) $_uid );
			$this->db->set( 'last_update', 'NOW()', FALSE );
			
			if ( $_data_user ) :
			
				$this->db->set( $_data_user );
				
			endif;
			
			//	Update the meta table
			$this->db->update( 'user' );
			
			if ( $_data_meta ) :
			
				$this->db->where( 'user_id', (int) $_uid );
				$this->db->set( $_data_meta );
				$this->db->update( 'user_meta' );
			
			endif;
		
		else :
		
			//	If there was no data then run an update anyway on just user table. We need to do this
			//	As some methods will use $this->db->set() before calling update(); not sure if this is
			//	a bad design or not... sorry.
			
			$this->db->set( 'last_update', 'NOW()', FALSE );
			$this->db->where( 'id', (int) $_uid );
			$this->db->update( 'user' );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	If we just updated the active user we should probably update their session info
		if ( $_uid == active_user( 'id' ) ) :
		
			$this->active_user->last_update = date( 'Y-m-d H:i:s' );
			
			if ( $data ) :
			
				foreach( $data AS $key => $val ) :
				
					$this->active_user->{$key} = $val;
				
				endforeach;
			
			endif;
			
			//	If there's a remember me cookie then update that too, but only if the password
			//	or email address has changed
			
			if ( ( isset( $data['email'] ) || isset( $data['email'] ) ) && $this->is_remembered() ) :
			
				$this->set_remember_cookie();
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		return TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Increase the user's failed login account by 1
	 *
	 * @access	public
	 * @param	int		$user_id	The ID of the user to increment
	 * @param	int		$expires	Time (in seconds) until expiration
	 * @return	void
	 * @author	Pablo
	 **/
	public function increment_failed_login( $user_id, $expires = 300 )
	{
		$this->db->set( 'failed_login_count', '`failed_login_count`+1', FALSE );
		$this->db->set( 'failed_login_expires', date( 'Y-m-d H:i:s', time() + $expires ) );
		$this->update( $user_id );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Reset a user's failed login
	 *
	 * @access	public
	 * @param	int		$user_id	The ID of the user to reset
	 * @return	void
	 * @author	Pablo
	 **/
	public function reset_failed_login( $user_id )
	{
		$this->db->set( 'failed_login_count', 0 );
		$this->db->set( 'failed_login_expires', 'NULL', FALSE );
		$this->update( $user_id );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Update a user's last login field
	 *
	 * @access	public
	 * @param	int		$user_id	The ID of the user to update
	 * @return	void
	 * @author	Pablo
	 **/
	public function update_last_login( $user_id )
	{
		$this->db->set( 'last_login', 'NOW()', FALSE );
		$this->db->set( 'login_count', 'login_count+1', FALSE );
		$this->update( $user_id );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Set the user's 'remember me' cookie, nom nom nom
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 **/
	public function set_remember_cookie()
	{
		if ( ! active_user( 'id' ) )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		//	Generate a code to remember the user by and save it to the DB
		$salt = sha1( active_user( 'password' ) );
		$this->db->set( 'remember_code', $salt );
		$this->db->where( 'id', active_user( 'id' ) );
		$this->db->update( 'user' );
		
		// --------------------------------------------------------------------------
		
		//	Set the cookies
		$data = NULL;
		$data['name']	= 'email';
		$data['value']	= active_user( 'email' );
		$data['expire']	= 1209600;
		set_cookie( $data );
		
		// --------------------------------------------------------------------------
		
		$data = NULL;
		$data['name']	= 'remember_code';
		$data['value']	= $salt;
		$data['expire']	= 1209600;
		set_cookie( $data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Clear's the user's remember me cookie
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 **/
	public function clear_remember_cookie()
	{
		$this->load->helper( 'cookie' );
		
		// --------------------------------------------------------------------------
		
		if ( get_cookie( 'email' ) )
			delete_cookie( 'email' );
		
		// --------------------------------------------------------------------------
		
		if ( get_cookie('remember_code' ) )
			delete_cookie( 'remember_code' );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Generate a unique salt
	 *
	 * @access	private
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	public function salt()
	{
		return md5( uniqid( rand() . DEPLOY_PRIVATE_KEY . APP_PRIVATE_KEY, TRUE ) );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Create a password hash
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	array
	 * @author	Pablo
	 **/
	public function hash_password( $password, $salt = FALSE )
	{
		if ( empty( $password ) )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		if ( ! $salt )
			$salt  = $this->salt();
		
		// --------------------------------------------------------------------------
		
		return array( sha1( sha1( $password ) . $salt ), $salt );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * hash a password based on the user's salt (as defined in DB)
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 * @author	Pablo
	 **/
	public function hash_password_db( $email, $password )
	{
		if ( empty( $email ) || empty( $password ) )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		$this->db->select( 'password, salt' );
		$this->db->where( 'email', $email );
		$this->db->limit( 1 );
		$_q = $this->db->get( 'user' );
		
		// --------------------------------------------------------------------------
		
		if ( $_q->num_rows() !== 1 )
			return FALSE;
		
		// --------------------------------------------------------------------------
				
		return sha1( sha1( $password ) . $_q->row()->salt );
	
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Refreshes the user's session from database data
	 *
	 * @access	protected
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	protected function _refresh_session()
	{
		//	Get the user
		$_me = $this->session->userdata( 'id' );
		
		// --------------------------------------------------------------------------
		
		//	No-one's home...
		if ( ! $_me )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		//	Store this entire user in memory
		$this->active_user = $this->get_user( $_me );
		
		// --------------------------------------------------------------------------
		
		//	Update user's 'last_seen' flag
		$_data['last_seen'] = date( 'Y-m-d H:i:s' );
		$this->db->where( 'id', $_me );
		$this->db->update( 'user', $_data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Create a new row in a user extended table
	 *
	 * @access	public
	 * @param	string	$table		The name of the table to insert to
	 * @param	object	$data		The data to insert
	 * @param	int		$user_id	If not updating the active user specify the user ID
	 * @return	mixed
	 * @author	Pablo
	 **/
	public function extra_table_insert( $table, $data, $user_id = FALSE )
	{
		$_uid = ( ! $user_id ) ? (int) active_user( 'id' ) : $user_id ;
		
		// --------------------------------------------------------------------------
		
		//	Unable to determine user ID
		if ( ! $_uid )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		$data = (object) $data;
		$data->user_id = $_uid;
		
		// --------------------------------------------------------------------------
		
		$this->db->insert( $table, $data );
		
		// --------------------------------------------------------------------------
		
		return $this->db->affected_rows() ? $this->db->insert_id() : FALSE ;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Update an extra user table
	 *
	 * @access	public
	 * @param	string	$table		The name of the table to fetch from
	 * @param	int		$id			The ID of the row to fetch
	 * @param	int		$user_id	If not fetching for the active user specify the user ID
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function extra_table_fetch( $table, $id = NULL, $user_id = NULL )
	{
		$_uid = ( ! $user_id ) ? (int) active_user( 'id' ) : $user_id ;
		
		// --------------------------------------------------------------------------
		
		//	Unable to determine user ID
		if ( ! $_uid )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		$this->db->where( 'user_id', $_uid );
		
		// --------------------------------------------------------------------------
		
		//	Add restriction of nessecary
		if ( $id )
			$this->db->where( 'id', $id );
		
		// --------------------------------------------------------------------------
		
		$_row = $this->db->get( $table );
		
		return ( $id ) ? $_row->row() : $_row->result();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Update an extra user table
	 *
	 * @access	public
	 * @param	string	$table		The name of the table to update
	 * @param	object	$data		The data to update
	 * @param	int		$user_id	If not updating the active user specify the user ID
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function extra_table_update( $table, $data, $user_id = FALSE )
	{
		$_uid = ( ! $user_id ) ? (int) active_user( 'id' ) : $user_id ;
		
		// --------------------------------------------------------------------------
		
		//	Unable to determine user ID
		if ( ! $_uid )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		$data = (object) $data;
		
		// --------------------------------------------------------------------------
		
		if ( ! isset( $data->id ) || empty( $data->id ) )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		$this->db->where( 'user_id', $_uid );
		$this->db->where( 'id', $data->id );
		$this->db->update( $table, $data );
		
		// --------------------------------------------------------------------------
		
		return TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	

	/**
	 * Delete from an extra user table
	 *
	 * @access	public
	 * @param	string	$table		The name of the table to delete from
	 * @param	int		$id			The ID of the row to delete
	 * @param	int		$user_id	If not updating the active user specify the user ID
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function extra_table_delete( $table, $id, $user_id = FALSE )
	{
		$_uid = ( ! $user_id ) ? (int) active_user( 'id' ) : $user_id ;
		
		// --------------------------------------------------------------------------
		
		//	Unable to determine user ID
		if ( ! $_uid )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		if ( ! isset( $id ) || empty( $id ) )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		$this->db->where( 'user_id', $_uid );
		$this->db->where( 'id', $id );
		$this->db->delete( $table );
		
		// --------------------------------------------------------------------------
				
		return $this->db->affected_rows() ? TRUE : FALSE ;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Create a new user
	 *
	 * @access	public
	 * @param	string	$email		The email address of the new user account
	 * @param	string	$password	The user's password
	 * @param	int		$group_id	The user's group
	 * @param	array	$data		Any meta data to be stored alongside the user
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function create( $email, $password, $group_id, $data = FALSE )
	{
		if ( ! $email || ! $group_id )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		//	Check email against DB
		$this->db->where( 'email', $email );
		if (  $this->db->count_all_results( 'user' ) ) :
		
			$this->_set_error( 'This email is already in use.' );
			return FALSE;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	All should be ok, go ahead and create the account
		$_ip_address		= $this->input->ip_address();
		$_activation_code	= $this->salt();
		
		// --------------------------------------------------------------------------
		
		//	If a password has been passed then generate the encrypted strings, otherwise
		//	just generate a salt.
		
		if ( is_null( $password ) ) :
		
			$_password[] = NULL;
			$_password[] = $this->salt(); 
		
		else :
		
			$_password = $this->hash_password( $password );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		// Users table
		$_data['password']			= $_password[0];
		$_data['password_md5']		= md5( $_password[0] );
		$_data['email']				= trim( $email );
		$_data['group_id']			= $group_id;
		$_data['ip_address']		= $_ip_address;
		$_data['last_ip']			= $_ip_address;
		$_data['created']			= date( 'Y-m-d H:i:s' );
		$_data['last_update']		= date( 'Y-m-d H:i:s' );
		$_data['active']			= ( isset( $data['active'] ) && $data['active'] )	? 1	: 0 ;
		$_data['salt']				= $_password[1];
		$_data['activation_code']	= $_activation_code;
		$_data['temp_pw']			= ( isset( $data['temp_pw'] ) && $data['temp_pw'] )	? 1	: 0 ;
		$_data['auth_method_id']	= ( isset( $data['auth_method_id'] ) )				? $data['auth_method_id']	: 1 ;
		
		//	Facebook oauth details
		$_data['fb_token']			= ( isset( $data['fb_token'] ) )					? $data['fb_token']			: NULL ;
		$_data['fb_id']				= ( isset( $data['fb_id'] ) )						? $data['fb_id']			: NULL ;
		
		//	Twitter oauth details
		$_data['tw_id']				= ( isset( $data['tw_id'] ) )						? $data['tw_id']			: NULL ;
		$_data['tw_token']			= ( isset( $data['tw_token'] ) )					? $data['tw_token']			: NULL ;
		$_data['tw_secret']			= ( isset( $data['tw_secret'] ) )					? $data['tw_secret']		: NULL ;
		
		//	Linkedin oauth details
		$_data['li_id']				= ( isset( $data['li_id'] ) )						? $data['li_id']			: NULL ;
		$_data['li_token']			= ( isset( $data['li_token'] ) )					? $data['li_token']			: NULL ;
		$_data['li_secret']			= ( isset( $data['li_secret'] ) )					? $data['li_secret']		: NULL ;
		
		//	Unset extra data fields which have been used already
		unset( $data['temp_pw'] );
		unset( $data['active'] );
		unset( $data['auth_method_id'] );
		unset( $data['fb_token'] );
		unset( $data['fb_id'] );
		unset( $data['tw_id'] );
		unset( $data['tw_token'] );
		unset( $data['tw_secret'] );
		unset( $data['li_id'] );
		unset( $data['li_token'] );
		unset( $data['li_secret'] );
		
		$this->db->insert( 'user', $_data );
		
		$_id = $this->db->insert_id();
		
		// --------------------------------------------------------------------------
		
		//	If a username has been supplied check it's unique, if it's not then use the
		//	User's ID (will be called in the MD5 query immediately following)
		
		if ( isset( $data['username'] ) && $data['username'] ) :
		
			$this->db->where( 'username' , trim( $data['username'] ) );
			
			if ( $this->db->count_all_results( 'user' ) ) :
			
				//	Not unique, use user ID
				$this->db->set( 'username', $_id );
			
			else :
			
				//	Unique, go ahead and use it
				$this->db->set( 'username', trim( $data['username'] ) );
			
			endif;
			
			unset( $data['username'] );
			
		else :
		
			//	Not supplied, use user ID
			$this->db->set( 'username', $_id );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Update the user table with an MD5 hash of the user ID; a number of functions
		//	make use of looking up this hashed information; this should be quicker.
		
		$this->db->set( 'id_md5', md5( $_id ) );
		$this->db->where( 'id', $_id );
		$this->db->update( 'user' );
		
		// --------------------------------------------------------------------------
		
		//	Generate a referral code
		$_referral = $this->_generate_referral();
		
		// --------------------------------------------------------------------------
		
		//	Create the user_meta record, add any extra data if needed
		$this->db->set( 'user_id', $_id );
		$this->db->set( 'referral', $_referral );
		
		if ( $data ) :
		
			$this->db->set( $data );
			
		endif;
		
		$this->db->insert( 'user_meta' );
		
		// --------------------------------------------------------------------------
		
		//	Return useful user info
		$_out['id']			= $_id;
		$_out['activation']	= $_activation_code;
		
		return $_out;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Delete a user
	 *
	 * @access	public
	 * @param	int		$id	The ID of the user to delete
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function destroy( $id )
	{
		$this->db->where( 'id', $id );
		$this->db->delete( 'user' );
		
		// --------------------------------------------------------------------------
		
		return (bool) $this->db->affected_rows();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Generate a valid referral code
	 *
	 * @access	private
	 * @param	none
	 * @return	string
	 * @author	Pablo
	 **/
	private function _generate_referral()
	{
		$this->load->helper( 'string' );
		
		// --------------------------------------------------------------------------
		
		while ( 1 > 0 ) :
		
			$referral = random_string( 'alnum', 8 );
			$q = $this->db->get_where( 'user_meta', array( 'referral' => $referral ) );
			if ( $q->num_rows() == 0 )
				break;
				
		endwhile;
		
		// --------------------------------------------------------------------------
		
		return $referral;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Set's a forgotten password token for a user
	 *
	 * @access	public
	 * @param	int		$group_id	The ID of the group to fetch
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function set_password_token( $email )
	{
		if ( empty( $email ) )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		//	Generate code
		$_key = $this->hash_password( $this->salt() );
		$_ttl = time() + 86400; // 24 hours.
		
		// --------------------------------------------------------------------------
		
		//	Update the user
		$this->db->set( 'forgotten_password_code', $_ttl . ':' . $_key[0] );
		$this->db->where( 'email', $email );
		$this->db->update( 'user');
		
		// --------------------------------------------------------------------------
		
		return (bool) $this->db->affected_rows();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Validate a forgotten password code. If valid generate a new password and update user table
	 *
	 * @access	public
	 * @param	string
	 * @return	string or boolean FALSE
	 * @author	Pablo
	 **/
	public function validate_password_token( $code )
	{
		if ( empty( $code ) )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		$this->db->like( 'forgotten_password_code', ':' . $code, 'before' );
		$_q = $this->db->get( 'user' );
		
		// --------------------------------------------------------------------------
		
		if ( $_q->num_rows() != 1 )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		$_user = $_q->row();
		$_code = explode( ':', $_user->forgotten_password_code );
		
		// --------------------------------------------------------------------------
		
		//	Check that the link is still valid
		if ( time() > $_code[0] ) :
		
			return 'EXPIRED';
		
		else :
		
			//	Valid hash and hasn't expired.
			$this->load->helper( 'string' );
			$_password	= random_string( 'alpha', 6 );
			$_hash	 	= $this->hash_password( $_password );
			
			// --------------------------------------------------------------------------
			
			$_data['password']					= $_hash[0];
			$_data['password_md5']				= md5( $_hash[0] );
			$_data['salt']						= $_hash[1];
			$_data['active']					= 1;
			$_data['temp_pw']					= 1;
			$_data['forgotten_password_code']	= NULL;
			
			// --------------------------------------------------------------------------
			
			$this->db->where( 'forgotten_password_code', $_user->forgotten_password_code );
			$this->db->set( $_data );
			$this->db->update( 'user' );
			
			return $_password;
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Activate a user
	 *
	 * @access	public
	 * @param	int		$id		The user to activate
	 * @param	string	$code	If present the user will only be activated if the code matches
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function activate( $id, $code = FALSE )
	{
		//	Code is present, use it in the check
		if ( $code != FALSE ) :
		
			//	Get the user
			$this->db->select( 'email' );
			$this->db->where( 'activation_code', $code );
			$this->db->limit( 1 );
			$_user = $this->db->get( 'user' )->row();
			
			// --------------------------------------------------------------------------
			
			if ( empty( $_user ) )
				return FALSE;
			
			// --------------------------------------------------------------------------
			
			//	Update the user
			$this->db->set( 'activation_code', NULL);
			$this->db->set( 'active', 1 );
			$this->db->set( 'last_login', 'NOW()', FALSE );
			$this->db->where( 'id', $id );
			$this->db->update( 'user' );
		
		
		//	Just bloody well activate the user.
		else :
		
			$this->db->set( 'activation_code', NULL );
			$this->db->set( 'active', 1 );
			$this->db->set( 'last_login', 'NOW()', FALSE );
			$this->db->where( 'id', $id );
			$this->db->update( 'user' );
			
		endif;
		
		// --------------------------------------------------------------------------
		
		//	How did we do?
		return (bool) $this->db->affected_rows();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function reward_referral( $user_id, $referrer_id )
	{
		//	TODO
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Deactivate a user
	 *
	 * @access	public
	 * @param	int		$id		The user to deactivate
	 * @param	array	$data	If present deactivation feedback
	 * @return	boolean
	 * @author	Pablo
	 **/
	public function deactivate( $id, $data )
	{		
		$this->db->set( 'activation_code', $this->salt() );
		$this->db->set( 'active', 0 );
		$this->db->set( 'last_login', 'NOW()', FALSE );
		$this->db->where( 'id', $id );
		$this->db->update( 'user' );
		
		// --------------------------------------------------------------------------
		
		//	How did we do?
		$_deactivated = (bool) $this->db->affected_rows();
		
		// --------------------------------------------------------------------------
		
		//	Save some feedback if nessecary
		if ( $_deactivated && $data ) :
		
			$this->db->set( 'deactivated_on', 'NOW()', FALSE );
			$this->extra_table_insert( 'user_meta_deactivate_feedback', $data );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		return $_deactivated;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Suspend a user
	 *
	 * @access	public
	 * @param	int		$id	The ID of the user to suspend
	 * @return	boolean
	 * @author	Pablo
	 **/
	 public function suspend( $id )
	 {
	 	return $this->update( $id, array( 'active' => 2 ) );
	 }
	 
	 
	 // --------------------------------------------------------------------------
	 
	 
	/**
	 * Unsuspend a user
	 *
	 * @access	public
	 * @param	int		$id	The ID of the user to unsuspend
	 * @return	boolean
	 * @author	Pablo
	 **/
	 public function unsuspend( $id )
	 {
	 	return $this->update( $id, array( 'active' => 1 ) );
	 }
	 
	 
	 // --------------------------------------------------------------------------
	 
	 
	/**
	 * Upload a profile image and update the user's record
	 *
	 * @access	public
	 * @param	string
	 * @param	int
	 * @param	string
	 * @param	int
	 * @return	bool
	 * @author	Pablo
	 **/
	public function upload_profile_image( $file, $user_id )
	{
		//	TODO: Should use the CDN library
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function delete_profile_img()
	{
		//	TODO: Should use the CDN library
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Returns an array of user groups
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	array
	 * @param	string
	 * @return	array
	 * @author	Pablo
	 **/
	public function get_groups()
	{
		$_groups = $this->db->get( 'user_group' )->result();
		
		// --------------------------------------------------------------------------
		
		//	Loop through results and unserialise the acl
		foreach( $_groups AS $group ) :
		
			$group->acl = unserialize( $group->acl );
			
		endforeach;
		
		// --------------------------------------------------------------------------
		
		return $_groups;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Returns an array of user groups
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	array
	 * @param	string
	 * @return	array
	 * @author	Pablo
	 **/
	public function get_groups_flat()
	{
		$_groups	= $this->get_groups();
		$_out		= array();
		
		// --------------------------------------------------------------------------
		
		//	Loop through results and unserialise the acl
		foreach( $_groups AS $group ) :
		
			$_out[$group->id] = $group->display_name;
			
		endforeach;
		
		// --------------------------------------------------------------------------
		
		return $_out;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Get a group's information
	 *
	 * @access	public
	 * @param	int		$group_id	The ID of the group to fetch
	 * @return	object
	 * @author	Pablo
	 **/
	public function get_group( $group_id )
	{
		$this->db->where( 'id', $group_id );
		$_group = $this->get_groups();
		
		// --------------------------------------------------------------------------
		
		return ( isset( $_group[0] ) ) ? $_group[0] : FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Update a group's information
	 *
	 * @access	public
	 * @param	int		$id		The ID of the group to update
	 * @param	array	$data	The data to use in the update
	 * @return	void
	 * @author	Pablo
	 **/
	public function update_group( $id, $data )
	{
		$this->db->set( $data );
		$this->db->where( 'id', $id );
		$this->db->update( 'user_group' );
	}
}

/* End of file user_model.php */
/* Location: ./system/application/models/user_model.php */