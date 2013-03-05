<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Form_validation extends CI_Form_validation {
	
	
	/*
	 *
	 * Quick mod of run() to allow for HMVC
	 *
	 */
	
	public function run($module = '', $group = '')
	{
		( is_object( $module ) ) AND $this->CI = &$module;
			return parent::run($group);
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Checks if a certain value is unique in a specified table
	 * if different from current value.
	 *
	 * @param	string
	 * @param	string - column.value
	 * @return	bool
	 */
	public function unique_if_diff( $new, $params )
	{
		$CI =& get_instance();
		
		list($table, $column, $old) = explode(".", $params, 3);
		
		if ($new == $CI->input->post( $old ))
			return TRUE;
		
		if ( ! array_key_exists( 'unique_if_diff', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'unique_if_diff', '%s is not unique.' );
				
		$CI->db->where( $column . ' !=', $CI->input->post( $old ) );
		$CI->db->where( $column, $new );
		$CI->db->limit( 1 );
		$q = $CI->db->get( $table );
		
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