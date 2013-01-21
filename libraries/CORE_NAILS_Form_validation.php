<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Form_validation extends CI_Form_validation {
	
	
	/*
	 *
	 * Quick mod of run() to allow for HMVC
	 *
	 */
	    
	public function run($module = '', $group = ''){
		(is_object($module)) AND $this->CI = &$module;
			return parent::run($group);
	}



	
	/*
	 *
	 * Adjusting rules to allow for accents plus adding a couple of
	 * new rules in
	 *
	/*
	
	
	
	
	
	
	
	/**
	 * Alpha Rules
	 *
	 * --------------------------------------------------------------------
	 */	
	
	/**
	 * Allow only letters, accents and spaces
	 *
	 * @param	string
	 * @return	bool
	 */	
	public function alpha_space_accent($str)
	{
		$CI =& get_instance();
		if ( ! array_key_exists( 'alpha_space_accent', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'alpha_space_accent', '%s contains invalid characters (letters [including accents] and spaces only).' );
		
		$str = utf8_decode($str);
		return ( ! preg_match("/^([\p{L}\ ])+$/i", $str)) ? FALSE : TRUE;
	}
	
	/**
	 * Allow only letters and spaces
	 *
	 * @param	string
	 * @return	bool
	 */	
	public function alpha_space($str)
	{
		$CI =& get_instance();
		if ( ! array_key_exists( 'alpha_space', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'alpha_space', '%s contains invalid characters (letters and spaces only).' );
		
		$str = utf8_decode($str);
		return ( ! preg_match("/^[abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ\ ]+$/i", $str)) ? FALSE : TRUE;
	}
	
	/**
	 * Allow only letters
	 *
	 * @param	string
	 * @return	bool
	 */	
	public function alpha($str)
	{
		$CI =& get_instance();
		if ( ! array_key_exists( 'alpha', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'alpha', '%s contains invalid characters (letters only).' );
		
		$str = utf8_decode($str);
		return ( ! preg_match("/^[abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ]+$/i", $str)) ? FALSE : TRUE;
	}
	
	
	
	
	
	
	
	
	
	/**
	 * Alpha-Numeric Rules
	 *
	 * --------------------------------------------------------------------
	 */


	/**
	 * Allow letters, accents, numbers and spaces
	 *
	 * @param	string
	 * @return	bool
	 */	
	public function alpha_numeric_space_accent($str)
	{
		$CI =& get_instance();
		if ( ! array_key_exists( 'alpha_numeric_space_accent', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'alpha_numeric_space_accent', '%s contains invalid characters (letters [including accents], numbers and spaces only).' );
		
		$str = utf8_decode($str);
		return ( ! preg_match("/^([\p{L}\p{N}\ ])+$/i", $str)) ? FALSE : TRUE;
	}
	
	/**
	 * Allow letters, numbers and spaces
	 *
	 * @param	string
	 * @return	bool
	 */	
	public function alpha_numeric_space($str)
	{
		$CI =& get_instance();
		if ( ! array_key_exists( 'alpha_numeric_space', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'alpha_numeric_space', '%s contains invalid characters (letters, numbers and spaces only).' );
		
		$str = utf8_decode($str);
		return ( ! preg_match("/^[abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0-9\ ]+$/i", $str)) ? FALSE : TRUE;
	}
	
	/**
	 * Allow letters and numbers
	 *
	 * @param	string
	 * @return	bool
	 */	
	public function alpha_numeric($str)
	{
		$CI =& get_instance();
		if ( ! array_key_exists( 'alpha_numeric', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'alpha_numeric', '%s contains invalid characters (letters and numbers only).' );
		
		$str = utf8_decode($str);
		return ( ! preg_match("/^[abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0-9]+$/i", $str)) ? FALSE : TRUE;
	}
	
	
	
	
	
	
	
	
	
	/**
	 * Alpha-Dash Rules
	 *
	 * --------------------------------------------------------------------
	 */
	
	/**
	 * Allow letters, numbers, accents, spaces, dashes and underscores
	 *
	 * @param	string
	 * @return	bool
	 */	
	public function alpha_dash_space_accent($str)
	{
		$CI =& get_instance();
		if ( ! array_key_exists( 'alpha_dash_space_accent', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'alpha_dash_space_accent', '%s contains invalid characters (letters [including accents], numbers, spaces, dashes and underscores only).' );
		
		$str = utf8_decode($str);
		return ( ! preg_match("/^[\p{L}\p{N}-_\ ]+$/i", $str)) ? FALSE : TRUE;
	}
	
	/**
	 * Allow letters, numbers, spaces, dashes and underscores
	 *
	 * @param	string
	 * @return	bool
	 */	
	public function alpha_dash_space($str)
	{
		$CI =& get_instance();
		if ( ! array_key_exists( 'alpha_dash_space', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'alpha_dash_space', '%s contains invalid characters (letters, numbers, spaces, dashes and underscores only).' );
		
		$str = utf8_decode($str);
		return ( ! preg_match("/^[abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0-9\ -_]+$/i", $str)) ? FALSE : TRUE;
	}
	
	/**
	 * Allow letters, numbers, dashes and underscores
	 *
	 * @param	string
	 * @return	bool
	 */	
	public function alpha_dash($str)
	{
		$CI =& get_instance();
		if ( ! array_key_exists( 'alpha_dash', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'alpha_dash', '%s contains invalid characters (letters, numbers, dashes and underscores only).' );
		
		$str = utf8_decode($str);
		return ( ! preg_match("/^[abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0-9-_]+$/i", $str)) ? FALSE : TRUE;
	}
	
	
	
	
	
	
	
	
	
	/**
	 * Aliases
	 *
	 * --------------------------------------------------------------------
	 */
	 
	/**
	 * Good for username fields - letters, numbers, dashes and underscores
	 *
	 * @param	string
	 * @return	bool
	 */	
	public function usernamesafe($str)
	{
		$CI =& get_instance();
		if ( ! array_key_exists( 'username_safe', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'usernamesafe', '%s contains invalid characters (letters, numbers, dashes and underscores only).' );
		
		return $this->alpha_dash($str);
	}
	
	/**
	 * Good for URLs - letters, numbers, dashes and underscores
	 *
	 * @param	string
	 * @return	bool
	 */	
	public function urlsafe($str)
	{
		$CI =& get_instance();
		if ( ! array_key_exists( 'urlsafe', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'urlsafe', '%s contains invalid characters (letters, numbers, dashes and underscores only).' );
		
		return $this->alpha_dash($str);
	}
	
	
	/**
	 * Checking captcha
	 *
	 * @param	string
	 * @return	bool
	 */	
	public function check_captcha($str)
	{
		$CI =& get_instance();
		if ( $str != $CI->session->userdata( 'captcha' ) ) :
			return FALSE;
		else:
			return TRUE;
		endif;

	}
	
	
	
	
	
	
	
	
	
	/**
	 * Newbies
	 * A bunch of useful functions to throw in the mix
	 *
	 * --------------------------------------------------------------------
	 */
	
	/**
	 * Checks if a certain value is unique in a specified table
	 * if different from current value.
	 *
	 * @param	string
	 * @param	string - column.value
	 * @return	bool
	 */	
	public function unique_if_diff($new, $params)
	{
		
		list($table, $column, $old) = explode(".", $params, 3);
		
		if ($new == $old)
			return TRUE;
			
		$CI =& get_instance();
		$CI->load->database();
		
		if ( ! array_key_exists( 'unique_if_diff', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'unique_if_diff', '%s is not unique.' );
				
		$CI->db->where($column.' != \''.$old.'\'');
		$CI->db->where($column, $new);
		$CI->db->limit(1);
		$q = $CI->db->get($table);
		if ($q->row())
			return FALSE;
		
		return TRUE;
	}
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Checks if a string is in valid UK postcode format
	 *
	 * @param	string
	 * @return	bool
	 */	
	public function valid_postcode( $str )
	{
		$CI =& get_instance();
		
		if ( ! array_key_exists( 'valid_postcode', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'valid_postcode', '%s is not a valid UK postcode.' );
		
		$pattern = '/^([Gg][Ii][Rr] 0[Aa]{2})|((([A-Za-z][0-9]{1,2})|(([A-Za-z][A-Ha-hJ-Yj-y][0-9]{1,2})|(([A-Za-z][0-9][A-Za-z])|([A-Za-z][A-Ha-hJ-Yj-y][0-9]?[A-Za-z])))) {0,1}[0-9][A-Za-z]{2})$/';
		return preg_match( $pattern, strtoupper( $str ) ) ? TRUE : FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Checks if a series of date dropdowns is valid
	 *
	 * @param	string
	 * @return	bool
	 */	
	public function valid_date( $day, $field )
	{
		$CI =& get_instance();
		
		if ( ! array_key_exists( 'valid_date', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'valid_date', '%s is not a valid date.' );
		
		$month	= $CI->input->post( $field . '_month' );
		$year	= $CI->input->post( $field . '_year' );
		
		//	If all fields are blank then assume the field is not required
		if ( $year . $month . $day == '00000000' )
			return TRUE;
		
		return checkdate( $month, $day, $year );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Checks if a date has been set
	 *
	 * @param	string
	 * @return	bool
	 */	
	public function date_required( $day, $field )
	{
		$CI =& get_instance();
		
		if ( ! array_key_exists( 'date_required', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'date_required', 'The %s field is required.' );
		
		$month	= $CI->input->post( $field . '_month' );
		$year	= $CI->input->post( $field . '_year' );
		
		//	If all fields are blank then the rule fails
		return $year . $month . $day == '00000000' ? FALSE : TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Checks if a date is in the future
	 *
	 * @param	string
	 * @return	bool
	 */	
	public function date_future( $day, $field )
	{
		$CI =& get_instance();
		
		if ( ! array_key_exists( 'date_future', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'date_future', 'The %s field must be in the future.' );
		
		$month	= $CI->input->post( $field . '_month' );
		$year	= $CI->input->post( $field . '_year' );
		
		return strtotime( $year . '-' . $month . '-' . $day ) < strtotime( date( 'Y-m-d' ) ) ? FALSE : TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Checks if a date is in the future
	 *
	 * @param	string
	 * @return	bool
	 */	
	public function date_past( $day, $field )
	{
		$CI =& get_instance();
		
		if ( ! array_key_exists( 'date_past', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'date_past', 'The %s field must be in the past.' );
		
		$month	= $CI->input->post( $field . '_month' );
		$year	= $CI->input->post( $field . '_year' );
		
		//	If all fields are blank then the rule fails
		return strtotime( $year . '-' . $month . '-' . $day ) > strtotime( date( 'Y-m-d' ) ) ? FALSE : TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Checks if a series of date dropdowns is valid
	 *
	 * @param	string
	 * @return	bool
	 */	
	public function valid_datetime( $day, $field )
	{
		$CI =& get_instance();
		
		if ( ! array_key_exists( 'valid_datetime', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'valid_datetime', '%s is not a valid datetime.' );
		
		$month	= $CI->input->post( $field . '_month' );
		$year	= $CI->input->post( $field . '_year' );
		$hour	= $CI->input->post( $field . '_hour' );
		$minute	= $CI->input->post( $field . '_minute' );
		
		$_compiled = $year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minute . ':00';
		
		//	If all fields are blank then assume the field is not required
		if ( $_compiled == '0000-00-00 00:00:00' )
			return TRUE;
		
		return date( 'Y-m-d H:i:s', strtotime( $_compiled ) ) == $_compiled;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Checks if a datetime has been set
	 *
	 * @param	string
	 * @return	bool
	 */	
	public function datetime_required( $day, $field )
	{
		$CI =& get_instance();
		
		if ( ! array_key_exists( 'datetime_required', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'datetime_required', 'The %s field is required.' );
		
		$month	= $CI->input->post( $field . '_month' );
		$year	= $CI->input->post( $field . '_year' );
		$hour	= $CI->input->post( $field . '_hour' );
		$minute	= $CI->input->post( $field . '_minute' );
		
		//	If all fields are blank then the rule fails
		return $year . $month . $day . $hour . $minute == '000000000000' ? FALSE : TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Checks if a datetime is in the future
	 *
	 * @param	string
	 * @return	bool
	 */	
	public function datetime_future( $day, $field )
	{
		$CI =& get_instance();
		
		if ( ! array_key_exists( 'datetime_required', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'datetime_required', 'The %s field is required.' );
		
		$month	= $CI->input->post( $field . '_month' );
		$year	= $CI->input->post( $field . '_year' );
		$hour	= $CI->input->post( $field . '_hour' );
		$minute	= $CI->input->post( $field . '_minute' );
		
		return strtotime( $year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $min ) < strtotime( date( 'Y-m-d H:m' ) ) ? FALSE : TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Checks if a datetime is in the future
	 *
	 * @param	string
	 * @return	bool
	 */	
	public function datetime_past( $day, $field )
	{
		$CI =& get_instance();
		
		if ( ! array_key_exists( 'datetime_required', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'datetime_required', 'The %s field is required.' );
		
		$month	= $CI->input->post( $field . '_month' );
		$year	= $CI->input->post( $field . '_year' );
		$hour	= $CI->input->post( $field . '_hour' );
		$minute	= $CI->input->post( $field . '_minute' );
		
		return strtotime( $year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $min ) > strtotime( date( 'Y-m-d H:m' ) ) ? FALSE : TRUE;
	}
	
}


/* End of file NAILS_Form_validation.php */
/* Location: ./system/application/core/NAILS_Form_validation.php */