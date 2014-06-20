<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		User_password_model
 *
 * Description:	The user group model handles user's passwords
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_User_password_model extends CI_Model
{
	protected $_user;
	protected $_errors;
	protected $_loaded_drivers;
	protected $_pw_charset_symbol;
	protected $_pw_charset_lower_alpha;
	protected $_pw_charset_upper_alpha;
	protected $_pw_charset_number;


	// --------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Set defaults
		$this->_pw_charset_symbol		= utf8_encode( '!@$^&*(){}":?<>~-=[];\'\\/.,' );
		$this->_pw_charset_lower_alpha	= utf8_encode( 'abcdefghijklmnopqrstuvwxyz' );
		$this->_pw_charset_upper_alpha	= utf8_encode( 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' );
		$this->_pw_charset_number		= utf8_encode( '0123456789' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Inject the user object, private by convention - only really used by a few
	 * core Nails classes
	 *
	 * @access	public
	 * @param object $user The user object
	 * @return void
	 **/
	public function _set_user_object( &$user )
	{
		$this->_user = $user;
	}


	// --------------------------------------------------------------------------


	/**
	 * Changes a password for a particular user
	 * @param  int $user_id  The user ID whose password to change
	 * @param  steing $password The raw, unencrypted new password
	 * @return boolean
	 */
	public function change( $user_id, $password )
	{
		//	TODO
	}


	// --------------------------------------------------------------------------


	/**
	 * Determines whether a password is correct for a particular user.
	 * @param  int  $user_id  The suer ID to check for
	 * @param  string  $password The raw, unencrypted password to check
	 * @return boolean
	 */
	public function is_correct( $user_id, $password )
	{
		if ( empty( $user_id ) || empty( $password ) ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$this->db->select( 'u.password, u.password_engine, u.salt' );
		$this->db->where( 'u.id', $user_id );
		$this->db->limit( 1 );
		$_q = $this->db->get( NAILS_DB_PREFIX . 'user u' );

		// --------------------------------------------------------------------------

		if ( $_q->num_rows() !== 1 ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	TODO: use the appropriate driver to determine password correctness
		//	But for now, do it the old way

		$_hash = sha1( sha1( $password ) . $_q->row()->salt );

		return $_q->row()->password === $_hash;
	}


	// --------------------------------------------------------------------------


	/**
	 * Create a password hash, checks to ensure a password is strong enough according
	 * to the password rules defined by the app.
	 * @param  string $password The raw, unencrypted password
	 * @return mixed           Array on success, FALSE on failure
	 */
	public function generate_hash( $password )
	{
		if ( empty( $password ) ) :

			$this->_set_error( 'No password to hash' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Check password satisfies password rules
		$_password_rules = $this->_get_password_rules();

		//	Lgng enough?
		if ( strlen( $password ) < $_password_rules['min_length'] ) :

			$this->_set_error( 'Password is too short.' );
			return FALSE;

		endif;

		//	Too long?
		if ( $_password_rules['max_length'] ) :

			if ( strlen( $password ) > $_password_rules['max_length'] ) :

				$this->_set_error( 'Password is too long.' );
				return FALSE;

			endif;

		endif;

		//	Contains at least 1 character from each of the charsets
		foreach ( $_password_rules['charsets'] AS $slug => $charset ) :

			$_chars		= str_split( $charset );
			$_is_valid	= FALSE;

			foreach ( $_chars AS $char ) :

				if ( strstr( $password, $char ) ) :

					$_is_valid = TRUE;
					break;

				endif;

			endforeach;

			if ( ! $_is_valid ) :

				switch( $slug ) :

					case 'symbol' :			$_item = 'a symbol';				break;
					case 'lower_alpha' :	$_item = 'a lower case letter';		break;
					case 'upper_alpha' :	$_item = 'an upper case letter';	break;
					case 'number' :			$_item = 'a number';				break;

				endswitch;

				$this->_set_error( 'Password must contain ' . $_item . '.' );
				return FALSE;

			endif;

		endforeach;

		//	Not be a bad password?
		foreach ( $_password_rules['is_not'] AS $str ) :

			if ( strtolower( $password ) == strtolower( $str ) ) :

				$this->_set_error( 'Password cannot be "' . $str . '"' );
				return FALSE;

			endif;

		endforeach;

		// --------------------------------------------------------------------------

		//	Password is valid, generate a salt
		$_salt = $this->salt();

		// --------------------------------------------------------------------------

		$_out				= new stdClass();
		$_out->password		= sha1( sha1( $password ) . $_salt );
		$_out->password_md5	= md5( $_out->password );
		$_out->salt			= $_salt;
		$_out->engine		= 'NAILS_1';

		return $_out;
	}


	// --------------------------------------------------------------------------


	/**
	 * Generates a password which is sufficiently secure according to the app's
	 * password rules
	 * @return string
	 */
	public function generate()
	{
		$_password_rules	= $this->_get_password_rules();
		$_pw_out			= array();

		// --------------------------------------------------------------------------

		//	We're generating a password, so ensure that we've got all the charsets;
		//	also make sure we include any additional charsets which have been defined.

		$_charsets					= array();
		$_charsets['symbol']		= $this->_pw_charset_symbol;
		$_charsets['lower_alpha']	= $this->_pw_charset_lower_alpha;
		$_charsets['upper_alpha']	= $this->_pw_charset_upper_alpha;
		$_charsets['number']		= $this->_pw_charset_number;

		foreach ( $_charsets AS $set => $chars ) :

			if ( ! isset( $_password_rules['charsets'][$set] ) ) :

				$_password_rules['charsets'][$set] = $chars;

			endif;

		endforeach;

		// --------------------------------------------------------------------------

		//	Work out the max length, if it's not been set
		if ( ! $_password_rules['min_length'] && ! $_password_rules['max_length'] ) :

			$_password_rules['max_length'] = count( $_password_rules['charsets'] ) * 2;

		elseif( $_password_rules['min_length'] && ! $_password_rules['max_length'] ) :

			$_password_rules['max_length'] = $_password_rules['min_length'] + count( $_password_rules['charsets'] );

		elseif ( $_password_rules['min_length'] > $_password_rules['max_length'] ) :

			$_password_rules['max_length'] = $_password_rules['min_length'] + count( $_password_rules['charsets'] );

		endif;

		// --------------------------------------------------------------------------

		//	We now have a max_length and all our chars, generate password!
		$_password_valid = TRUE;
		do
		{
			do
			{
				foreach ( $_password_rules['charsets'] AS $charset ) :

					$_character	= rand( 0, strlen( $charset ) - 1 );
					$_pw_out[]	= $charset[$_character];

				endforeach;

			} while( count( $_pw_out ) < $_password_rules['max_length'] );

			//	Check password isn't a prohibited string
			foreach ( $_password_rules['is_not'] AS $str ) :

				if ( strtolower( implode( '', $_pw_out ) ) == strtolower( $str ) ) :

					$_password_valid = FALSE;
					break;

				endif;

			endforeach;

		} while( ! $_password_valid );

		// --------------------------------------------------------------------------

		//	Shuffle the string
		shuffle( $_pw_out );

		// --------------------------------------------------------------------------

		return implode( '', $_pw_out );
	}


	// --------------------------------------------------------------------------


	/**
	 * Get's the app's password rules
	 * @return array
	 */
	protected function _get_password_rules()
	{
		$this->config->load( 'auth' );

		$_pw_str		= '';
		$_pw_rules		= $this->config->item( 'auth_password_rules' );
		$_pw_rules		= ! is_array( $_pw_rules ) ? array() : $_pw_rules;
		$_min_length	= 0;
		$_max_length	= FALSE;
		$_contains		= array();
		$_is_not		= array();

		foreach ( $_pw_rules AS $rule => $val ) :

			switch( $rule ) :

				case 'min_length' :

					$_min_length = (int) $val;

				break;

				case 'max_length' :

					$_max_length = (int) $val;

				break;

				case 'contains' :

					foreach( $val AS $str ) :

						$_contains[] = (string) $str;

					endforeach;

				break;

				case 'is_not' :

					foreach( $val AS $str ) :

						$_is_not[] = (string) $str;

					endforeach;

				break;

			endswitch;

		endforeach;

		// --------------------------------------------------------------------------

		$_contains = array_filter( $_contains );
		$_contains = array_unique( $_contains );

		$_is_not = array_filter( $_is_not );
		$_is_not = array_unique( $_is_not );

		// --------------------------------------------------------------------------

		//	Generate the lsit of characters to use
		$_chars = array();
		foreach ( $_contains AS $charset ) :

			switch( $charset ) :

				case 'symbol' :			$_chars[$charset]	= $this->_pw_charset_symbol;		break;
				case 'lower_alpha' :	$_chars[$charset]	= $this->_pw_charset_lower_alpha;	break;
				case 'upper_alpha' :	$_chars[$charset]	= $this->_pw_charset_upper_alpha;	break;
				case 'number' :			$_chars[$charset]	= $this->_pw_charset_number;		break;

				//	Not a 'special' charset? Whatever this string is just set that as the chars to use
				default :				$_chars[]			= utf8_encode( $charset );			break;

			endswitch;

		endforeach;

		// --------------------------------------------------------------------------

		//	Make sure min_length is >= count( $_chars ), so we can satisfy the
		//	requirements of the chars

		$_min_length = $_min_length < count( $_chars ) ? count( $_chars ) : $_min_length;

		$_out = array();
		$_out['min_length']	= $_min_length;
		$_out['max_length']	= $_max_length;
		$_out['charsets']	= $_chars;
		$_out['is_not']		= $_is_not;

		return $_out;
	}


	// --------------------------------------------------------------------------


	/**
	 * Loads the particulat password driver/model
	 * @param  string $driver The name of the driver
	 * @return bool
	 */
	protected function _load_password_driver( $driver )
	{
		if (! empty( $this->_loaded_drivers[$driver] ) ) :

			return TRUE;

		endif;

		$this->load->model( 'user_password_' . $driver . '_model' );

		$this->_loaded_drivers[$driver] = TRUE;

		return TRUE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Generates a random salt
	 * @param  string $pepper Additional data to inject into the salt
	 * @return string
	 */
	public function salt( $pepper = '' )
	{
		return md5( uniqid( $pepper . rand() . DEPLOY_PRIVATE_KEY . APP_PRIVATE_KEY, TRUE ) );
	}


	// --------------------------------------------------------------------------


	/**
	 * Set's a forgotten password token for a user
	 * @param string $identifier The identifier to use for setting the token (set by APP_NATIVE_LOGIN_USING)
	 * @return boolean
	 */
	public function set_token( $identifier )
	{
		if ( empty( $identifier ) ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Generate code
		$_key = sha1( sha1( $this->salt() ) . $this->salt() . APP_PRIVATE_KEY );
		$_ttl = time() + 86400; // 24 hours.

		// --------------------------------------------------------------------------

		//	Update the user
		switch( APP_NATIVE_LOGIN_USING ) :

			case 'EMAIL' :

				$_user = $this->_user->get_by_email( $identifier );

			break;

			// --------------------------------------------------------------------------

			case 'USERNAME' :

				$_user = $this->_user->get_by_username( $identifier );

			break;

			// --------------------------------------------------------------------------

			case 'BOTH' :
			default:

				$this->load->helper( 'email' );

				if ( valid_email( $identifier ) ) :

					$_user = $this->_user->get_by_email( $identifier );

				else :

					$_user = $this->_user->get_by_username( $identifier );

				endif;

			break;

		endswitch;

		if ( $_user ) :

			$_data = array(

				'forgotten_password_code' => $_ttl . ':' . $_key
			);

			return $this->_user->update( $_user->id, $_data );

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Validate a forgotten password code.
	 * @param  string $code The token to validate
	 * @param  string $generate_new_pw Whetehr or not to generate a new password (only if token is valid)
	 * @return boolean
	 */
	public function validate_token( $code, $generate_new_pw )
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
			$_out				= array();
			$_out['user_id']	= $_user->id;

			//	Generate a new password?
			if ( $generate_new_pw ) :

				$_out['password']	= $this->generate();

				if ( empty( $_out['password'] ) ) :

					//	This should never happen, but just in case.
					return FALSE;

				endif;

				$_hash = $this->generate_hash( $_out['password'] );

				if ( ! $_hash ) :

					//	Again, this should never happen, but just in case.
					return FALSE;

				endif;

				// --------------------------------------------------------------------------

				$_data['password']					= $_hash->password;
				$_data['password_md5']				= $_hash->password_md5;
				$_data['password_engine']			= $_hash->engine;
				$_data['salt']						= $_hash->salt;
				$_data['temp_pw']					= TRUE;
				$_data['forgotten_password_code']	= NULL;

				$this->db->where( 'forgotten_password_code', $_user->forgotten_password_code );
				$this->db->set( $_data );
				$this->db->update( NAILS_DB_PREFIX . 'user' );

			endif;

		endif;

		return $_out;
	}


	// --------------------------------------------------------------------------


/**
	 * --------------------------------------------------------------------------
	 *
	 * ERROR METHODS
	 * These methods provide a consistent interface for setting and retrieving
	 * errors which are generated.
	 *
	 * --------------------------------------------------------------------------
	 **/


	/**
	 * Set a generic error
	 *
	 * @access	protected
	 * @param	string	$error	The error message
	 * @return void
	 **/
	protected function _set_error( $error )
	{
		$this->_errors[] = $error;
	}


	// --------------------------------------------------------------------------


	/**
	 * Get any errors
	 *
	 * @access	public
	 * @return array
	 **/
	public function get_errors()
	{
		return $this->_errors;
	}


	// --------------------------------------------------------------------------


	/**
	 * Get last error
	 *
	 * @access	public
	 * @return mixed
	 **/
	public function last_error()
	{
		return end( $this->_errors );
	}


	// --------------------------------------------------------------------------


	/**
	 * Clear the last error
	 *
	 * @access	public
	 * @return mixed
	 **/
	public function clear_last_error()
	{
		return array_pop( $this->_errors );
	}


	// --------------------------------------------------------------------------


	/**
	 * Clears all errors
	 *
	 * @access	public
	 * @return mixed
	 **/
	public function clear_errors()
	{
		$this->_errors = array();
		return array();
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_USER_PASSWORD_MODEL' ) ) :

	class User_password_model extends NAILS_User_password_model
	{
	}

endif;

/* End of file user_password_model.php */
/* Location: ./system/application/models/user_password_model.php */