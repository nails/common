<fieldset id="edit-user-security-questions">

	<legend><?=lang( 'accounts_edit_security_questions_legend' )?></legend>

	<div class="box-container">
	<?php

		//	Require new securty questions on log in
		$_field					= array();
		$_field['key']			= 'reset_security_questions';
		$_field['label']		= lang( 'accounts_edit_security_questions_field_reset_label' );
		$_field['default']		= FALSE;
		$_field['required']		= FALSE;

		$_options	= array();

		$_options[] = array(
			'value'		=> 'TRUE',
			'label'		=> lang( 'accounts_edit_security_questions_field_reset_yes' ),
			'selected'	=> set_radio( $_field['key'] ) ? TRUE : FALSE
		);

		$_options[] = array(
			'value'		=> 'FALSE',
			'label'		=> lang( 'accounts_edit_security_questions_field_reset_no' ),
			'selected'	=>	! set_radio( $_field['key'] ) ? TRUE : FALSE
		);

		echo form_field_radio( $_field, $_options );

	?>
	</div>

</fieldset>