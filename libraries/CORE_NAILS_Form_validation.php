<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Form_validation extends CI_Form_validation
{
	/**
	 * Quick mod of run() to allow for HMVC.
	 */
	public function run($module = '', $group = '')
	{
		( is_object( $module ) ) AND $this->CI = &$module;
			return parent::run($group);
	}


	// --------------------------------------------------------------------------


	/**
	 * Returns the form validation error array.
	 * @return array
	 */
	public function get_error_array()
	{
		return $this->_error_array;
	}


	// --------------------------------------------------------------------------


	/**
	 * Checks if a certain value is unique in a specified table if different
	 * from current value.
	 * @param  string $new    The form value
	 * @param  string $params Parameters passed from set_rules() method
	 * @return boolean
	 */
	public function unique_if_diff( $new, $params )
	{
		$CI =& get_instance();

		list($table, $column, $old) = explode(".", $params, 3);

		if ( $new == $old ) :

			return TRUE;

		endif;

		if ( ! array_key_exists( 'unique_if_diff', $CI->form_validation->_error_messages ) ) :

			$CI->form_validation->set_message( 'unique_if_diff', lang( 'fv_unique_if_diff_field' ) );

		endif;

		$CI->db->where( $column . ' !=', $old );
		$CI->db->where( $column, $new );
		$CI->db->limit( 1 );
		$q = $CI->db->get( $table );

		if ($q->row())
			return FALSE;

		return TRUE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Checks if a string is in a valid UK post code format.
	 * @param  string $str The form value
	 * @return boolean
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
	 * Checks if a date is valid.
	 * @param  string $date The form value
	 * @return boolean
	 */
	public function valid_date( $date )
	{
		//	If blank, then assume the date is not required
		if ( ! $date  ) :

			return TRUE;

		endif;

		// --------------------------------------------------------------------------

		$CI =& get_instance();

		if ( ! array_key_exists( 'valid_date', $CI->form_validation->_error_messages ) ) :

			$CI->form_validation->set_message( 'valid_date', lang( 'fv_valid_date_field' ) );

		endif;

		$_time = strtotime( $date );

		if ( $_time === FALSE ) :

			return FALSE;

		endif;

		$_date = date( 'Y-m-d', $_time );

		@list( $_year, $_month, $_day ) = explode( '-', $_date );

		return checkdate( (int) $_month, (int) $_day, (int) $_year );
	}


	// --------------------------------------------------------------------------


	/**
	 * Checks if a date us in the future.
	 * @param  string $date The form value
	 * @return boolean
	 */
	public function date_future( $date )
	{
		//	If blank, then assume the date is not required
		if ( ! $date  ) :

			return TRUE;

		endif;

		// --------------------------------------------------------------------------

		$CI =& get_instance();

		if ( ! array_key_exists( 'date_future', $CI->form_validation->_error_messages ) ) :

			$CI->form_validation->set_message( 'date_future', lang( 'fv_valid_date_future_field' ) );

		endif;

		$_time = strtotime( $date );

		if ( $_time === FALSE ) :

			return FALSE;

		endif;

		$_date = date( 'Y-m-d', $_time );

		@list( $_year, $_month, $_day ) = explode( '-', $_date );

		return strtotime( (int) $_year . '-' . (int) $_month . '-' . (int) $_day ) < strtotime( date( 'Y-m-d' ) ) ? FALSE : TRUE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Checks if a date is in the past.
	 * @param  string $date The form value
	 * @return boolean
	 */
	public function date_past( $date )
	{
		//	If blank, then assume the date is not required
		if ( ! $date  ) :

			return TRUE;

		endif;

		// --------------------------------------------------------------------------

		$CI =& get_instance();

		if ( ! array_key_exists( 'date_past', $CI->form_validation->_error_messages ) ) :

			$CI->form_validation->set_message( 'date_past', lang( 'fv_valid_date_past_field' ) );

		endif;

		$_time = strtotime( $date );

		if ( $_time === FALSE ) :

			return FALSE;

		endif;

		$_date = date( 'Y-m-d', $_time );

		@list( $_year, $_month, $_day ) = explode( '-', $_date );

		return strtotime( (int) $_year . '-' . (int) $_month . '-' . (int) $_day ) > strtotime( date( 'Y-m-d' ) ) ? FALSE : TRUE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Checks if a date is before another date field.
	 * @param  string $date  The form value
	 * @param  string $field The other POST field to check against
	 * @return boolean
	 */
	public function date_before( $date, $field )
	{
		//	If blank, then assume the datetime is not required
		if ( ! $date  ) :

			return TRUE;

		endif;

		// --------------------------------------------------------------------------

		$CI =& get_instance();

		if ( ! array_key_exists( 'date_before', $CI->form_validation->_error_messages ) ) :

			$CI->form_validation->set_message( 'date_before', lang( 'fv_valid_date_before_field' ) );

		endif;

		// --------------------------------------------------------------------------

		return strtotime( $date ) < strtotime( $CI->input->post( $field ) ) ? TRUE : FALSE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Checks if a date is after another date field.
	 * @param  string $date  The form value
	 * @param  string $field The other POST field to check against
	 * @return boolean
	 */
	public function date_after( $date, $field )
	{
		//	If blank, then assume the datetime is not required
		if ( ! $date  ) :

			return TRUE;

		endif;

		// --------------------------------------------------------------------------

		$CI =& get_instance();

		if ( ! array_key_exists( 'date_after', $CI->form_validation->_error_messages ) ) :

			$CI->form_validation->set_message( 'date_after', lang( 'fv_valid_date_after_field' ) );

		endif;

		// --------------------------------------------------------------------------

		return strtotime( $date ) > strtotime( $CI->input->post( $field ) );
	}


	// --------------------------------------------------------------------------


	/**
	 * Checks if a datetime is valid.
	 * @param  string $datetime The form value
	 * @return boolean
	 */
	public function valid_datetime( $datetime )
	{
		//	If blank, then assume the datetime is not required
		if ( ! $datetime  ) :

			return TRUE;

		endif;

		// --------------------------------------------------------------------------

		$CI =& get_instance();

		if ( ! array_key_exists( 'valid_datetime', $CI->form_validation->_error_messages ) ) :

			$CI->form_validation->set_message( 'valid_datetime', lang( 'fv_valid_datetime_field' ) );

		endif;

		$_datetime = explode( ' ', date( 'Y-m-d H:i:s', strtotime( $datetime ) ) );

		if ( ! isset( $_datetime[0] ) || ! isset( $_datetime[1] ) ) :

			return FALSE;

		endif;

		$_time = strtotime( $_datetime[0] );

		if ( $_time === FALSE ) :

			return FALSE;

		endif;

		$_date = date( 'Y-m-d', $_time );

		@list( $_year, $_month, $_day ) = explode( '-', $_date );

		$_valid_date = checkdate( (int) $_month, (int) $_day, (int) $_year );
		$_valid_time = preg_match( '/^([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/', $_datetime[1] );

		return $_valid_date && $_valid_time ? TRUE : FALSE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Checks if a datetime is in the future.
	 * @param  string $datetime The form value
	 * @return boolean
	 */
	public function datetime_future( $datetime )
	{
		//	If blank, then assume the datetime is not required
		if ( ! $datetime  ) :

			return TRUE;

		endif;

		// --------------------------------------------------------------------------

		$CI =& get_instance();

		if ( ! array_key_exists( 'datetime_future', $CI->form_validation->_error_messages ) ) :

			$CI->form_validation->set_message( 'datetime_future', lang( 'fv_valid_datetime_future_field' ) );

		endif;

		$_datetime = explode( ' ', date( 'Y-m-d H:i:s', strtotime( $datetime ) ) );

		if ( ! isset( $_datetime[0] ) || ! isset( $_datetime[1] ) ) :

			return FALSE;

		endif;

		$_time = strtotime( $_datetime[0] );

		if ( $_time === FALSE ) :

			return FALSE;

		endif;

		$_date = date( 'Y-m-d', $_time );

		@list( $_year, $_month, $_day ) = explode( '-', $_date );

		$_valid_date = checkdate( (int) $_month, (int) $_day, (int) $_year );
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
	 * Checks if a datetime is in the past.
	 * @param  string $datetime The form value
	 * @return boolean]
	 */
	public function datetime_past( $datetime )
	{
		//	If blank, then assume the datetime is not required
		if ( ! $datetime  ) :

			return TRUE;

		endif;

		// --------------------------------------------------------------------------

		$CI =& get_instance();

		if ( ! array_key_exists( 'datetime_past', $CI->form_validation->_error_messages ) ) :

			$CI->form_validation->set_message( 'datetime_past', lang( 'fv_valid_datetime_past_field' ) );

		endif;

		$_datetime = explode( ' ', date( 'Y-m-d H:i:s', strtotime( $datetime ) ) );

		if ( ! isset( $_datetime[0] ) || ! isset( $_datetime[1] ) ) :

			return FALSE;

		endif;

		$_time = strtotime( $_datetime[0] );

		if ( $_time === FALSE ) :

			return FALSE;

		endif;

		$_date = date( 'Y-m-d', $_time );

		@list( $_year, $_month, $_day ) = explode( '-', $_date );

		$_valid_date = checkdate( (int) $_month, (int) $_day, (int) $_year );
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


	// --------------------------------------------------------------------------


	/**
	 * Checks if a datetime is before another field.
	 * @param  string $datetime The form value
	 * @param  string $field    The other POST field to check against
	 * @return boolean
	 */
	public function datetime_before( $datetime, $field )
	{
		//	If blank, then assume the datetime is not required
		if ( ! $datetime  ) :

			return TRUE;

		endif;

		// --------------------------------------------------------------------------

		$CI =& get_instance();

		if ( ! array_key_exists( 'datetime_before', $CI->form_validation->_error_messages ) ) :

			$CI->form_validation->set_message( 'datetime_before', lang( 'fv_valid_datetime_before_field' ) );

		endif;

		// --------------------------------------------------------------------------

		return strtotime( $datetime ) < strtotime( $CI->input->post( $field ) ) ? TRUE : FALSE;
	}


	// --------------------------------------------------------------------------


	/**
	 * Checks if a datetime is after another field.
	 * @param  string $datetime The form value
	 * @param  string $field    The other POST field to check against
	 * @return boolean
	 */
	public function datetime_after( $datetime, $field )
	{
		//	If blank, then assume the datetime is not required
		if ( ! $datetime  ) :

			return TRUE;

		endif;

		// --------------------------------------------------------------------------

		$CI =& get_instance();

		if ( ! array_key_exists( 'datetime_after', $CI->form_validation->_error_messages ) ) :

			$CI->form_validation->set_message( 'datetime_after', lang( 'fv_valid_datetime_after_field' ) );

		endif;

		// --------------------------------------------------------------------------

		return strtotime( $datetime ) > strtotime( $CI->input->post( $field ) );
	}


	// --------------------------------------------------------------------------


	/**
	 * Checks if a value is within a range as defined in $field
	 * @param  string $str   The form value
	 * @param  string $field The range, e.g., 0-10
	 * @return boolean
	 */
	public function in_range( $str, $field )
	{
		$_range = explode( '-', $field );
		$_low	= isset( $_range[0] ) ? (float) $_range[0] : NULL;
		$_high	= isset( $_range[1] ) ? (float) $_range[1] : NULL;

		if ( NULL === $_low || NULL === $_high ) :

			return TRUE;

		endif;

		if ( (float) $str >= $_low && (float) $str <= $_high ) :

			return TRUE;

		else :

			$CI =& get_instance();

			if ( ! array_key_exists( 'in_range', $CI->form_validation->_error_messages ) ) :

				$CI->form_validation->set_message( 'in_range', lang( 'fv_in_range_field' ) );

			endif;

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Valid Email, using filter_var if possible falling back to CI's regex
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function valid_email($str)
	{
		if ( function_exists( 'filter_var' ) ) :

			return (bool) filter_var( $str, FILTER_VALIDATE_EMAIL );

		else :

			return parent::valid_email($str);

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Same as alpha_dash, but includes periods
	 * @param  string $str The string to test
	 * @return boolean
	 */
	public function alpha_dash_period($str)
	{
		return ( ! preg_match("/^([\.-a-z0-9_-])+$/i", $str)) ? FALSE : TRUE;
	}

}


/* End of file NAILS_Form_validation.php */
/* Location: ./core/NAILS_Form_validation.php */