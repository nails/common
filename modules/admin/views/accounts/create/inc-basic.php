<fieldset>

	<legend><?=lang( 'accounts_create_basic_legend' )?></legend>
	
	<div class="box-container">
	<?php
	
		//	Group ID
		$_field					= array();
		$_field['key']			= 'group_id';
		$_field['label']		= lang( 'accounts_create_field_group_label' );
		$_field['required']		= TRUE;
		
		echo form_field_dropdown( $_field, $groups, lang( 'accounts_create_field_group_tip' ) );
		
		// --------------------------------------------------------------------------
		
		//	Password
		$_field					= array();
		$_field['key']			= 'password';
		$_field['label']		= lang( 'form_label_password' );
		$_field['placeholder']	= lang( 'accounts_create_field_password_placeholder' );
		
		echo form_field( $_field, lang( 'accounts_create_field_password_tip' ) );
		
		// --------------------------------------------------------------------------
		
		//	Send welcome/activation email
		$_field					= array();
		$_field['key']			= 'send_activation';
		$_field['label']		= lang( 'accounts_create_field_send_welcome_label' );
		$_field['default']		= FALSE;
		$_field['required']		= FALSE;
		
		$_options = array();
		$_options[] = array(
			'value'		=> 'TRUE',
			'label'		=> lang( 'accounts_create_field_send_welcome_yes' ),
			'selected'	=> TRUE
		);
		$_options[] = array(
			'value'		=> 'FALSE',
			'label'		=> lang( 'accounts_create_field_send_welcome_no' ),
			'selected'	=>	FALSE
		);
		
		echo form_field_radio( $_field, $_options );
		
		// --------------------------------------------------------------------------
		
		//	Require password update on log in
		$_field					= array();
		$_field['key']			= 'temp_pw';
		$_field['label']		= lang( 'accounts_create_field_temp_pw_label' );
		$_field['default']		= FALSE;
		$_field['required']		= FALSE;
		
		$_options = array();
		$_options[] = array(
			'value'		=> 'TRUE',
			'label'		=> lang( 'accounts_create_field_temp_pw_yes' ),
			'selected'	=> TRUE
		);
		$_options[] = array(
			'value'		=> 'FALSE',
			'label'		=> lang( 'accounts_create_field_temp_pw_no' ),
			'selected'	=>	FALSE
		);
		
		echo form_field_radio( $_field, $_options );
		
		// --------------------------------------------------------------------------
		
		//	First Name
		$_field					= array();
		$_field['key']			= 'first_name';
		$_field['label']		= lang( 'form_label_first_name' );
		$_field['required']		= TRUE;
		$_field['placeholder']	= lang( 'accounts_create_field_first_placeholder' );
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Last name
		$_field					= array();
		$_field['key']			= 'last_name';
		$_field['label']		= lang( 'form_label_last_name' );
		$_field['required']		= TRUE;
		$_field['placeholder']	= lang( 'accounts_create_field_last_placeholder' );
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Email address
		$_field					= array();
		$_field['key']			= 'email';
		$_field['label']		= 'Email';
		$_field['required']		= TRUE;
		$_field['placeholder']	= lang( 'accounts_create_field_email_placeholder' );;
		
		echo form_field( $_field );
	
	?>
	</div>
</fieldset>