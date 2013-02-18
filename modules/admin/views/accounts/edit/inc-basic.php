<fieldset>

	<legend>Basic Information</legend>
	
	<div class="box-container">
	<?php
	
		//	Group ID
		$_field					= array();
		$_field['key']			= 'group_id';
		$_field['label']		= 'User Group';
		$_field['default']		= $user_edit->group_id;
		$_field['required']		= TRUE;
		
		echo form_field_dropdown( $_field, $groups, 'Specify to which group this user belongs.' );
		
		// --------------------------------------------------------------------------
		
		//	Reset Password
		$_field					= array();
		$_field['key']			= 'password';
		$_field['label']		= 'Reset Password';
		$_field['default']		= '';
		$_field['required']		= FALSE;
		$_field['placeholder']	= 'Reset the user\'s password by specifying a new one here.';
		
		echo form_field( $_field, 'The user will NOT be informed of the password change.' );
		
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
			'label'		=> '<strong>Yes</strong>, require user to update password on next log in.',
			'selected'	=> $user_edit->temp_pw ? TRUE : FALSE
		);
		$_options[] = array(
			'value'		=> 'FALSE',
			'label'		=> '<strong>No</strong>, do not require user to update password on next log in.',
			'selected'	=>	! $user_edit->temp_pw ? TRUE : FALSE
		);
		
		echo form_field_radio( $_field, $_options );
		
		// --------------------------------------------------------------------------
		
		//	First Name
		$_field					= array();
		$_field['key']			= 'first_name';
		$_field['label']		= 'First Name';
		$_field['default']		= $user_edit->first_name;
		$_field['required']		= TRUE;
		$_field['placeholder']	= 'The user\'s first name';
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Last name
		$_field					= array();
		$_field['key']			= 'last_name';
		$_field['label']		= 'Last Name';
		$_field['default']		= $user_edit->last_name;
		$_field['required']		= TRUE;
		$_field['placeholder']	= 'The user\'s last name';
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Email address
		$_field					= array();
		$_field['key']			= 'email';
		$_field['label']		= 'Email';
		$_field['default']		= $user_edit->email;
		$_field['required']		= TRUE;
		$_field['placeholder']	= 'The user\'s email address';
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Email verified
		$_field					= array();
		$_field['key']			= 'active';
		$_field['label']		= 'Email verified';
		$_field['default']		= $user_edit->active ? 'Yes' : 'No';
		$_field['required']		= FALSE;
		$_field['placeholder']	= 'The user\'s email address has been verified';
		$_field['readonly']		= TRUE;
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Username
		$_field					= array();
		$_field['key']			= 'username';
		$_field['label']		= 'Username';
		$_field['default']		= $user_edit->username;
		$_field['required']		= FALSE;
		$_field['placeholder']	= 'The user\'s username';
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Registered IP
		$_field					= array();
		$_field['key']			= 'ip_address';
		$_field['label']		= 'Registered IP';
		$_field['default']		= $user_edit->ip_address;
		$_field['required']		= FALSE;
		$_field['placeholder']	= 'The user\'s IP address when they registered';
		$_field['readonly']		= TRUE;
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Last IP
		$_field					= array();
		$_field['key']			= 'last_ip';
		$_field['label']		= 'Last IP';
		$_field['default']		= $user_edit->last_ip;
		$_field['required']		= FALSE;
		$_field['placeholder']	= 'The user\'s last recorded IP address';
		$_field['readonly']		= TRUE;
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Created On
		$_field					= array();
		$_field['key']			= 'created_on';
		$_field['label']		= 'Created';
		$_field['default']		= date( 'jS M Y @ H:i', strtotime( $user_edit->created_on ) );
		$_field['required']		= FALSE;
		$_field['placeholder']	= 'The time and date the user created the account';
		$_field['readonly']		= TRUE;
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Created On
		$_field					= array();
		$_field['key']			= 'last_update';
		$_field['label']		= 'Modified';
		$_field['default']		= date( 'jS M Y @ H:i', strtotime( $user_edit->last_update ) );
		$_field['required']		= FALSE;
		$_field['placeholder']	= 'The time and date the user was last modified';
		$_field['readonly']		= TRUE;
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Log in count
		$_field					= array();
		$_field['key']			= 'login_count';
		$_field['label']		= 'Log in counter';
		$_field['default']		= $user_edit->login_count ? $user_edit->login_count : 'Never Logged In';
		$_field['required']		= FALSE;
		$_field['placeholder']	= 'The number of times a user has logged in';
		$_field['readonly']		= TRUE;
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Last Log in
		$_field					= array();
		$_field['key']			= 'last_login';
		$_field['label']		= 'Last Log in';
		$_field['default']		= $user_edit->last_login ? date( 'jS M Y @ H:i', strtotime( $user_edit->last_login ) ) : 'Never Logged In';
		$_field['required']		= FALSE;
		$_field['placeholder']	= 'The time and date the user last logged in';
		$_field['readonly']		= TRUE;
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Referral Code
		$_field					= array();
		$_field['key']			= 'referral';
		$_field['label']		= 'Referral Code';
		$_field['default']		= $user_edit->referral;
		$_field['required']		= FALSE;
		$_field['placeholder']	= 'The user\'s referral code';
		$_field['readonly']		= TRUE;
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Referred by
		$_field					= array();
		$_field['key']			= 'referred_by';
		$_field['label']		= 'Referred By';
		$_field['default']		= $user_edit->referred_by;
		$_field['required']		= FALSE;
		$_field['placeholder']	= 'The user who referred this user, if any';
		$_field['readonly']		= TRUE;
		
		echo form_field( $_field );
	
	?>
	</div>
</fieldset>