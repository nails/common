<fieldset id="edit-user-password">

	<legend><?=lang( 'accounts_edit_password_legend' )?></legend>
	
	<div class="box-container">
	<?php
		//	Reset Password
		$_field					= array();
		$_field['key']			= 'password';
		$_field['label']		= lang( 'accounts_edit_password_field_password_label' );
		$_field['default']		= '';
		$_field['required']		= FALSE;
		$_field['placeholder']	= lang( 'accounts_edit_password_field_password_placeholder' );
		
		echo form_field( $_field, lang( 'accounts_edit_password_field_password_tip' ) );
		
		// --------------------------------------------------------------------------
		
		//	Require password update on log in
		$_field					= array();
		$_field['key']			= 'temp_pw';
		$_field['label']		= lang( 'accounts_edit_password_field_temp_pw_label' );
		$_field['default']		= FALSE;
		$_field['required']		= FALSE;
		
		$_options = array();
		$_options[] = array(
			'value'		=> 'TRUE',
			'label'		=> lang( 'accounts_edit_password_field_temp_pw_yes' ),
			'selected'	=> $user_edit->temp_pw ? TRUE : FALSE
		);
		$_options[] = array(
			'value'		=> 'FALSE',
			'label'		=> lang( 'accounts_edit_password_field_temp_pw_no' ),
			'selected'	=>	! $user_edit->temp_pw ? TRUE : FALSE
		);
		
		echo form_field_radio( $_field, $_options );

	?>
	</div>

</fieldset>