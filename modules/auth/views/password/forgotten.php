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
	
	//	Form attributes
	$attr = array(
	
		'id'	=> 'forgotten-password-form',
		'class'	=> 'container nails-default-form'
		
	);
	
	echo form_open( 'auth/forgotten_password', $attr );
	
	// --------------------------------------------------------------------------
	
	//	Write the HTML for the forgotten password form
?>

	<p>
		Please enter your registered email address so we can send you an email with a link which
		you can use to reset your password.
	</p>
	
	
	<!--	INPUT FIELDS	-->
	<?php
		
		$_field	= 'email';
		$_name	= 'Email';
		$_error = form_error( $_field ) ? 'error' : NULL
	
	?>
	<div class="row <?=$_error?>">
		<?=form_label( $_name, $_field, array( 'class' => 'two columns first' ) ); ?>
		<div class="four columns last">
			<?=form_input( $_field, set_value( $_field ), 'placeholder="' . $_name . '"' )?>
			<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
		</div>
	</div>
	
	<div class="row button-row">
		<label class="two columns first">&nbsp;</label>
		<div class="four columns last">
			<?=form_submit( 'submit', 'Reset Password', 'class="awesome"' )?>
		</div>
	</div>
	
	
<?php
	
	// --------------------------------------------------------------------------
	
	//	Close the form
	echo form_close();