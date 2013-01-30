<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * form_field
 *
 * Generates a form field (of type text, password or textarea)
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */
if ( ! function_exists( 'form_email' ) )
{
	function form_email($data = '', $value = '', $extra = '')
	{
		$defaults = array('type' => 'email', 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);

		return "<input "._parse_form_attributes($data, $defaults).$extra." />";
	}	
}


// --------------------------------------------------------------------------


/**
 * form_field
 *
 * Generates a form field (of type text, password or textarea)
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */
if ( ! function_exists( 'form_field' ) )
{
	function form_field( $field, $help = '' )
	{
		//	Set var defaults
		$_field					= array();
		$_field['type']			= isset( $field['type'] ) ? $field['type'] : 'text';
		$_field['oddeven']		= isset( $field['oddeven'] ) ? $field['oddeven'] : NULL;
		$_field['key']			= isset( $field['key'] ) ? $field['key'] : NULL;
		$_field['label']		= isset( $field['label'] ) ? $field['label'] : NULL;
		$_field['default']		= isset( $field['default'] ) ? $field['default'] : NULL;
		$_field['sub_label']	= isset( $field['sub_label'] ) ? $field['sub_label'] : NULL;
		$_field['required']		= isset( $field['required'] ) ? $field['required'] : FALSE;
		$_field['placeholder']	= isset( $field['placeholder'] ) ? $field['placeholder'] : NULL;
		$_field['readonly']		= isset( $field['readonly'] ) ? $field['readonly'] : FALSE;
		$_field['error']		= isset( $field['error'] ) ? $field['error'] : FALSE;
		
		$_help			= array();
		$_help['src']	= is_array( $help ) && isset( $help['src'] ) ? $help['src'] : 'assets/img/form/help.png';
		$_help['class']	= is_array( $help ) && isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= is_array( $help ) && isset( $help['rel'] ) ? $help['rel'] : 'tipsy-right';
		$_help['title']	= is_array( $help ) && isset( $help['title'] ) ? $help['title'] : NULL;
		$_help['title']	= is_string( $help ) ? $help : $_help['title'];
		
		$_error			= form_error( $_field['key'] ) || $_field['error'] ? 'error' : '';
		$_readonly		= $_field['readonly'] ? 'readonly="readonly"' : '';
		$_readonly_cls	= $_field['readonly'] ? 'readonly' : '';
		
		// --------------------------------------------------------------------------
		
		$_out  = '<div class="field ' . $_error . ' ' . $_field['oddeven'] . ' ' . $_readonly_cls . '">';
		$_out .= '<label>';
				
		//	Label
		$_out .= '<span class="label">';
		$_out .= $_field['label'];
		$_out .= $_field['required'] ? '*' : '';
		$_out .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';
		$_out .= '</span>';
		
		//	field
		switch ( $_field['type'] ) :
		
			case 'password' :
			
				$_out .= form_password( $_field['key'], set_value( $_field['key'], $_field['default'] ), 'placeholder="' . $_field['placeholder'] . '" ' . $_readonly );
				
			break;
			
			case 'textarea' :
			
				$_out .= form_textarea( $_field['key'], set_value( $_field['key'], $_field['default'] ), 'placeholder="' . $_field['placeholder'] . '" ' . $_readonly );
				
			break;
			
			case 'upload' :
			case 'file' :
			
				$_out .= form_upload( $_field['key'] );
				
			break;
			
			case 'text' :
			default :
			
				$_out .= form_input( $_field['key'], set_value( $_field['key'], $_field['default'] ), 'placeholder="' . $_field['placeholder'] . '" ' . $_readonly );
				
			break;
			
		endswitch;
		
		//	Download original file, if type is file and original is available
		if ( ( $_field['type'] == 'file' || $_field['type'] == 'upload' ) && $_field['default'] ) :
		
			$_out .= '<span class="file-download">';
			$_out .= 'Download: ' . anchor( cdn_serve( 'test', $_field['default'] ), $_field['default'] );
			$_out .= '</span>';
		
		endif;
		
		//	Tip
		$_out .= $_help['title'] ? img( $_help ) : '';
		
		//	Error
		if ( $_field['error'] ) :
		
			$_out .= '<span class="error">' . $_field['error'] . '</span>';
			
		else :
		
			$_out .= form_error( $_field['key'], '<span class="error">', '</span>' );
		
		endif;
				
		$_out .= '</label>';
		$_out .= '<div class="clear"></div>';
		$_out .= '</div>';
		
		// --------------------------------------------------------------------------
		
		return $_out;
	}
}


// --------------------------------------------------------------------------


/**
 * form_field_date
 *
 * Generates a form field (of type select) for dates
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */
if ( ! function_exists( 'form_field_date' ) )
{
	function form_field_date( $field, $help = '', $short = FALSE, $start_year = NULL, $end_year = NULL )
	{
		$_ci =& get_instance();
		$_ci->load->helper( 'date' );
		
		// --------------------------------------------------------------------------
		
		$short		= $short ? $short : FALSE;
		$start_year	= $start_year ? $start_year : date( 'Y' ) + 5;
		$end_year	= $end_year ? $end_year : 1900;
		
		// --------------------------------------------------------------------------
		
		//	Set var defaults
		$_field					= array();
		$_field['type']			= isset( $field['type'] ) ? $field['type'] : 'text';
		$_field['oddeven']		= isset( $field['oddeven'] ) ? $field['oddeven'] : NULL;
		$_field['key']			= isset( $field['key'] ) ? $field['key'] : NULL;
		$_field['label']		= isset( $field['label'] ) ? $field['label'] : NULL;
		$_field['default']		= isset( $field['default'] ) ? $field['default'] : NULL;
		$_field['sub_label']	= isset( $field['sub_label'] ) ? $field['sub_label'] : NULL;
		$_field['required']		= isset( $field['required'] ) ? $field['required'] : FALSE;
		$_field['placeholder']	= isset( $field['placeholder'] ) ? $field['placeholder'] : NULL;
		
		$_help			= array();
		$_help['src']	= is_array( $help ) && isset( $help['src'] ) ? $help['src'] : 'assets/img/form/help.png';
		$_help['class']	= is_array( $help ) && isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= is_array( $help ) && isset( $help['rel'] ) ? $help['rel'] : 'tipsy-right';
		$_help['title']	= is_array( $help ) && isset( $help['title'] ) ? $help['title'] : NULL;
		$_help['title']	= is_string( $help ) ? $help : $_help['title'];
		
		$_error = form_error( $_field['key'] . '_year' ) || form_error( $_field['key'] . '_month' ) || form_error( $_field['key'] . '_day' ) ? 'error' : '';
		
		// --------------------------------------------------------------------------
		
		$_out  = '<div class="field date-picker ' . $_error . ' ' . $_field['oddeven'] . '">';
		$_out .= '<label>';
				
		//	Label
		$_out .= '<span class="label">';
		$_out .= $_field['label'];
		$_out .= $_field['required'] ? '*' : '';
		$_out .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';
		$_out .= '</span>';
		
		if ( $_ci->input->post() ) :
		
			$_date		= array();
			$_date[]	= $_ci->input->post( $_field['key'] .'_year' );
			$_date[]	= $_ci->input->post( $_field['key'] .'_month' );
			$_date[]	= $_ci->input->post( $_field['key'] .'_day' );
		
		else :
		
			if ( isset( $_field['default'] ) ) :
			
				$_date	= explode( '-', $_field['default'] );
				
			else :
				
				$_date	= array( '' );
			
			endif;
			
		endif;
		
		if ( ! isset( $_date[1] ) )
			$_date[1] = FALSE;
			
		if ( ! isset( $_date[2] ) )
			$_date[2] = FALSE;
		
		//	Input
		$_out .= dropdown_days( $_field['key'] . '_day', $_date[2] );
		$_out .= dropdown_months( $_field['key'] . '_month', $short, $_date[1] );
		$_out .= dropdown_years( $_field['key'] . '_year', $start_year, $end_year, $_date[0] );
		
		//	Tip
		$_out .= $_help['title'] ? img( $_help ) : '';
		
		//	Error
		if ( $_error ) :
			$_out .= '<span class="error">' . form_error( $_field['key'] . '_day' ) . '</span>';
		endif;
				
		$_out .= '</label>';
		$_out .= '<div class="clear"></div>';
		$_out .= '</div>';
		
		// --------------------------------------------------------------------------
		
		return $_out;
	}
}


// --------------------------------------------------------------------------


/**
 * form_field_datetime
 *
 * Generates a form field (of type select) for datetime
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */
if ( ! function_exists( 'form_field_datetime' ) )
{
	function form_field_datetime( $field, $help = '', $short = TRUE, $start_year = NULL, $end_year = NULL )
	{
		$_ci =& get_instance();
		$_ci->load->helper( 'date' );
		
		// --------------------------------------------------------------------------
		
		$short		= $short ? $short : FALSE;
		$start_year	= $start_year ? $start_year : date( 'Y' ) + 5;
		$end_year	= $end_year ? $end_year : 1900;
		
		// --------------------------------------------------------------------------
		
		//	Set var defaults
		$_field					= array();
		$_field['type']			= isset( $field['type'] ) ? $field['type'] : 'text';
		$_field['oddeven']		= isset( $field['oddeven'] ) ? $field['oddeven'] : NULL;
		$_field['key']			= isset( $field['key'] ) ? $field['key'] : NULL;
		$_field['label']		= isset( $field['label'] ) ? $field['label'] : NULL;
		$_field['default']		= isset( $field['default'] ) ? $field['default'] : NULL;
		$_field['sub_label']	= isset( $field['sub_label'] ) ? $field['sub_label'] : NULL;
		$_field['required']		= isset( $field['required'] ) ? $field['required'] : FALSE;
		$_field['placeholder']	= isset( $field['placeholder'] ) ? $field['placeholder'] : NULL;
		
		$_help			= array();
		$_help['src']	= is_array( $help ) && isset( $help['src'] ) ? $help['src'] : 'assets/img/form/help.png';
		$_help['class']	= is_array( $help ) && isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= is_array( $help ) && isset( $help['rel'] ) ? $help['rel'] : 'tipsy-right';
		$_help['title']	= is_array( $help ) && isset( $help['title'] ) ? $help['title'] : NULL;
		$_help['title']	= is_string( $help ) ? $help : $_help['title'];
		
		$_error = form_error( $_field['key'] . '_year' ) || form_error( $_field['key'] . '_month' ) || form_error( $_field['key'] . '_day' ) ? 'error' : '';
		
		// --------------------------------------------------------------------------
		
		$_out  = '<div class="field date-picker datetime-picker ' . $_error . ' ' . $_field['oddeven'] . '">';
		$_out .= '<label>';
				
		//	Label
		$_out .= '<span class="label">';
		$_out .= $_field['label'];
		$_out .= $_field['required'] ? '*' : '';
		$_out .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';
		$_out .= '</span>';
		
		if ( $_ci->input->post() ) :
		
			$_datetime		= array();
			$_datetime[]	= $_ci->input->post( $_field['key'] .'_year' );
			$_datetime[]	= $_ci->input->post( $_field['key'] .'_month' );
			$_datetime[]	= $_ci->input->post( $_field['key'] .'_day' );
			$_datetime[]	= $_ci->input->post( $_field['key'] .'_hour' );
			$_datetime[]	= $_ci->input->post( $_field['key'] .'_minute' );
		
		else :
		
			if ( isset( $_field['default'] ) ) :
			
				//	Firstly, explode the space (expected string is in the format DDDD-DD-DD DD:DD:DD)
				$_temp	= explode( ' ', $_field['default'] );
				$_temp1	= isset( $_temp[0] ) ? explode( '-', $_temp[0] ) : array( '' );
				$_temp2	= isset( $_temp[1] ) ? explode( ':', $_temp[1] ) : array( '' );
				$_datetime = array_merge( $_temp1, $_temp2 );
				
			else :
				
				$_datetime	= array( '' );
			
			endif;
			
		endif;
		
		if ( ! isset( $_datetime[1] ) )
			$_datetime[1] = FALSE;
			
		if ( ! isset( $_datetime[2] ) )
			$_datetime[2] = FALSE;
			
		if ( ! isset( $_datetime[3] ) )
			$_datetime[3] = FALSE;
			
		if ( ! isset( $_datetime[4] ) )
			$_datetime[4] = FALSE;
		
		//	Input
		$_out .= dropdown_days( $_field['key'] . '_day', $_datetime[2] );
		$_out .= dropdown_months( $_field['key'] . '_month', $short, $_datetime[1] );
		$_out .= dropdown_years( $_field['key'] . '_year', $start_year, $end_year, $_datetime[0] );
		$_out .= dropdown_hours( $_field['key'] . '_hour', $_datetime[3] );
		$_out .= dropdown_minutes( $_field['key'] . '_minute', NULL, $_datetime[4] );
		
		//	Tip
		$_out .= $_help['title'] ? img( $_help ) : '';
		
		//	Error
		if ( $_error ) :
			$_out .= '<span class="error">' . form_error( $_field['key'] . '_day' ) . '</span>';
		endif;
				
		$_out .= '</label>';
		$_out .= '<div class="clear"></div>';
		$_out .= '</div>';
		
		// --------------------------------------------------------------------------
		
		return $_out;
	}
}


// --------------------------------------------------------------------------


/**
 * form_field_dropdown
 *
 * Generates a form field (of type select)
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */
if ( ! function_exists( 'form_field_dropdown' ) )
{
	function form_field_dropdown( $field, $options, $help = '' )
	{
		//	Set var defaults
		$_field					= array();
		$_field['type']			= isset( $field['type'] ) ? $field['type'] : 'text';
		$_field['oddeven']		= isset( $field['oddeven'] ) ? $field['oddeven'] : NULL;
		$_field['key']			= isset( $field['key'] ) ? $field['key'] : NULL;
		$_field['label']		= isset( $field['label'] ) ? $field['label'] : NULL;
		$_field['default']		= isset( $field['default'] ) ? $field['default'] : NULL;
		$_field['sub_label']	= isset( $field['sub_label'] ) ? $field['sub_label'] : NULL;
		$_field['required']		= isset( $field['required'] ) ? $field['required'] : FALSE;
		$_field['placeholder']	= isset( $field['placeholder'] ) ? $field['placeholder'] : NULL;
		
		$_help			= array();
		$_help['src']	= is_array( $help ) && isset( $help['src'] ) ? $help['src'] : 'assets/img/form/help.png';
		$_help['class']	= is_array( $help ) && isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= is_array( $help ) && isset( $help['rel'] ) ? $help['rel'] : 'tipsy-right';
		$_help['title']	= is_array( $help ) && isset( $help['title'] ) ? $help['title'] : NULL;
		$_help['title']	= is_string( $help ) ? $help : $_help['title'];
		
		$_error = form_error( $_field['key'] ) ? 'error' : '';
		
		// --------------------------------------------------------------------------
		
		$_out  = '<div class="field ' . $_error . ' ' . $_field['oddeven'] . '">';
		$_out .= '<label>';
				
		//	Label
		$_out .= '<span class="label">';
		$_out .= $_field['label'];
		$_out .= $_field['required'] ? '*' : '';
		$_out .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';
		$_out .= '</span>';
		
		//	field
		if ( ! isset( $options[0] ) ) :
		
			$_options = array( 'Please Choose...' ) + $options;
		
		else :
		
			$_options = $options;
		
		endif;
		
		$_out .= form_dropdown( $_field['key'], $_options, set_value( $_field['key'], $_field['default'] ) );
		
		//	Tip
		$_out .= $_help['title'] ? img( $_help ) : '';
		
		//	Error
		$_out .= form_error( $_field['key'], '<span class="error">', '</span>' );
				
		$_out .= '</label>';
		$_out .= '<div class="clear"></div>';
		$_out .= '</div>';
		
		// --------------------------------------------------------------------------
		
		return $_out;
	}
}


// --------------------------------------------------------------------------


/**
 * form_field_dropdow
 *
 * Generates a form field (of type select)
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */
if ( ! function_exists( 'form_field_boolean' ) )
{
	function form_field_boolean( $field, $help = '' )
	{
		$_options		= array();
		$_options[0]	= 'No';
		$_options[1]	= 'Yes';
		
		return form_field_dropdown( $field, $_options, $help );
	}
}


// --------------------------------------------------------------------------


/**
 * form_field
 *
 * Generates a form field containing radio buttons
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */
if ( ! function_exists( 'form_field_radio' ) )
{
	function form_field_radio( $field, $options, $help = '' )
	{
		$_ci =& get_instance();
		
		// --------------------------------------------------------------------------
		
		//	Set var defaults
		$_field					= array();
		$_field['oddeven']		= isset( $field['oddeven'] ) ? $field['oddeven'] : NULL;
		$_field['key']			= isset( $field['key'] ) ? $field['key'] : NULL;
		$_field['label']		= isset( $field['label'] ) ? $field['label'] : NULL;
		$_field['default']		= isset( $field['default'] ) ? $field['default'] : NULL;
		$_field['sub_label']	= isset( $field['sub_label'] ) ? $field['sub_label'] : NULL;
		$_field['required']		= isset( $field['required'] ) ? $field['required'] : FALSE;
		$_field['placeholder']	= isset( $field['placeholder'] ) ? $field['placeholder'] : NULL;
		
		$_help			= array();
		$_help['src']	= is_array( $help ) && isset( $help['src'] ) ? $help['src'] : 'assets/img/form/help.png';
		$_help['class']	= is_array( $help ) && isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= is_array( $help ) && isset( $help['rel'] ) ? $help['rel'] : 'tipsy-right';
		$_help['title']	= is_array( $help ) && isset( $help['title'] ) ? $help['title'] : NULL;
		$_help['title']	= is_string( $help ) ? $help : $_help['title'];
		
		$_error = form_error( $_field['key'] ) ? 'error' : '';
		
		// --------------------------------------------------------------------------
		
		$_out  = '<div class="field ' . $_error . ' ' . $_field['oddeven'] . '">';
		
		//	First option
		$_out .= '<label>';
				
		//	Label
		$_out .= '<span class="label">';
		$_out .= $_field['label'];
		$_out .= $_field['required'] ? '*' : '';
		$_out .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';
		$_out .= '</span>';
		
		//	field
		if ( $_ci->input->post( $_field['key'] ) ) :
		
			$_selected = $_ci->input->post( $_field['key'] ) == $options[0]['value'] ? TRUE : FALSE;
		
		else :
		
			$_selected = $options[0]['selected'];
		
		endif;
		$_out .= form_radio( $_field['key'], $options[0]['value'], $_selected ) . '<span class="text">' . $options[0]['label'] . '</span>';
		
		//	Tip
		$_out .= $_help['title'] ? img( $_help ) : '';
		
		//	Error
		$_out .= form_error( $_field['key'], '<span class="error">', '</span>' );
				
		$_out .= '</label>';
		
		
		//	Remaining options
		for ( $i = 1; $i < count( $options ); $i++ ) :
		
			$_out .= '<label>';
			
			//	Label
			$_out .= '<span class="label">&nbsp;</span>';
					
			//	Input
			if ( $_ci->input->post( $_field['key'] ) ) :
			
				$_selected = $_ci->input->post( $_field['key'] ) == $options[$i]['value'] ? TRUE : FALSE;
			
			else :
			
				$_selected = $options[$i]['selected'];
			
			endif;
			$_out .= form_radio( $_field['key'], $options[$i]['value'], $_selected ) . '<span class="text">' . $options[$i]['label'] . '</span>';
			
			$_out .= '</label>';
		
		endfor;
		
		$_out .= '<div class="clear"></div>';
		$_out .= '</div>';
		
		// --------------------------------------------------------------------------
		
		return $_out;
	}
}


// --------------------------------------------------------------------------


/**
 * form_field
 *
 * Generates a form field containing radio buttons
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */
if ( ! function_exists( 'form_field_checkbox' ) )
{
	function form_field_checkbox( $field, $options, $help = '' )
	{
		$_ci =& get_instance();
		
		// --------------------------------------------------------------------------
		
		//	Set var defaults
		$_field					= array();
		$_field['oddeven']		= isset( $field['oddeven'] ) ? $field['oddeven'] : NULL;
		$_field['key']			= isset( $field['key'] ) ? $field['key'] : NULL;
		$_field['label']		= isset( $field['label'] ) ? $field['label'] : NULL;
		$_field['default']		= isset( $field['default'] ) ? $field['default'] : NULL;
		$_field['sub_label']	= isset( $field['sub_label'] ) ? $field['sub_label'] : NULL;
		$_field['required']		= isset( $field['required'] ) ? $field['required'] : FALSE;
		$_field['placeholder']	= isset( $field['placeholder'] ) ? $field['placeholder'] : NULL;
		
		$_help			= array();
		$_help['src']	= is_array( $help ) && isset( $help['src'] ) ? $help['src'] : 'assets/img/form/help.png';
		$_help['class']	= is_array( $help ) && isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= is_array( $help ) && isset( $help['rel'] ) ? $help['rel'] : 'tipsy-right';
		$_help['title']	= is_array( $help ) && isset( $help['title'] ) ? $help['title'] : NULL;
		$_help['title']	= is_string( $help ) ? $help : $_help['title'];
		
		$_error = form_error( $_field['key'] ) ? 'error' : '';
		
		// --------------------------------------------------------------------------
		
		$_out  = '<div class="field ' . $_error . ' ' . $_field['oddeven'] . '">';
		
		//	First option
		$_out .= '<label>';
				
		//	Label
		$_out .= '<span class="label">';
		$_out .= $_field['label'];
		$_out .= $_field['required'] ? '*' : '';
		$_out .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';
		$_out .= '</span>';
		
		//	field
		if ( substr( $_field['key'], -2 ) == '[]' ) :
		
			//	Field is an array, need to look for the value
			$_values	= $_ci->input->post( substr( $_field['key'], 0, -2 ) );
			$_selected	= $_ci->input->post() ? FALSE : $options[0]['selected'];
			
			if ( is_array( $_values ) && array_search( $options[0]['value'], $_values ) !== FALSE ) :
			
				$_selected = TRUE;
			
			endif;
		
		else :
		
			//	Normal field, continue as normal Mr Norman!
			if ( $_ci->input->post( $_field['key'] ) ) :
			
				$_selected = $_ci->input->post( $_field['key'] ) == $options[0]['value'] ? TRUE : FALSE;
			
			else :
			
				$_selected = $options[0]['selected'];
			
			endif;
		
		endif;
		
		$_key = isset( $options[0]['key'] ) ? $options[0]['key'] : $_field['key'];
		
		$_out .= form_checkbox( $_key, $options[0]['value'], $_selected ) . '<span class="text">' . $options[0]['label'] . '</span>';
		
		//	Tip
		$_out .= $_help['title'] ? img( $_help ) : '';
		
		//	Error
		$_out .= form_error( $_field['key'], '<span class="error">', '</span>' );
				
		$_out .= '</label>';
		
		
		//	Remaining options
		for ( $i = 1; $i < count( $options ); $i++ ) :
		
			$_out .= '<label>';
			
			//	Label
			$_out .= '<span class="label">&nbsp;</span>';
			
			//	Input
			if ( substr( $_field['key'], -2 ) == '[]' ) :
			
				//	Field is an array, need to look for the value
				$_values	= $_ci->input->post( substr( $_field['key'], 0, -2 ) );
				$_selected	= $_ci->input->post() ? FALSE : $options[$i]['selected'];
				
				if ( is_array( $_values ) && array_search( $options[$i]['value'], $_values ) !== FALSE ) :
				
					$_selected = TRUE;
				
				endif;
			
			else :
			
				//	Normal field, continue as normal Mr Norman!
				if ( $_ci->input->post( $_field['key'] ) ) :
				
					$_selected = $_ci->input->post( $_field['key'] ) == $options[$i]['value'] ? TRUE : FALSE;
				
				else :
				
					$_selected = $options[$i]['selected'];
				
				endif;
			
			endif;
			
			$_key = isset( $options[$i]['key'] ) ? $options[$i]['key'] : $_field['key'];
			
			$_out .= form_checkbox( $_key, $options[$i]['value'], $_selected ) . '<span class="text">' . $options[$i]['label'] . '</span>';
			
			$_out .= '</label>';
		
		endfor;
		
		$_out .= '<div class="clear"></div>';
		$_out .= '</div>';
		
		// --------------------------------------------------------------------------
		
		return $_out;
	}
}


// --------------------------------------------------------------------------


/**
 * form_field
 *
 * Generates a form field containing radio buttons
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */
if ( ! function_exists( 'form_field_submit' ) )
{
	function form_field_submit( $button_value = 'Submit', $button_name = 'submit', $odd_even = '' )
	{
		$_out  = '<div class="field submit ' . $odd_even . '">';
				
		//	Label
		$_out .= '<span class="label">&nbsp;</span>';
		
		//	field
		$_out .= form_submit( $button_name, $button_value, 'class=""' );
				
		$_out .= '<div class="clear"></div>';
		$_out .= '</div>';
		
		// --------------------------------------------------------------------------
		
		return $_out;
	}
}