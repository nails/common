<?php

	/**
	 *	THE FORGOTTEN PASSWORD FORM
	 *	
	 *	This view contains only the basic form required for resetting a password. The controller
	 *	will look for an app version of the file  first and load that up. It will fall back
	 *	to the empty Nails view if not available (which includes some basic styling so as not
	 *	to look totally rubbish).
	 *	
	 *	You can completely overload this view by creating a view at:
	 *	
	 *	application/views/auth/password/forgotten
	 *	
	 **/
	
	// --------------------------------------------------------------------------
	
	/**
	 *	ERRORS
	 *	
	 *	Only individual field errors are shown, generic erros (such as email send failure)
	 *	should be handled by the containing header files.
	 *	
	 **/
	 
?>

	<p>
		Please enter your registered email address so we can send you an email with a link which
		you can use to reset your password.
	</p>

<?php
	
	//	Form attributes
	$attr = array(
	
		'id'	=> 'forgotten-password-form',
		'class'	=>	'well form-horizontal'
		
	);
	
	echo form_open( 'auth/forgotten_password', $attr );
	
	// --------------------------------------------------------------------------
	
	//	Write the HTML for the forgotten password form
?>
	
	
	<!--	INPUT FIELDS	-->
	<?php
		
		$_field	= 'email';
		$_name	= 'Email';
		$_error = form_error( $_field ) ? 'error' : NULL
	
	?>
	<div class="control-group <?=$_error?>">
		<?=form_label( $_name, $_field, array( 'class' => 'control-label' ) ); ?>
		<div class="controls">
			<?=form_input( $_field, set_value( $_field ), 'placeholder="' . $_name . '"' )?>
			<?=form_error( $_field, '<span class="help-inline">', '</span>' )?>
		</div>
	</div>
	
	<hr />
	
	<div class="control-group">
		<label class="control-label">&nbsp;</label>
		<div class="controls">
			<?=form_submit( 'submit', 'Reset Password', 'class="btn btn-primary"' )?>
		</div>
	</div>
	
	
<?php
	
	// --------------------------------------------------------------------------
	
	//	Close the form
	echo form_close();