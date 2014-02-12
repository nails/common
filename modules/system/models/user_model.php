<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		User_model
 *
 * Description:	The user model contains all methods for interacting and
 *				querying the active user. It also contains functionality for
 *				interfacing with the database with regards user accounts.
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_User_model extends NAILS_Model
{
	protected $_me;
	protected $_active_user;
	protected $_remember_cookie;
	protected $_is_remembered;
	protected $_is_logged_in;

	// --------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Set defaults
		$this->_remember_cookie	= 'nailsrememberme';
		$this->_is_remembered	= NULL;

		// --------------------------------------------------------------------------

		//	Clear the active_user
		$this->clear_active_user();
	}


	// --------------------------------------------------------------------------


	/**
	 * Initialise the generic user model
	 *
	 * @access	public
	 * @return	void
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
	 * user_model class to set the data when it instantiates.
	 *
	 * @access	static
	 * @return	void
	 *
	 **/
	public function find_remembered_user()
	{
		$_ci =& get_instance();
;
		// --------------------------------------------------------------------------

		//	User is already logged in, nothing to do.
		if ( (bool) $this->is_logged_in() ) :

			return;

		endif;

		// --------------------------------------------------------------------------

		//	Look for a cookie
		$_ci->load->helper( 'cookie' );
		$_remember_me = get_cookie( $this->_remember_cookie );

		// --------------------------------------------------------------------------

		//	If we're missing anything then there's nothing to do
		if ( ! $_remember_me ) :

			return;

		endif;

		// --------------------------------------------------------------------------

		//	User cookie's were found
		define( 'LOGIN_REMEMBERED_USER', $_remember_me );

		// --------------------------------------------------------------------------

		return;
	}


	// --------------------------------------------------------------------------


	/**
	 * Log in a previously logged in user
	 *
	 * @access	protected
	 * @return	void
	 *
	 **/
	protected function _login_remembered_user()
	{
		//	Only attempt to log in a user if they are remembered.
		//	This constant is set in User_Model::find_remembered_user();

		if ( ! defined( 'LOGIN_REMEMBERED_USER' ) || ! LOGIN_REMEMBERED_USER ) :

			return;

		endif;

		// --------------------------------------------------------------------------

		//	Get the credentials from the constant set earlier
		$_remember	= explode( '|', LOGIN_REMEMBERED_USER );
		$_email		= isset( $_remember[0] ) ? $_remember[0] : NULL;
		$_code		= isset( $_remember[1] ) ? $_remember[1] : NULL;

		if ( $_email && $_code ) :

			//	Look up the user so we can cross-check the codes
			$_u = $this->get_by_email( $_email, TRUE );

			if ( $_u && $_code === $_u->remember_code ) :

				//	User was validated, log them in!
				$this->update_last_login( $_u->id );
				$this->set_login_data( $_u->id );
				$this->_me = $_u->id;

			endif;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetches a value from the active user's session data; done this way so
	 * that interfacing with active user data is consistent.
	 *
	 * @access	public
	 * @param	string	$keys		The key to look up in active_user
	 * @param	string	$delimiter	If multiple fields are requested they'll be joined by this string
	 * @return	mixed
	 *
	 **/
	public function active_user( $keys = FALSE, $delimiter = ' '  )
	{
		//	Only look for a value if we're logged in
		if ( ! $this->is_logged_in() ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	If $keys is FALSE just return the user object in its entirety
		if ( $keys === FALSE ) :

			return $this->_active_user;

		endif;

		// --------------------------------------------------------------------------

		//	Only stitch items together if we have more than one key
		if ( strpos( $keys, ',' ) === FALSE ) :

			$_val = ( isset( $this->_active_user->{$keys} ) ) ? $this->_active_user->{$keys} : FALSE;

			//	If something is found, then use that
			if ( $_val !== FALSE ) :

				return $_val;

			else:

				//	Nothing was found, but if $keys matches user_meta_* then attempt an extra table look up
				if ( preg_match( '/^user_meta_(.*)/', $keys ) ) :

					//	Look up the extra table
					$_val = $this->extra_table_fetch( $keys, NULL, $this->_active_user->id );

					//	Save it to active_user so that we don't do this lookup twice
					$this->_active_user->{$keys} = $_val;

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

			$_val = ( isset( $this->_active_user->{trim( $key )} ) ) ? $this->_active_user->{trim( $key )} : FALSE;

			//	If something is found, use that.
			if ( $_val !== FALSE ) :

				$_out[] = $_val;

			else:

				//	Nothing was found, but if $key matcehs user_meta_* then attempt an extra table look up
				if ( preg_match( '/^user_meta_(.*)/', $key ) ) :

					//	Look up the extra table
					$_val = $this->extra_table_fetch( $key, NULL, $this->_active_user->id );

					//	Save it to active_user so that we don't do this lookup twice
					$this->_active_user->{$key} = $_val;

					//	...and return the data to the user.
					//	(Normally doesn't really make sense as this will just return the word Array because
					//	this is being imploded into a concacted string, however if a comma is left in by
					//	accident or the other keys fail to return data then the output will be as normal).

					$_out[] =  $_val;

				endif;

			endif;

		endforeach;

		//	If nothing was found, just return FALSE
		if ( empty( $_out ) ) :

			return FALSE;

		endif;

		//	If we have more than 1 element then stitch them together,
		//	if not just return the single element

		return count( $_out > 1 ) ? implode( $delimiter, $_out ) : $_out[0];
	}


	// --------------------------------------------------------------------------


	public function set_active_user( $user )
	{
		$this->_active_user = $user;
	}


	// --------------------------------------------------------------------------


	public function clear_active_user()
	{
		$this->_active_user = new stdClass();
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
	 *
	 **/
	public function set_login_data( $id, $set_session_data = TRUE )
	{
		//	Valid user?
		if ( is_numeric( $id ) ) :

			$_user	= $this->get_by_id( $id );
			$_error	= 'Invalid User ID.';

		elseif ( is_string( $id ) ) :

			$this->load->helper( 'email' );

			if ( valid_email( $id ) ) :

				$_user	= $this->get_by_email( $id );
				$_error	= 'Invalid User email.';

			else :

				$this->_set_error( 'Invalid User email.' );
				return FALSE;

			endif;

		else :

			$this->_set_error( 'Invalid user ID or email.' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Test user
		if ( ! $_user ) :

			$this->_set_error( $_error );
			return FALSE;

		elseif ( $_user->is_suspended ) :

			$this->_set_error( 'User is suspended.' );
			return FALSE;

		else :

			//	Set the flag
			$this->_is_logged_in = TRUE;

			//	Set session variables
			if ( $set_session_data ) :

				$this->session->set_userdata( 'id',			$_user->id );
				$this->session->set_userdata( 'email',		$_user->email );
				$this->session->set_userdata( 'group_id',	$_user->group_id );

			endif;

			//	Set the active user
			$this->set_active_user( $_user );

			return TRUE;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Clears the login data for a user
	 *
	 * @access	public
	 * @return	void
	 *
	 **/
	public function clear_login_data()
	{
		//	Clear the session
		$this->session->unset_userdata( 'id' );
		$this->session->unset_userdata( 'email' );
		$this->session->unset_userdata( 'group_id' );

		//	Set the flag
		$this->_is_logged_in = FALSE;

		//	Reset the active_user
		$this->clear_active_user();
	}


	// --------------------------------------------------------------------------


	/**
	 * Determines whether the active user is logged in or not.
	 *
	 * @access	public
	 * @return	bool
	 *
	 **/
	public function is_logged_in()
	{
		return $this->_is_logged_in;
	}


	// --------------------------------------------------------------------------


	/**
	 * Determines whether the active user is to be remembered
	 *
	 * @access	public
	 * @return	bool
	 *
	 **/
	public function is_remembered()
	{
		//	Deja vu?
		if ( NULL !== $this->_is_remembered ) :

			return $this->_is_remembered;

		endif;

		// --------------------------------------------------------------------------

		//	Look for the remember me cookie and explode it, if we're landed with
		//	a 2 part array then it's likely this is a valid cookie - however, this
		//	test is, obviously, not gonna detect a spoof.

		$this->load->helper( 'cookie' );

		$_cookie = get_cookie( $this->_remember_cookie );
		$_cookie = explode( '|', $_cookie );

		$this->_is_remembered = count( $_cookie ) == 2 ? TRUE : FALSE;

		return $this->_is_remembered;
	}


	// --------------------------------------------------------------------------


	/**
	 * Determines whether the active user group has admin permissions.
	 *
	 * @access	public
	 * @return	boolean
	 *
	 **/
	public function is_admin( $user = NULL )
	{
		if ( $this->is_superuser( $user ) ) :

			return TRUE;

		endif;

		return $this->has_permission( 'admin', $user );
	}


	// --------------------------------------------------------------------------


	/**
	 * Determines whether the active user is a superuser. Extend this method to
	 * alter it's response.
	 *
	 * @access	public
	 * @return	boolean
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
	 *
	 **/
	public function was_admin()
	{
		return (bool) $this->session->userdata( 'admin_recovery' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Determines whether the specified user has a certain ACL permission
	 *
	 * @access	public
	 * @param	string	$permission	The permission to check for, in the format admin.account.view
	 * @param	mixed	$user		The user to check for; if null uses active user, if numeric, fetches suer, if object uses that object
	 * @return	boolean
	 *
	 **/
	public function has_permission( $permission, $user = NULL )
	{
		//	Fetch the correct ACL
		if ( is_numeric( $user ) ) :

			$_user = $this->get_by_id( $user );

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

		if ( ! $_acl ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Super users can do anything they damn well please
		if ( isset( $_acl['superuser'] ) && $_acl['superuser'] ) :

			return TRUE;

		endif;

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
	 *
	 **/
	public function get_all( $extended = NULL, $order = NULL, $limit = NULL, $where = NULL, $search = NULL )
	{
		//	Write selects
		$this->db->select( 'u.*' );
		$this->db->select( 'ue.email, ue.is_verified email_is_verified, ue.date_verified email_is_verified_on' );
		$this->db->select( $this->_get_meta_columns() );
		$this->db->select( 'uam.type AS `auth_type`' );
		$this->db->select( 'ug.display_name AS `group_name`' );
		$this->db->select( 'ug.default_homepage AS `group_homepage`' );
		$this->db->select( 'ug.acl AS `group_acl`' );
		$this->db->select( 'dfd.label date_format_date_label, dfd.format date_format_date_format' );
		$this->db->select( 'dft.label date_format_time_label, dft.format date_format_time_format' );
		$this->db->select( 'ul.name language_name, ul.slug language_slug' );

		// --------------------------------------------------------------------------

		//	Set Order
		if ( is_array( $order ) ) :

			$this->db->order_by( $order[0], $order[1] );

		endif;

		// --------------------------------------------------------------------------

		//	Set Limit
		if ( is_array( $limit ) ) :

			$this->db->limit( $limit[0], $limit[1] );

		endif;

		// --------------------------------------------------------------------------

		//	Build conditionals
		$this->_getcount_users_common( $where, $search );

		// --------------------------------------------------------------------------

		//	Execute Query
		$q = $this->db->get( NAILS_DB_PREFIX . 'user u' );

		if ( ! $q ) :

			return array();

		endif;

		$_user = $q->result();

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

				$user->user_acl	= unserialize( $user->user_acl );
				$user->acl		= array();

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

			//	Format the user object
			$this->_format_user_object( $user );

		endforeach;

		// --------------------------------------------------------------------------

		//	Return the data
		return $_user;
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
	 *
	 **/
	public function get_all_minimal( $order = NULL, $limit = NULL, $where = NULL, $search = NULL )
	{
		//	Write selects
		$this->db->select( 'u.id, ue.email, ue.is_verified email_is_verified, ue.date_verified email_is_verified_on, u.first_name, u.last_name, u.profile_img, u.gender' );

		// --------------------------------------------------------------------------

		//	Set Order
		if ( is_array( $order ) ) :

			$this->db->order_by( $order[0], $order[1] );

		endif;

		// --------------------------------------------------------------------------

		//	Set Limit
		if ( is_array( $limit ) ) :

			$this->db->limit( $limit[0], $limit[1] );

		endif;

		// --------------------------------------------------------------------------

		//	Build conditionals
		$this->_getcount_users_common( $where, $search );

		// --------------------------------------------------------------------------

		//	Execute Query
		$q		= $this->db->get( NAILS_DB_PREFIX . 'user u' );
		$_user	= $q->result();

		// --------------------------------------------------------------------------

		//	Determine the user's ACL
		foreach ( $_user AS $user ) :

			//	Format the user object
			$this->_format_user_object( $user, TRUE );

		endforeach;

		// --------------------------------------------------------------------------

		//	Return the data
		return $_user;
	}


	// --------------------------------------------------------------------------


	/**
	 * Counts the total amount of users for a partricular query/search key. Essentially performs
	 * the same query as $this->get_all() but without limiting.
	 *
	 * @access	public
	 * @param	string	$where	An array of where conditions
	 * @param	mixed	$search	A string containing the search terms
	 * @return	int
	 *
	 **/
	public function count_all( $where = NULL, $search = NULL )
	{
		$this->_getcount_users_common( $where, $search );

		// --------------------------------------------------------------------------

		//	Execute Query
		return $this->db->count_all_results( NAILS_DB_PREFIX . 'user u' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Consolidates the extra calls which need to be made (to save having the same calls in get_all and count_all)
	 *
	 * @access	protected
	 * @param	string	$where	An array of where conditions
	 * @param	mixed	$search	A string containing the search terms
	 * @return	int
	 *
	 **/
	protected function _getcount_users_common( $where = NULL, $search = NULL )
	{
		$this->db->join( NAILS_DB_PREFIX . 'user_email ue',			'u.id = ue.user_id AND ue.is_primary = 1',	'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'user_meta um',			'u.id = um.user_id',						'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'user_auth_method uam',	'u.auth_method_id = uam.id',				'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'user_group ug',			'u.group_id = ug.id',						'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'date_format_date dfd',	'u.date_format_date_id = dfd.id',			'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'date_format_time dft',	'u.date_format_time_id = dft.id',			'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'language ul',				'u.language_id = ul.id',				'LEFT' );

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
			$search['columns']['email']		= 'ue.email';
			$search['columns']['username']	= 'u.username';
			$search['columns']['name']		= array( ' ', 'u.first_name', 'u.last_name' );

		endif;

		//	If there is a search term to use then build the search query
		if ( isset( $search[ 'keywords' ] ) && $search[ 'keywords' ] ) :

			//	Parse the keywords, look for specific column searches
			preg_match_all( '/\(([a-zA-Z0-9\.\- \_]+)=\"(.+?)\"\)/', $search['keywords'], $_matches );

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
				$search['keywords'] = preg_replace( '/\(([a-zA-Z0-9\.\- ]+):([a-zA-Z0-9\.\- ]+)\)/', '', $search['keywords'] );

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


	protected function _get_meta_columns()
	{
		$_cols = array();

		// --------------------------------------------------------------------------

		//	Module: shop
		if ( module_is_enabled( 'shop' ) ) :

			$_cols[] = 'um.shop_basket';
			$_cols[] = 'um.shop_currency';

		endif;

		// --------------------------------------------------------------------------

		return $_cols;
	}


	// --------------------------------------------------------------------------


	/**
	 * Get a specific user by their ID
	 *
	 * @access	public
	 * @param	string	$user_id	The user's ID
	 * @param	mixed	$extended	Specific extra tables to join, TRUE for all user_meta_*
	 * @return	object
	 *
	 **/
	public function get_by_id( $user_id, $extended = FALSE )
	{
		if ( ! is_numeric( $user_id ) ) :

			return FALSE;

		endif;

		$this->db->where( 'u.id', (int) $user_id );
		$user = $this->get_all( $extended );

		return empty( $user ) ? FALSE : $user[0];
	}


	// --------------------------------------------------------------------------




	/**
	 * Get a specific user by their email address
	 *
	 * @access	public
	 * @param	string	$email		The user's email address
	 * @param	mixed	$extended	Specific extra tables to join, TRUE for all user_meta_*
	 * @return	object
	 *
	 **/
	public function get_by_email( $email, $extended = FALSE )
	{
		if ( ! is_string( $email ) ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Look up the email, and if we find an ID then fetch that user
		$this->db->select( 'user_id' );
		$this->db->where( 'email', trim( $email ) );
		$_id = $this->db->get( NAILS_DB_PREFIX . 'user_email' )->row();

		if ( $_id ) :

			return $this->get_by_id( $_id->user_id );

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Get a specific user by their username
	 *
	 * @access	public
	 * @param	string	$user_id	The user's ID
	 * @param	mixed	$extended	Specific extra tables to join, TRUE for all user_meta_*
	 * @return	object
	 *
	 **/
	public function get_by_username( $username, $extended = FALSE )
	{
		if ( ! is_string( $username ) ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$this->db->where( 'u.username', trim( $username ) );
		$user = $this->get_all( $extended );

		return empty( $user ) ? FALSE : $user[0];
	}


	// --------------------------------------------------------------------------


	/**
	 * Get a specific user by their Facebook ID
	 *
	 * @access	public
	 * @param	int		$fbid		The user's Facebook ID
	 * @param	mixed	$extended	Specific extra tables to join, TRUE for all user_meta_*
	 * @return	object
	 *
	 **/
	public function get_by_fbid( $fbid, $extended = FALSE )
	{
		$this->db->where( 'u.fb_id', $fbid );
		$user = $this->get_all( $extended );

		return empty( $user ) ? FALSE : $user[0];
	}


	// --------------------------------------------------------------------------


	/**
	 * Get a specific user by their Twitter ID
	 *
	 * @access	public
	 * @param	int		$twid		The user's Twitter ID
	 * @param	mixed	$extended	Specific extra tables to join, TRUE for all user_meta_*
	 * @return	object
	 *
	 **/
	public function get_by_twid( $twid, $extended = FALSE )
	{
		$this->db->where( 'u.tw_id', $twid );
		$user = $this->get_all( $extended );

		return empty( $user ) ? FALSE : $user[0];
	}


	// --------------------------------------------------------------------------


	/**
	 * Get a specific user by their LinkedIn ID
	 *
	 * @access	public
	 * @param	int		$fbid		The user's LinkedIn ID
	 * @param	mixed	$extended	Specific extra tables to join, TRUE for all user_meta_*
	 * @return	object
	 *
	 **/
	public function get_by_liid( $linkedinid, $extended = FALSE )
	{
		$this->db->where( 'u.li_id', $linkedinid );
		$user = $this->get_all( $extended );

		return empty( $user ) ? FALSE : $user[0];
	}


	// --------------------------------------------------------------------------




	/**
	 * Get a specific user by the MD5 hash of their ID and password
	 *
	 * @access	public
	 * @param	string	$_hash_id	The user's id as an MD5 hash
	 * @param	mixed	$_hash_pw	The user's hashed password as an MD5 hash
	 * @return	object
	 *
	 **/
	public function get_by_hashes( $_hash_id, $_hash_pw, $extended = FALSE )
	{
		if ( empty( $_hash_id ) || empty( $_hash_pw ) ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Set wheres
		$this->db->where( 'u.id_md5',		$_hash_id );
		$this->db->where( 'u.password_md5',	$_hash_pw );

		// --------------------------------------------------------------------------

		//	Do it
		$this->db->limit( 1 );
		$q = $this->get_all( $extended );

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
	 *
	 **/
	public function get_by_referral( $referral_code, $extended = FALSE  )
	{
		$this->db->where( 'u.referral', $referral_code );
		$user = $this->get_all( $extended );

		return empty( $user ) ? FALSE : $user[0];
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns recent users; ordered by ID, desc
	 *
	 * @access	public
	 * @param	int			$limit		The number of user's to return
	 * @param	boolean		$extended	Whether to include extended data or not
	 * @return	object
	 *
	 **/
	public function get_new_users( $limit = 25, $extended = FALSE )
	{
		$this->db->limit( $limit );
		$this->db->order_by( 'u.id', 'desc' );
		return $this->get_all( $extended );
	}



	// --------------------------------------------------------------------------



	public function get_emails_for_user( $id )
	{
		$this->db->where( 'user_id', $id );
		$this->db->order_by( 'is_primary', 'DESC' );
		$this->db->order_by( 'email', 'ASC' );
		return $this->db->get( NAILS_DB_PREFIX . 'user_email' )->result();
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
	 *
	 **/
	public function update( $user_id = NULL, $data = NULL )
	{
		$data = (array) $data;

		//	Get the user ID to update
		if ( NULL !== $user_id && $user_id !== FALSE ) :

			$_uid = $user_id;

		elseif ( active_user( 'id' ) ) :

			$_uid = active_user( 'id' );

		else :

			$this->_set_error( 'No user ID set' );
			return FALSE;

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
			$_cols[]	= 'ip_address';
			$_cols[]	= 'last_ip';
			$_cols[]	= 'password';
			$_cols[]	= 'password_md5';
			$_cols[]	= 'salt';
			$_cols[]	= 'forgotten_password_code';
			$_cols[]	= 'remember_code';
			$_cols[]	= 'created';
			$_cols[]	= 'last_login';
			$_cols[]	= 'last_seen';
			$_cols[]	= 'is_suspended';
			$_cols[]	= 'temp_pw';
			$_cols[]	= 'failed_login_count';
			$_cols[]	= 'failed_login_expires';
			$_cols[]	= 'last_update';
			$_cols[]	= 'user_acl';
			$_cols[]	= 'login_count';
			$_cols[]	= 'admin_nav';
			$_cols[]	= 'admin_dashboard';
			$_cols[]	= 'referral';
			$_cols[]	= 'referred_by';
			$_cols[]	= 'salutation';
			$_cols[]	= 'first_name';
			$_cols[]	= 'last_name';
			$_cols[]	= 'gender';
			$_cols[]	= 'profile_img';
			$_cols[]	= 'timezone';
			$_cols[]	= 'date_format_date_id';
			$_cols[]	= 'date_format_time_id';
			$_cols[]	= 'language_id';

			//	Safety first, no updating of user's ID.
			unset( $data->id );
			unset( $data->id_md5 );

			//	If we're updating the user's password we should generate a new hash
			if (  array_key_exists( 'password', $data ) ) :

				$_hash = $this->hash_password( $data['password'] );

				$data['password']		= $_hash[0];
				$data['password_md5']	= md5( $_hash[0] );
				$data['salt']			= $_hash[1];
				$_password_updated		= TRUE;

			else :

				$_password_updated		= FALSE;

			endif;

			//	Set the data
			$_data_user		= array();
			$_data_meta		= array();
			$_data_email	= '';
			$_data_username	= '';

			foreach ( $data AS $key => $val ) :

				//	user or user_meta?
				if ( array_search( $key, $_cols ) !== FALSE ) :

					//	Careful now, some items cannot be blank and must be NULL
					switch( $key ) :

						case 'profile_img' :

							$_data_user[$key] = $val ? $val : NULL;

						break;

						default :

							$_data_user[$key] = $val;

						break;

					endswitch;

				elseif ( $key == 'email' ) :

					$_data_email = trim( $val );

				elseif ( $key == 'username' ) :

					$_data_username = trim( $val );

				else :

					$_data_meta[$key] = $val;

				endif;

			endforeach;

			// --------------------------------------------------------------------------

			//	If a username has been passed then check if it's available
			if ( $_data_username ) :

				//	Check if the username is already being used
				$this->db->where( 'username', $_data_username );
				$this->db->where( 'id !=', $_uid );
				$_username = $this->db->get( NAILS_DB_PREFIX . 'user' )->row();

				if ( $_username ) :

					$this->_set_error( 'Username is already in use.' );
					return FALSE;

				else :

					$_data_user['username'] = $_data_username;

				endif;

			endif;

			// --------------------------------------------------------------------------

			//	Begin transaction
			$_rollback = FALSE;
			$this->db->trans_begin();

			// --------------------------------------------------------------------------

			//	Update the user table
			$this->db->where( 'id', (int) $_uid );
			$this->db->set( 'last_update', 'NOW()', FALSE );

			if ( $_data_user ) :

				$this->db->set( $_data_user );

			endif;

			$this->db->update( NAILS_DB_PREFIX . 'user' );

			// --------------------------------------------------------------------------

			//	Update the meta table
			if ( $_data_meta ) :

				$this->db->where( 'user_id', (int) $_uid );
				$this->db->set( $_data_meta );
				$this->db->update( NAILS_DB_PREFIX . 'user_meta' );

			endif;

			// --------------------------------------------------------------------------

			//	If an email has been passed then attempt to update the user's email too
			if ( $_data_email ) :

				$this->load->helper( 'email' );

				if ( valid_email( $_data_email ) ) :

					//	Check if the email is already being used
					$this->db->where( 'email', $_data_email );
					$_email = $this->db->get( NAILS_DB_PREFIX . 'user_email' )->row();

					if ( $_email ) :

						//	Email is in use, if it's in use by the ID of this user then
						//	set it as the primary email for this account. If it's in use
						//	by another user then error

						if ( $_email->user_id == $_uid ) :

							$this->email_make_primary( $_email->email );

						else :

							$this->_set_error( 'Email is already in use.' );
							$_rollback = TRUE;

						endif;

					else :

						//	Doesn't appear to be in use, add as a new email address and
						//	make it the primary one

						$this->email_add( (int) $_uid, $_data_email, TRUE );

					endif;

				else :

					//	Error, not a valid email; roll back transaction
					$this->_set_error( '"' . $_data_email . '" is not a valid email address.' );
					$_rollback = TRUE;

				endif;

			endif;

			// --------------------------------------------------------------------------

			//	How'd we get on?
			if ( ! $_rollback && $this->db->trans_status() !== FALSE ) :

				$this->db->trans_commit();

				// --------------------------------------------------------------------------

				//	If the user's password was updated send them a notification
				if ( $_password_updated ) :

					$this->load->library( 'emailer' );

					$_email						= new stdClass();
					$_email->type				= 'password_updated';
					$_email->to_id				= $_uid;
					$_email->data				= array();
					$_email->data['updated_at']	= date( 'Y-m-d H:i:s' );
					$_email->data['updated_by']	= array( 'id' => active_user( 'id' ), 'name' => active_user( 'first_name,last_name' ) );
					$_email->data['ip_address']	= $this->input->ip_address();

					$this->emailer->send( $_email, TRUE );

				endif;

			else :

				$this->db->trans_rollback();
				return FALSE;

			endif;

		else :

			//	If there was no data then run an update anyway on just user table. We need to do this
			//	As some methods will use $this->db->set() before calling update(); not sure if this is
			//	a bad design or not... sorry.

			$this->db->set( 'last_update', 'NOW()', FALSE );
			$this->db->where( 'id', (int) $_uid );
			$this->db->update( NAILS_DB_PREFIX . 'user' );

		endif;

		// --------------------------------------------------------------------------

		//	If we just updated the active user we should probably update their session info
		if ( $_uid == active_user( 'id' ) ) :

			$this->_active_user->last_update = date( 'Y-m-d H:i:s' );

			if ( $data ) :

				foreach( $data AS $key => $val ) :

					$this->_active_user->{$key} = $val;

				endforeach;

			endif;

			// --------------------------------------------------------------------------

			//	If there's a remember me cookie then update that too, but only if the password
			//	or email address has changed

			if ( ( isset( $data['email'] ) || isset( $data['password'] ) ) && $this->is_remembered() ) :

				$this->set_remember_cookie();

			endif;

		endif;

		// --------------------------------------------------------------------------

		return TRUE;
	}


	// --------------------------------------------------------------------------


	public function email_add( $user_id, $email, $is_primary = FALSE, $is_verified = FALSE, $send_email = TRUE )
	{
		$_email	= trim( strtolower( $email ) );
		$_u		= $this->get_by_id( $user_id );

		if ( ! $_u ) :

			$this->_set_error( 'Invalid User ID' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Test email, if it's in use and for the same user then return true. If
		//	it's in use by a different user then return an error.

		$this->db->select( 'id, user_id, is_verified, code' );
		$this->db->where( 'email', $_email );
		$_test = $this->db->get( NAILS_DB_PREFIX . 'user_email' )->row();

		if ( $_test ) :

			if ( $_test->user_id == $_u->id ) :

				//	In use, but belongs to the same user - return the code
				//	(imitates behavior of newly added email)

				if ( $is_primary ) :

					$this->email_make_primary( $_test->id );

				endif;

				//	Resend verification email?
				if ( ! $_test->is_verified ) :

					$this->_email_add_send_verify( $_u->id, $_u->group_id, $_test->code );

				endif;

				return $_test->code;

			else :

				//	In use, but belongs to another user
				$this->_set_error( 'Email in use by another user.' );
				return FALSE;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Make sure the email is valid
		$this->load->helper( 'email' );
		if ( ! valid_email( $_email ) ) :

			$this->set_error( '"' . $_email . '" is not a valid email address' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$_code = $this->salt();

		$this->db->set( 'user_id',		$_u->id );
		$this->db->set( 'email',		$_email );
		$this->db->set( 'code',			$_code );
		$this->db->set( 'is_verified',	(bool) $is_verified );
		$this->db->set( 'date_added',	'NOW()', FALSE );

		$this->db->insert( NAILS_DB_PREFIX . 'user_email' );

		if ( $this->db->affected_rows() ) :

			//	Make it the primary email address?
			if ( $is_primary ) :

				$this->email_make_primary( $this->db->insert_id() );

			endif;

			//	Send off the verification email
			if ( $send_email && ! $is_verified ) :

				$this->_email_add_send_verify( $_u->id, $_u->group_id, $_code );

			endif;

			return $_code;

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _email_add_send_verify( $user_id, $user_group, $code )
	{
		$this->load->library( 'emailer' );

		$_email						= new stdClass();
		$_email->type				= 'verify_email_' . $user_group;
		$_email->to_id				= $user_id;
		$_email->data				= array();
		$_email->data['user_id']	= $user_id;
		$_email->data['code']		= $code;

		if ( ! $this->emailer->send( $_email, TRUE ) ) :

			//	Failed to send using the group email, try using the generic email template
			$_email->type = 'verify_email';

			if ( ! $this->emailer->send( $_email, TRUE ) ) :

				//	Email failed to send, for now, do nothing.

			endif;

		endif;
	}


	// --------------------------------------------------------------------------


	public function email_delete( $email )
	{
		$this->db->where( 'is_primary', FALSE );
		$this->db->where( 'email', $email );
		$this->db->delete( NAILS_DB_PREFIX . 'user_email' );

		return (bool) $this->db->affected_rows();
	}


	// --------------------------------------------------------------------------


	public function email_verify( $id_email, $code )
	{
		//	Check user exists
		if ( is_numeric( $id_email ) ) :

			$_user = $this->get_by_id( $id_email );

		else :

			$_user = $this->get_by_email( $id_email );

		endif;

		if ( ! $_user ) :

			$this->_set_error( 'User does not exist.' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Check if email has already been verified
		$this->db->where( 'user_id', $_user->id );
		$this->db->where( 'is_verified', TRUE );
		$this->db->where( 'code', $code );

		if ( $this->db->count_all_results( NAILS_DB_PREFIX . 'user_email' ) ) :

			$this->_set_error( 'Email has already been verified.' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Go ahead and set as verified
		$this->db->set( 'is_verified', TRUE );
		$this->db->set( 'date_verified', 'NOW()', FALSE );
		$this->db->where( 'user_id', $_user->id );
		$this->db->where( 'is_verified', FALSE );
		$this->db->where( 'code', $code );

		$this->db->update( NAILS_DB_PREFIX . 'user_email' );

		return (bool) $this->db->affected_rows();
	}


	// --------------------------------------------------------------------------


	public function email_make_primary( $email )
	{
		//	Fetch other emails
		$this->db->select( 'id,user_id,email' );

		if ( is_numeric( $email ) ) :

			$this->db->where( 'id', $email );

		else :

			$this->db->where( 'email', $email );

		endif;

		$_email = $this->db->get( NAILS_DB_PREFIX . 'user_email' )->row();

		if ( ! $_email ) :

			return FALSE;

		endif;

		//	Update
		$this->db->trans_begin();

			$this->db->set( 'is_primary', FALSE );
			$this->db->where( 'user_id', $_email->user_id );
			$this->db->update( NAILS_DB_PREFIX . 'user_email' );

			$this->db->set( 'is_primary', TRUE );
			$this->db->where( 'id', $_email->id );
			$this->db->update( NAILS_DB_PREFIX . 'user_email' );

		$this->db->trans_complete();

		if ( $this->db->trans_status() === FALSE ) :

			$this->db->trans_rollback();
			return FALSE;

		else :

			$this->db->trans_commit();
			return TRUE;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Increase the user's failed login account by 1
	 *
	 * @access	public
	 * @param	int		$user_id	The ID of the user to increment
	 * @param	int		$expires	Time (in seconds) until expiration
	 * @return	void
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
	 **/
	public function set_remember_cookie( $id = NULL, $password = NULL, $email = NULL )
	{
		if ( ! $id || ! $password || ! $email ) :

			if ( ! active_user( 'id' ) ||  ! active_user( 'password' ) || ! active_user( 'email' ) ) :

				return FALSE;

			else :

				$id			= active_user( 'id' );
				$password	= active_user( 'password' );
				$email		= active_user( 'email' );

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Generate a code to remember the user by and save it to the DB

		$_salt = $this->encrypt->encode( sha1( $id . $password . $email . APP_PRIVATE_KEY. time() ), APP_PRIVATE_KEY );

		$this->db->set( 'remember_code', $_salt );
		$this->db->where( 'id', $id );
		$this->db->update( NAILS_DB_PREFIX . 'user' );

		// --------------------------------------------------------------------------

		//	Set the cookie
		$_data				= array();
		$_data['name']		= $this->_remember_cookie;
		$_data['value']		= $email . '|' . $_salt;
		$_data['expire']	= 1209600; //	2 weeks

		set_cookie( $_data );

		// --------------------------------------------------------------------------

		//	Update the flag
		$this->_is_remembered = TRUE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Clears the user's remember me cookie
	 *
	 * @access	public
	 * @return	void
	 **/
	public function clear_remember_cookie()
	{
		$this->load->helper( 'cookie' );

		// --------------------------------------------------------------------------

		delete_cookie( $this->_remember_cookie );

		// --------------------------------------------------------------------------

		//	Update the flag
		$this->_is_remembered = FALSE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Generate a unique salt
	 *
	 * @access	public
	 * @param	none
	 * @return	string
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
	 **/
	public function hash_password( $password, $salt = FALSE )
	{
		if ( empty( $password ) ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		if ( ! $salt ) :

			$salt = $this->salt();

		endif;

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
	 **/
	public function hash_password_db( $user_id, $password )
	{
		if ( empty( $user_id ) || empty( $password ) ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$this->db->select( 'u.password, u.salt' );
		$this->db->where( 'u.id', $user_id );
		$this->db->limit( 1 );
		$_q = $this->db->get( NAILS_DB_PREFIX . 'user u' );

		// --------------------------------------------------------------------------

		if ( $_q->num_rows() !== 1 ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		return sha1( sha1( $password ) . $_q->row()->salt );

	}


	// --------------------------------------------------------------------------


	/**
	 * Refreshes the user's session from database data
	 *
	 * @access	protected
	 * @return	void
	 *
	 **/
	protected function _refresh_session()
	{
		//	Get the user; be wary of admin's logged in as other people
		if ( $this->was_admin() ) :

			$_admin = $this->session->userdata( 'admin_recovery');

			if ( ! empty( $_admin->logged_in_as ) ) :

				$_me = $_admin->logged_in_as;

			else :

				$_me = $this->session->userdata( 'id' );

			endif;

		else :

			$_me = $this->session->userdata( 'id' );

		endif;

		//	Is anybody home? Hello...?
		if ( ! $_me ) :

			$_me = $this->_me;

			if ( ! $_me ) :

				return FALSE;

			endif;

		endif;

		$_me = $this->get_by_id( $_me );

		// --------------------------------------------------------------------------

		//	Store this entire user in memory
		$this->set_active_user( $_me );

		// --------------------------------------------------------------------------

		//	Set the user's logged in flag
		$this->_is_logged_in = TRUE;

		// --------------------------------------------------------------------------

		//	Update user's 'last_seen' flag
		$this->db->set( 'last_seen', 'NOW()', FALSE );
		$this->db->where( 'id', $_me->id );
		$this->db->update( NAILS_DB_PREFIX . 'user' );
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
	 **/
	public function extra_table_insert( $table, $data, $user_id = FALSE )
	{
		$_uid = ! $user_id ? (int) active_user( 'id' ) : $user_id ;

		// --------------------------------------------------------------------------

		//	Unable to determine user ID
		if ( ! $_uid ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$data			= (object) $data;
		$data->user_id	= $_uid;

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
	 **/
	public function extra_table_fetch( $table, $id = NULL, $user_id = NULL )
	{
		$_uid = ! $user_id ? (int) active_user( 'id' ) : $user_id ;

		// --------------------------------------------------------------------------

		//	Unable to determine user ID
		if ( ! $_uid ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$this->db->where( 'user_id', $_uid );

		// --------------------------------------------------------------------------

		//	Add restriction if necessary
		if ( $id ) :

			$this->db->where( 'id', $id );

		endif;

		// --------------------------------------------------------------------------

		$_row = $this->db->get( $table );

		return $id ? $_row->row() : $_row->result();
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
	 **/
	public function extra_table_update( $table, $data, $user_id = FALSE )
	{
		$_uid = ! $user_id ? (int) active_user( 'id' ) : $user_id ;

		// --------------------------------------------------------------------------

		//	Unable to determine user ID
		if ( ! $_uid ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$data = (object) $data;

		// --------------------------------------------------------------------------

		if ( ! isset( $data->id ) || empty( $data->id ) ) :

			return FALSE;

		endif;

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
	 **/
	public function extra_table_delete( $table, $id, $user_id = FALSE )
	{
		$_uid = ! $user_id ? (int) active_user( 'id' ) : $user_id ;

		// --------------------------------------------------------------------------

		//	Unable to determine user ID
		if ( ! $_uid ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		if ( ! isset( $id ) || empty( $id ) ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$this->db->where( 'user_id', $_uid );
		$this->db->where( 'id', $id );
		$this->db->delete( $table );

		// --------------------------------------------------------------------------

		return (bool) $this->db->affected_rows();
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
	 **/
	public function create( $email, $password, $group_id = NULL, $data = FALSE, $send_welcome = TRUE )
	{
		if ( ! $email ) :

			$this->_set_error( '"email" is a required field' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Check email against DB
		$this->db->where( 'email', $email );
		if (  $this->db->count_all_results( NAILS_DB_PREFIX . 'user_email' ) ) :

			$this->_set_error( 'This email is already in use.' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	All should be ok, go ahead and create the account
		$_ip_address = $this->input->ip_address();

		// --------------------------------------------------------------------------

		//	If a password has been passed then generate the encrypted strings, otherwise
		//	just generate a salt.

		if ( NULL === $password ) :

			$_password[] = NULL;
			$_password[] = $this->salt();

		else :

			$_password = $this->hash_password( $password );

		endif;

		//	Do we need to inform the user of their password? This might be set
		//	if an admin created the account, or if the system generated a new password

		$_inform_user_pw = ! empty( $data['inform_user_pw'] ) ? TRUE : FALSE;
		unset( $data['inform_user_pw'] );

		// --------------------------------------------------------------------------

		//	Check that we're dealing with a valid group
		if ( NULL === $group_id ) :

			$group_id = APP_USER_DEFAULT_GROUP;

		endif;

		$_group = $this->get_group( $group_id );

		if ( ! $_group ) :

			$this->_set_error( 'Invalid Group ID specified.' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Check we're dealing with a valid auth_method
		if ( ! empty( $data['auth_method_id'] ) ) :

			if ( is_numeric( $data['auth_method_id'] ) ) :

				$this->db->where( 'id', (int) $data['auth_method_id'] );

			else :

				//	TODO: Change this column to be called `slug`
				$this->db->where( 'type', $data['auth_method_id'] );

			endif;

			$_auth_method = $this->db->get( NAILS_DB_PREFIX . 'user_auth_method' )->row();

			if ( ! $_auth_method ) :

				//	Define a use friendly error (this may be shown to them)
				$this->_set_error( 'There was an error creating the user account - Error #001' );

				//	This is a problem, email devs
				send_developer_mail( 'No auth method available for the supplied auth_method_id', 'The user_model->create() method was called with an invalid auth_method_id ("' . $data['auth_method_id'] . '"). This needs investigated and corrected.' );

				return FALSE;

			endif;

		else :

			//	TODO: this column should be `slug`
			$this->db->where( 'type', 'native' );
			$_auth_method = $this->db->get( NAILS_DB_PREFIX . 'user_auth_method' )->row();

			if ( ! $_auth_method ) :

				//	Define a use friendly error (this may be shown to them)
				$this->_set_error( 'There was an error creating the user account - Error #002' );

				//	This is a problem, email devs
				send_developer_mail( 'No Native Authentication Method', 'There is no authentication method defined in the database for native registrations.' );

				return FALSE;

			endif;

		endif;

		// --------------------------------------------------------------------------

		// Users table
		$_data						= array();
		$_data['password']			= $_password[0];
		$_data['password_md5']		= md5( $_password[0] );
		$_data['group_id']			= $group_id;
		$_data['ip_address']		= $_ip_address;
		$_data['last_ip']			= $_ip_address;
		$_data['created']			= date( 'Y-m-d H:i:s' );
		$_data['last_update']		= date( 'Y-m-d H:i:s' );
		$_data['is_suspended']		= isset( $data['is_suspended'] ) && $data['is_suspended']	? 1	: 0 ;
		$_data['salt']				= $_password[1];
		$_data['temp_pw']			= ! empty( $data['temp_pw'] )	? TRUE	: FALSE ;
		$_data['auth_method_id']	= $_auth_method->id;

		//	Facebook oauth details
		$_data['fb_token']			= ! empty( $data['fb_token'] )	? $data['fb_token']		: NULL ;
		$_data['fb_id']				= ! empty( $data['fb_id'] )		? $data['fb_id']		: NULL ;

		//	Twitter oauth details
		$_data['tw_id']				= ! empty( $data['tw_id'] )		? $data['tw_id']		: NULL ;
		$_data['tw_token']			= ! empty( $data['tw_token'] )	? $data['tw_token']		: NULL ;
		$_data['tw_secret']			= ! empty( $data['tw_secret'] )	? $data['tw_secret']	: NULL ;

		//	Linkedin oauth details
		$_data['li_id']				= ! empty( $data['li_id'] )		? $data['li_id']		: NULL ;
		$_data['li_token']			= ! empty( $data['li_token'] )	? $data['li_token']		: NULL ;

		//	Referral code
		$_data['referral']			= $this->_generate_referral();

		//	Other data
		$_data['salutation']		= ! empty( $data['salutation'] )	? $data['salutation']	: NULL ;
		$_data['first_name']		= ! empty( $data['first_name'] )	? $data['first_name']	: NULL ;
		$_data['last_name']			= ! empty( $data['last_name'] )		? $data['last_name']	: NULL ;

		if ( isset( $data['gender'] ) ) :

			$_data['gender'] = $data['gender'];

		endif;

		if ( isset( $data['timezone'] ) ) :

			$_data['timezone'] = $data['timezone'];

		elseif ( defined( 'APP_DEFAULT_TIMEZONE' ) && APP_DEFAULT_TIMEZONE ) :

			$_data['timezone'] = APP_DEFAULT_TIMEZONE;

		elseif ( defined( 'DEPLOY_SYSTEM_TIMEZONE' ) && DEPLOY_SYSTEM_TIMEZONE ) :

			$_data['timezone'] = DEPLOY_SYSTEM_TIMEZONE;

		else :

			$_data['timezone'] = 'UTC';

		endif;

		if ( isset( $data['date_format_date_id'] ) ) :

			$_data['date_format_date_id'] = $data['date_format_date_id'];

		endif;

		if ( isset( $data['date_format_time_id'] ) ) :

			$_data['date_format_time_id'] = $data['date_format_time_id'];

		endif;

		if ( isset( $data['language_id'] ) ) :

			$_data['language_id'] = $data['language_id'];

		endif;

		//	Unset extra data fields which have been used already
		unset( $data['temp_pw'] );
		unset( $data['is_verified'] );
		unset( $data['is_suspended'] );
		unset( $data['auth_method_id'] );
		unset( $data['fb_token'] );
		unset( $data['fb_id'] );
		unset( $data['tw_id'] );
		unset( $data['tw_token'] );
		unset( $data['tw_secret'] );
		unset( $data['li_id'] );
		unset( $data['li_token'] );
		unset( $data['salutation'] );
		unset( $data['first_name'] );
		unset( $data['last_name'] );
		unset( $data['gender'] );
		unset( $data['timezone'] );
		unset( $data['date_format_date_id'] );
		unset( $data['date_format_time_id'] );
		unset( $data['language_id'] );

		$this->db->set( $_data );
		$this->db->insert( NAILS_DB_PREFIX . 'user' );

		$_id = $this->db->insert_id();

		// --------------------------------------------------------------------------

		//	If a username has been supplied check it's unique, if it's not then use the
		//	User's ID (will be called in the MD5 query immediately following)

		if ( isset( $data['username'] ) && $data['username'] ) :

			$this->db->where( 'username' , trim( $data['username'] ) );

			if ( $this->db->count_all_results( NAILS_DB_PREFIX . 'user' ) ) :

				//	Not unique, use user ID
				$this->db->set( 'username', 'user' . $_id );

			else :

				//	Unique, go ahead and use it
				$this->db->set( 'username', trim( $data['username'] ) );

			endif;

			unset( $data['username'] );

		else :

			//	Not supplied, use user ID
			$this->db->set( 'username', 'user' . $_id );

		endif;

		// --------------------------------------------------------------------------

		//	Update the user table with an MD5 hash of the user ID; a number of functions
		//	make use of looking up this hashed information; this should be quicker.

		$this->db->set( 'id_md5', md5( $_id ) );
		$this->db->where( 'id', $_id );
		$this->db->update( NAILS_DB_PREFIX . 'user' );

		// --------------------------------------------------------------------------

		//	Create the user_meta record, add any extra data if needed
		$this->db->set( 'user_id', $_id );

		if ( $data ) :

			$this->db->set( $data );

		endif;

		$this->db->insert( NAILS_DB_PREFIX . 'user_meta' );

		// --------------------------------------------------------------------------

		//	Finally add the email address to the user_email table
		$_verified	= isset( $data['is_verified'] ) && $data['is_verified'] ? TRUE : FALSE ;
		$_code		= $this->email_add( $_id, $email, TRUE, $_verified, FALSE );

		// --------------------------------------------------------------------------

		//	Send the user the welcome email
		if ( $send_welcome ) :

			$this->load->library( 'emailer' );

			$_email					= new stdClass();
			$_email->type			= 'new_user_' . $group_id;
			$_email->to_id			= $_id;
			$_email->data			= array();
			$_email->data['method']	= $_auth_method;

			//	If this user is created by an admin then take note of that.
			if ( $this->is_admin() ) :

				$_email->data['admin']				= new stdClass();
				$_email->data['admin']->id			= active_user( 'id' );
				$_email->data['admin']->first_name	= active_user( 'first_name' );
				$_email->data['admin']->last_name	= active_user( 'last_name' );
				$_email->data['admin']->group		= new stdClass();
				$_email->data['admin']->group->id	= $_group->id;
				$_email->data['admin']->group->name	= $_group->display_name;

			endif;

			if ( ! empty( $_inform_user_pw ) ) :

				$_email->data['password'] = $password;

				//	Is this a temp password? We should let them know that too
				if ( $_data['temp_pw'] ) :

					$_email->data['temp_pw'] = $_data['temp_pw'];

				endif;

			endif;

			//	If the email isn't verified we'll want to include a note asking them to do so
			if ( ! $_verified ) :

				$_email->data['verification_code']	= $_code;

			endif;

			if ( ! $this->emailer->send( $_email, TRUE ) ) :

				//	Failed to send using the group email, try using the generic email template
				$_email->type = 'new_user';

				if ( ! $this->emailer->send( $_email, TRUE ) ) :

					//	Email failed to send, musn't exist, oh well.
					$_error  = 'Failed to send welcome email.';
					$_error .= ! empty( $_inform_user_pw ) ? ' Inform the user their password is <strong>' . $password . '</strong>' : '';

					$this->_set_error( $_error );

				endif;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Return useful user info
		$_out['id']			= $_id;
		$_out['activation']	= $_code;

		return $_out;
	}


	// --------------------------------------------------------------------------


	/**
	 * Delete a user
	 *
	 * @access	public
	 * @param	int		$id	The ID of the user to delete
	 * @return	boolean
	 **/
	public function destroy( $id )
	{
		$this->db->where( 'id', $id );
		$this->db->delete( NAILS_DB_PREFIX . 'user' );

		// --------------------------------------------------------------------------

		return (bool) $this->db->affected_rows();
	}


	// --------------------------------------------------------------------------


	/**
	 * Generate a valid referral code
	 *
	 * @access	protected
	 * @param	none
	 * @return	string
	 **/
	protected function _generate_referral()
	{
		$this->load->helper( 'string' );

		// --------------------------------------------------------------------------

		while ( 1 > 0 ) :

			$referral = random_string( 'alnum', 8 );
			$q = $this->db->get_where( NAILS_DB_PREFIX . 'user', array( 'referral' => $referral ) );
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
	 * @param	string $identifier The identifier to use for setting the token (set by APP_NATIVE_LOGIN_METHOD)
	 * @return	boolean
	 **/
	public function set_password_token( $identifier )
	{
		if ( empty( $identifier ) ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Generate code
		$_key = $this->hash_password( $this->salt() );
		$_ttl = time() + 86400; // 24 hours.

		// --------------------------------------------------------------------------

		//	Update the user
		switch( APP_NATIVE_LOGIN_USING ) :

			case 'EMAIL' :

				$_user = $this->get_by_email( $identifier );

			break;

			// --------------------------------------------------------------------------

			case 'USERNAME' :

				$_user = $this->get_by_username( $identifier );

			break;

			// --------------------------------------------------------------------------

			case 'BOTH' :
			default:

				$this->load->helper( 'email' );

				if ( valid_email( $identifier ) ) :

					$_user = $this->get_by_email( $identifier );

				else :

					$_user = $this->get_by_username( $identifier );

				endif;

			break;

		endswitch;

		if ( $_user ) :

			$_data = array(

				'forgotten_password_code' => $_ttl . ':' . $_key[0]
			);

			return $this->update( $_user->id, $_data );

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function reward_referral( $user_id, $referrer_id )
	{
		//	TODO
	}


	// --------------------------------------------------------------------------


	/**
	 * Validate a forgotten password code. If valid generate a new password and update user table
	 *
	 * @access	public
	 * @param	string
	 * @return	string or boolean FALSE
	 **/
	public function validate_password_token( $code )
	{
		if ( empty( $code ) ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$this->db->like( 'forgotten_password_code', ':' . $code, 'before' );
		$_q = $this->db->get( NAILS_DB_PREFIX . 'user' );

		// --------------------------------------------------------------------------

		if ( $_q->num_rows() != 1 ) :

			return FALSE;

		endif;

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
			$_data['temp_pw']					= TRUE;
			$_data['forgotten_password_code']	= NULL;

			// --------------------------------------------------------------------------

			$this->db->where( 'forgotten_password_code', $_user->forgotten_password_code );
			$this->db->set( $_data );
			$this->db->update( NAILS_DB_PREFIX . 'user' );

			return $_password;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Suspend a user
	 *
	 * @access	public
	 * @param	int		$id	The ID of the user to suspend
	 * @return	boolean
	 **/
	 public function suspend( $id )
	 {
	 	return $this->update( $id, array( 'is_suspended' => TRUE ) );
	 }


	 // --------------------------------------------------------------------------


	/**
	 * Unsuspend a user
	 *
	 * @access	public
	 * @param	int		$id	The ID of the user to unsuspend
	 * @return	boolean
	 **/
	 public function unsuspend( $id )
	 {
	 	return $this->update( $id, array( 'is_suspended' => FALSE ) );
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
	 **/
	public function get_groups()
	{
		$_groups = $this->db->get( NAILS_DB_PREFIX . 'user_group' )->result();

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
	 **/
	public function get_groups_flat()
	{
		$_groups	= $this->get_groups();
		$_out		= array();

		// --------------------------------------------------------------------------

		//	Loop through results and unserialise the ACL
		foreach( $_groups AS $group ) :

			$_out[$group->id] = $group->display_name;
			//	TODO: unserialize the ACL (check it's not being unserialized twice elsewhere)

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
	 **/
	public function get_group( $group_id )
	{
		if ( is_numeric( $group_id ) ) :

			$this->db->where( 'id', (int) $group_id );

		else :

			//	TODO: change this column to `slug`
			$this->db->where( 'name', $group_id );

		endif;

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
	 **/
	public function update_group( $id, $data )
	{
		$this->db->set( $data );
		$this->db->where( 'id', $id );
		$this->db->update( NAILS_DB_PREFIX . 'user_group' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Format a user object
	 *
	 * @access	protected
	 * @param	object	$user	The user object to format
	 * @param	array	$data	The data to use in the update
	 * @return	void
	 **/
	protected function _format_user_object( &$user, $minimal = FALSE )
	{
		if ( $minimal ) :

			$user->id = (int) $user->id;

		else :

			//	Ints
			$user->id					= (int) $user->id;
			$user->auth_method_id		= (int) $user->auth_method_id;
			$user->group_id				= (int) $user->group_id;
			$user->login_count			= (int) $user->login_count;
			$user->referred_by			= (int) $user->referred_by;
			$user->failed_login_count	= (int) $user->failed_login_count;

			//	Bools
			$user->temp_pw				= (bool) $user->temp_pw;
			$user->is_suspended			= (bool) $user->is_suspended;
			$user->email_is_verified	= (bool) $user->email_is_verified;
			$user->email_is_verified_on	= $user->email_is_verified_on;

			//	Dates (TODO)

			// --------------------------------------------------------------------------

			//	Social Networks (TODO)

			// --------------------------------------------------------------------------

			//	Tidy up date/time/timezone field
			$user->date_setting					= new stdClass();
			$user->date_setting->format			= new stdClass();
			$user->date_setting->format->date	= new stdClass();
			$user->date_setting->format->time	= new stdClass();

			$user->date_setting->format->date->id		= (int) $user->date_format_date_id;
			$user->date_setting->format->date->label	= $user->date_format_date_label;
			$user->date_setting->format->date->format	= $user->date_format_date_format;

			$user->date_setting->format->time->id		= (int) $user->date_format_time_id;
			$user->date_setting->format->time->label	= $user->date_format_time_label;
			$user->date_setting->format->time->format	= $user->date_format_time_format;

			//	Set an easy access pref
			$user->pref_date_format						= $user->date_format_date_format;
			$user->pref_time_format						= $user->date_format_time_format;

			unset( $user->date_format_date_id );
			unset( $user->date_format_date_label );
			unset( $user->date_format_date_format );

			unset( $user->date_format_time_id );
			unset( $user->date_format_time_label );
			unset( $user->date_format_time_format );

			// --------------------------------------------------------------------------

			//	Tidy up language field
			$user->language_setting			= new stdClass();
			$user->language_setting->id		= (int) $user->language_id;
			$user->language_setting->name	= $user->language_name;
			$user->language_setting->slug	= $user->language_slug;

			unset( $user->language_id );
			unset( $user->language_name );
			unset( $user->language_slug );

			// --------------------------------------------------------------------------

			//	Tidy User meta
			unset( $user->user_id );

		endif;
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' MODELS
 *
 * The following block of code makes it simple to extend one of the core
 * models. Some might argue it's a little hacky but it's a simple 'fix'
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
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if ( ! defined( 'NAILS_ALLOW_EXTENSION_USER_MODEL' ) ) :

	class User_model extends NAILS_User_model
	{
	}

endif;

/* End of file user_model.php */
/* Location: ./system/application/models/user_model.php */