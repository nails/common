<?php

	/**
	 *	THE RESET PASSWORD FORM
	 *	
	 *	This view contains only the basic form required for resetting a user's temp password.
	 *	The controller will look for an app version of the file  first and load that up. It
	 *	will fall back to the empty Nails view if not available (which includes some basic
	 *	styling so as not to look totally rubbish).
	 *	
	 *	You can completely overload this view by creating a view at:
	 *	
	 *	application/views/auth/papssword/change_temp
	 *	
	 **/
	
	// --------------------------------------------------------------------------
	
	/**
	 *	ERRORS
	 *	
	 *	Only individual field errors are shown, generic erros should be handled by the containing
	 *	header files.
	 *	
	 **/
	 
	
	//	Form attributes
	$attr = array(
	
		'id'	=> 'reset-temp-password-form',
		'class'	=> 'well'
		
	);
	
	//	If there's a 'return_to' variable set it as a GET variable in case there;'s a form
	//	validation error. Otherwise don't show it - cleaner.
	
	echo ( $return_to ) ? form_open( 'auth/reset_password/' . $auth->id . '/' . $auth->hash . '?return_to=' . $return_to, $attr ) : form_open( 'auth/reset_password/' . $auth->id . '/' . $auth->hash, $attr );
	
	// --------------------------------------------------------------------------
	
	//	Write the HTML for the reset password form
?>
	
	
	<?=form_hidden( 'resetme', TRUE )?>
	
	<!--	INPUT FIELDS	-->
	<?php
		
		$_field	= 'new_password';
		$_name	= 'New Password';
		$_error = form_error( $_field ) ? 'error' : NULL
	
	?>
	<div class="control-group <?=$_error?>">
		<?=form_label( $_name, $_field, array( 'class' => 'control-label' ) ); ?>
		<div class="controls">
			<?=form_password( $_field, NULL, 'placeholder="' . $_name . '"' )?>
			<?=form_error( $_field, '<span class="help-inline">', '</span>' )?>
		</div>
	</div>
	
	<?php
		
		$_field	= 'confirm_pass';
		$_name	= 'Confirm';
		$_error = form_error( $_field ) ? 'error' : NULL
	
	?>
	<div class="control-group <?=$_error?>">
		<?=form_label( $_name, $_field, array( 'class' => 'control-label' ) ); ?>
		<div class="controls">
			<?=form_password( $_field, NULL, 'placeholder="' . $_name . '"' )?>
			<?=form_error( $_field, '<span class="help-inline">', '</span>' )?>
		</div>
	</div>
	
	<hr />
	
	<div class="control-group">
		<label class="control-label">&nbsp;</label>
		<div class="controls">
			<?=form_submit( 'submit', lang( 'action_reset_continue' ), 'class="btn btn-primary"' )?>
		</div>
	</div>
	
	
<?php
	
	// --------------------------------------------------------------------------
	
	//	Close the form
	echo form_close();