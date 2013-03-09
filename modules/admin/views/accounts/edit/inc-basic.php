<fieldset>

	<legend><?=lang( 'accounts_edit_basic_legend' )?></legend>
	
	<div class="box-container">
	<?php
	
		//	Group ID
		$_field					= array();
		$_field['key']			= 'group_id';
		$_field['label']		= lang( 'accounts_edit_basic_field_group_label' );
		$_field['default']		= $user_edit->group_id;
		$_field['required']		= TRUE;
		
		echo form_field_dropdown( $_field, $groups, lang( 'accounts_edit_basic_field_group_tip' ) );
		
		// --------------------------------------------------------------------------
		
		//	Reset Password
		$_field					= array();
		$_field['key']			= 'password';
		$_field['label']		= lang( 'accounts_edit_basic_field_password_label' );
		$_field['default']		= '';
		$_field['required']		= FALSE;
		$_field['placeholder']	= lang( 'accounts_edit_basic_field_password_placeholder' );
		
		echo form_field( $_field, lang( 'accounts_edit_basic_field_password_tip' ) );
		
		// --------------------------------------------------------------------------
		
		//	Require password update on log in
		$_field					= array();
		$_field['key']			= 'temp_pw';
		$_field['label']		= lang( 'accounts_edit_basic_field_temp_pw_label' );
		$_field['default']		= FALSE;
		$_field['required']		= FALSE;
		
		$_options = array();
		$_options[] = array(
			'value'		=> 'TRUE',
			'label'		=> lang( 'accounts_edit_basic_field_temp_pw_yes' ),
			'selected'	=> $user_edit->temp_pw ? TRUE : FALSE
		);
		$_options[] = array(
			'value'		=> 'FALSE',
			'label'		=> lang( 'accounts_edit_basic_field_temp_pw_no' ),
			'selected'	=>	! $user_edit->temp_pw ? TRUE : FALSE
		);
		
		echo form_field_radio( $_field, $_options );
		
		// --------------------------------------------------------------------------
		
		//	First Name
		$_field					= array();
		$_field['key']			= 'first_name';
		$_field['label']		= lang( 'form_label_first_name' );
		$_field['default']		= $user_edit->first_name;
		$_field['required']		= TRUE;
		$_field['placeholder']	= lang( 'accounts_edit_basic_field_first_placeholder' );
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Last name
		$_field					= array();
		$_field['key']			= 'last_name';
		$_field['label']		= lang( 'form_label_last_name' );
		$_field['default']		= $user_edit->last_name;
		$_field['required']		= TRUE;
		$_field['placeholder']	= lang( 'accounts_edit_basic_field_last_placeholder' );
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Email address
		$_field					= array();
		$_field['key']			= 'email';
		$_field['label']		= lang( 'form_label_email' );
		$_field['default']		= $user_edit->email;
		$_field['required']		= TRUE;
		$_field['placeholder']	= lang( 'accounts_edit_basic_field_email_placeholder' );
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Email verified
		$_field					= array();
		$_field['key']			= 'is_verified';
		$_field['label']		= lang( 'accounts_edit_basic_field_verified_label' );
		$_field['default']		= $user_edit->is_verified ? lang( 'Yes' ) : lang( 'no' );
		$_field['required']		= FALSE;
		$_field['readonly']		= TRUE;
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Username
		$_field					= array();
		$_field['key']			= 'username';
		$_field['label']		= lang( 'accounts_edit_basic_field_username_label' );
		$_field['default']		= $user_edit->username;
		$_field['required']		= FALSE;
		$_field['placeholder']	= lang( 'accounts_edit_basic_field_username_placeholder' );
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Registered IP
		$_field					= array();
		$_field['key']			= 'ip_address';
		$_field['label']		= lang( 'accounts_edit_basic_field_register_ip_label' );
		$_field['default']		= $user_edit->ip_address;
		$_field['required']		= FALSE;
		$_field['readonly']		= TRUE;
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Last IP
		$_field					= array();
		$_field['key']			= 'last_ip';
		$_field['label']		= lang( 'accounts_edit_basic_field_last_ip_label' );
		$_field['default']		= $user_edit->last_ip;
		$_field['required']		= FALSE;
		$_field['readonly']		= TRUE;
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Created On
		$_field					= array();
		$_field['key']			= 'created';
		$_field['label']		= lang( 'accounts_edit_basic_field_created_label' );
		$_field['default']		= date( 'jS M Y @ H:i', strtotime( $user_edit->created ) );
		$_field['required']		= FALSE;
		$_field['readonly']		= TRUE;
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Created On
		$_field					= array();
		$_field['key']			= 'last_update';
		$_field['label']		= lang( 'accounts_edit_basic_field_modified_label' );
		$_field['default']		= date( 'jS M Y @ H:i', strtotime( $user_edit->last_update ) );
		$_field['required']		= FALSE;
		$_field['readonly']		= TRUE;
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Log in count
		$_field					= array();
		$_field['key']			= 'login_count';
		$_field['label']		= lang( 'accounts_edit_basic_field_logincount_label' );
		$_field['default']		= $user_edit->login_count ? $user_edit->login_count : lang( 'accounts_edit_basic_field_not_logged_in' );
		$_field['required']		= FALSE;
		$_field['readonly']		= TRUE;
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Last Log in
		$_field					= array();
		$_field['key']			= 'last_login';
		$_field['label']		= lang( 'accounts_edit_basic_field_last_login_label' );
		$_field['default']		= $user_edit->last_login ? date( 'jS M Y @ H:i', strtotime( $user_edit->last_login ) ) : lang( 'accounts_edit_basic_field_not_logged_in' );
		$_field['required']		= FALSE;
		$_field['readonly']		= TRUE;
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Referral Code
		$_field					= array();
		$_field['key']			= 'referral';
		$_field['label']		= lang( 'accounts_edit_basic_field_referral_label' );
		$_field['default']		= $user_edit->referral;
		$_field['required']		= FALSE;
		$_field['readonly']		= TRUE;
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Referred by
		$_field					= array();
		$_field['key']			= 'referred_by';
		$_field['label']		= lang( 'accounts_edit_basic_field_referred_by_label' );
		$_field['default']		= $user_edit->referred_by;
		$_field['required']		= FALSE;
		$_field['placeholder']	= lang( 'accounts_edit_basic_field_referred_by_placeholder' );
		$_field['readonly']		= TRUE;
		
		echo form_field( $_field );
	
	?>
	</div>
</fieldset>