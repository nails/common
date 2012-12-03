<fieldset>

	<legend>Basic Information</legend>
	
	<div class="box-container">
	<?php
	
		//	Group ID
		$_field					= array();
		$_field['key']			= 'group_id';
		$_field['label']		= 'User Group';
		$_field['required']		= TRUE;
		
		echo form_field_dropdown( $_field, $groups, 'Specify to which group this user belongs.' );
		
		// --------------------------------------------------------------------------
		
		//	Password
		$_field					= array();
		$_field['key']			= 'password';
		$_field['label']		= 'Password';
		$_field['required']		= TRUE;
		$_field['placeholder']	= 'The user\'s password';
		
		echo form_field( $_field, 'The user will not be automatically informed what their password is.' );
		
		// --------------------------------------------------------------------------
		
		//	Require password update on log in
		$_field					= array();
		$_field['key']			= 'temp_pw';
		$_field['label']		= 'Update on log in';
		$_field['default']		= FALSE;
		$_field['required']		= FALSE;
		
		$_options = array();
		$_options[] = array(
			'value'		=> 'TRUE',
			'label'		=> '<strong>Yes</strong>, require user to update password on first log in.',
			'selected'	=> TRUE
		);
		$_options[] = array(
			'value'		=> 'FALSE',
			'label'		=> '<strong>No</strong>, do not require user to update password on first log in.',
			'selected'	=>	FALSE
		);
		
		echo form_field_radio( $_field, $_options );
		
		// --------------------------------------------------------------------------
		
		//	First Name
		$_field					= array();
		$_field['key']			= 'first_name';
		$_field['label']		= 'First Name';
		$_field['required']		= TRUE;
		$_field['placeholder']	= 'The user\'s first name';
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Last name
		$_field					= array();
		$_field['key']			= 'last_name';
		$_field['label']		= 'Last Name';
		$_field['required']		= TRUE;
		$_field['placeholder']	= 'The user\'s last name';
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Email address
		$_field					= array();
		$_field['key']			= 'email';
		$_field['label']		= 'Email';
		$_field['required']		= TRUE;
		$_field['placeholder']	= 'The user\'s email address';
		
		echo form_field( $_field );
	
	?>
	</div>
</fieldset>