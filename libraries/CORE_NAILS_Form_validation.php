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
	
	
	public function get_error_array()
	{
		return $this->_error_array;
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
		
		if ( ! array_key_exists( 'unique_if_diff', $CI->form_validation->_error_messages ) ) :
		
			$CI->form_validation->set_message( 'unique_if_diff', lang( 'fv_unique_if_diff_field' ) );
			
		endif;
				
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
		
		if ( ! array_key_exists( 'valid_postcode', $CI->form_validation->_error_messages ) ) :
		
			$CI->form_validation->set_message( 'valid_postcode', lang( 'fv_valid_postcode' ) );
			
		endif;
		
		$pattern = '/^([Gg][Ii][Rr] 0[Aa]{2})|((([A-Za-z][0-9]{1,2})|(([A-Za-z][A-Ha-hJ-Yj-y][0-9]{1,2})|(([A-Za-z][0-9][A-Za-z])|([A-Za-z][A-Ha-hJ-Yj-y][0-9]?[A-Za-z])))) {0,1}[0-9][A-Za-z]{2})$/';
		return preg_match( $pattern, strtoupper( $str ) ) ? TRUE : FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Checks if a date is valid
	 *
	 * @param	string
	 * @return	bool
	 */
	public function valid_date( $date )
	{
		//	If blank, then assume the date is not required
		if ( ! $date  )
			return TRUE;

		// --------------------------------------------------------------------------

		$CI =& get_instance();
		
		if ( ! array_key_exists( 'valid_date', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'valid_date', lang( 'fv_valid_date_field' ) );
		
		$_date	= explode( '-', $date );
		$_year	= isset( $_date[0] ) ? $_date[0] : NULL;
		$_month	= isset( $_date[1] ) ? $_date[1] : NULL;
		$_day	= isset( $_date[2] ) ? $_date[2] : NULL;
		
		return checkdate( $_month, $_day, $_year );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Checks if a date is in the future
	 *
	 * @param	string
	 * @return	bool
	 */
	public function date_future( $date )
	{
		//	If blank, then assume the date is not required
		if ( ! $date  )
			return TRUE;

		// --------------------------------------------------------------------------

		$CI =& get_instance();
		
		if ( ! array_key_exists( 'date_future', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'date_future', lang( 'fv_valid_date_future_field' ) );
		
		$_date	= explode( '-', $date );
		$_year	= isset( $_date[0] ) ? $_date[0] : NULL;
		$_month	= isset( $_date[1] ) ? $_date[1] : NULL;
		$_day	= isset( $_date[2] ) ? $_date[2] : NULL;
		
		return strtotime( $_year . '-' . $_month . '-' . $_day ) < strtotime( date( 'Y-m-d' ) ) ? FALSE : TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Checks if a date is in the future
	 *
	 * @param	string
	 * @return	bool
	 */
	public function date_past( $date )
	{
		//	If blank, then assume the date is not required
		if ( ! $date  )
			return TRUE;

		// --------------------------------------------------------------------------

		$CI =& get_instance();
		
		if ( ! array_key_exists( 'date_past', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'date_past', lang( 'fv_valid_date_past_field' ) );
		
		$_date	= explode( '-', $date );
		$_year	= isset( $_date[0] ) ? $_date[0] : NULL;
		$_month	= isset( $_date[1] ) ? $_date[1] : NULL;
		$_day	= isset( $_date[2] ) ? $_date[2] : NULL;
		
		return strtotime( $_year . '-' . $_month . '-' . $_day ) > strtotime( date( 'Y-m-d' ) ) ? FALSE : TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Checks if a series of date dropdowns is valid
	 *
	 * @param	string
	 * @return	bool
	 */
	public function valid_datetime( $datetime )
	{
		//	If blank, then assume the datetime is not required
		if ( ! $datetime  )
			return TRUE;

		// --------------------------------------------------------------------------

		$CI =& get_instance();
		
		if ( ! array_key_exists( 'valid_datetime', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'valid_datetime', lang( 'fv_valid_datetime_field' ) );
		
		$_datetime	= explode( ' ', $datetime );
		
		if ( ! isset( $_datetime[0] ) || ! isset( $_datetime[1] ) ) 
			return FALSE;

		$_date	= explode( '-', $_datetime[0] );
		$_year	= isset( $_date[0] ) ? $_date[0] : NULL;
		$_month	= isset( $_date[1] ) ? $_date[1] : NULL;
		$_day	= isset( $_date[2] ) ? $_date[2] : NULL;

		$_valid_date = checkdate( $_month, $_day, $_year );
		$_valid_time = preg_match( '/^([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/', $_datetime[1] );
		
		return $_valid_date && $_valid_time ? TRUE : FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Checks if a datetime is in the future
	 *
	 * @param	string
	 * @return	bool
	 */
	public function datetime_future( $datetime )
	{
		//	If blank, then assume the datetime is not required
		if ( ! $datetime  )
			return TRUE;

		// --------------------------------------------------------------------------

		$CI =& get_instance();
		
		if ( ! array_key_exists( 'datetime_future', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'datetime_future', lang( 'fv_valid_datetime_future_field' ) );
		
		$_datetime	= explode( ' ', $datetime );
		
		if ( ! isset( $_datetime[0] ) || ! isset( $_datetime[1] ) ) 
			return FALSE;

		$_date	= explode( '-', $_datetime[0] );
		$_year	= isset( $_date[0] ) ? $_date[0] : NULL;
		$_month	= isset( $_date[1] ) ? $_date[1] : NULL;
		$_day	= isset( $_date[2] ) ? $_date[2] : NULL;

		$_valid_date = checkdate( $_month, $_day, $_year );
		$_valid_time = preg_match( '/^([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/', $_datetime[1] );
		
		if ( $_valid_date && $_valid_time ) :

			if ( strtotime( $year . '-' . $month . '-' . $day . ' ' . $_datetime[1] ) > time() ) :

				return TRUE;

			else :

				return FALSE;

			endif;

		else :

			return FALSE;

		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Checks if a datetime is in the future
	 *
	 * @param	string
	 * @return	bool
	 */
	public function datetime_past( $datetime )
	{
		//	If blank, then assume the datetime is not required
		if ( ! $datetime  )
			return TRUE;

		// --------------------------------------------------------------------------

		$CI =& get_instance();
		
		if ( ! array_key_exists( 'datetime_past', $CI->form_validation->_error_messages ) )
			$CI->form_validation->set_message( 'datetime_past', lang( 'fv_valid_datetime_past_field' ) );
		
		$_datetime	= explode( ' ', $datetime );
		
		if ( ! isset( $_datetime[0] ) || ! isset( $_datetime[1] ) ) 
			return FALSE;

		$_date	= explode( '-', $_datetime[0] );
		$_year	= isset( $_date[0] ) ? $_date[0] : NULL;
		$_month	= isset( $_date[1] ) ? $_date[1] : NULL;
		$_day	= isset( $_date[2] ) ? $_date[2] : NULL;

		$_valid_date = checkdate( $_month, $_day, $_year );
		$_valid_time = preg_match( '/^([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/', $_datetime[1] );
		
		if ( $_valid_date && $_valid_time ) :

			if ( strtotime( $year . '-' . $month . '-' . $day . ' ' . $_datetime[1] ) < time() ) :

				return TRUE;

			else :

				return FALSE;

			endif;

		else :

			return FALSE;

		endif;
	}
	
}


/* End of file NAILS_Form_validation.php */
/* Location: ./core/NAILS_Form_validation.php */