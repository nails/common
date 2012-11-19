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
		
		$_help			= array();
		$_help['src']	= isset( $help['key'] ) ? $help['key'] : 'assets/img/form/help.png';
		$_help['class']	= isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= isset( $help['rel'] ) ? $help['rel'] : 'tipsy-right';
		$_help['title']	= isset( $help['title'] ) ? $help['title'] : NULL;
		$_help['title']	= is_string( $help ) ? $help : $_help['title'];
		
		$_error			= form_error( $_field['key'] ) ? 'error' : '';
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
			
			case 'text' :
			default :
			
				$_out .= form_input( $_field['key'], set_value( $_field['key'], $_field['default'] ), 'placeholder="' . $_field['placeholder'] . '" ' . $_readonly );
				
			break;
			
		endswitch;
		
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
if ( ! function_exists( 'form_field_date' ) )
{
	function form_field_date( $field, $help = '' )
	{
		$_ci =& get_instance();
		
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
		$_help['src']	= isset( $help['key'] ) ? $help['key'] : 'assets/img/form/help.png';
		$_help['class']	= isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= isset( $help['rel'] ) ? $help['rel'] : 'tipsy-right';
		$_help['title']	= isset( $help['title'] ) ? $help['title'] : NULL;
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
		
			$_dob	= array();
			$_dob[]	= $_ci->input->post( $_field['key'] .'_year' );
			$_dob[]	= $_ci->input->post( $_field['key'] .'_month' );
			$_dob[]	= $_ci->input->post( $_field['key'] .'_day' );
		
		else :
		
			if ( isset( $_field['default'] ) ) :
			
				$_dob	= explode( '-', $_field['default'] );
				
			else :
				
				$_dob	= array( '' );
			
			endif;
			
		endif;
		
		if ( ! isset( $_dob[1] ) )
			$_dob[1] = FALSE;
			
		if ( ! isset( $_dob[2] ) )
			$_dob[2] = FALSE;
		
		//	Input
		$_out .= dropdown_days( $_field['key'] . '_day', $_dob[2] );
		$_out .= dropdown_months( $_field['key'] . '_month', FALSE, $_dob[1] );
		$_out .= dropdown_years( $_field['key'] . '_year', date( 'Y' ), 1900, $_dob[0] );
		
		//	Tip
		$_out .= $_help['title'] ? img( $_help ) : '';
		
		//	Error
		if ( $_error ) :
			$_out .= '<span class="error">Please enter a valid date.</span>';
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
 * form_field_dropdow
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
		$_help['src']	= isset( $help['key'] ) ? $help['key'] : 'assets/img/form/help.png';
		$_help['class']	= isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= isset( $help['rel'] ) ? $help['rel'] : 'tipsy-right';
		$_help['title']	= isset( $help['title'] ) ? $help['title'] : NULL;
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
		$_help['src']	= isset( $help['key'] ) ? $help['key'] : 'assets/img/form/help.png';
		$_help['class']	= isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= isset( $help['rel'] ) ? $help['rel'] : 'tipsy-right';
		$_help['title']	= isset( $help['title'] ) ? $help['title'] : NULL;
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
		$_help['src']	= isset( $help['key'] ) ? $help['key'] : 'assets/img/form/help.png';
		$_help['class']	= isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= isset( $help['rel'] ) ? $help['rel'] : 'tipsy-right';
		$_help['title']	= isset( $help['title'] ) ? $help['title'] : NULL;
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
		$_out .= form_checkbox( $_field['key'], $options[0]['value'], $_selected ) . '<span class="text">' . $options[0]['label'] . '</span>';
		
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
			$_out .= form_checkbox( $_field['key'], $options[$i]['value'], $_selected ) . '<span class="text">' . $options[$i]['label'] . '</span>';
			
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