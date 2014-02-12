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
		<?=lang( 'auth_forgot_message' )?>
	</p>


	<!--	INPUT FIELDS	-->
	<?php

		switch ( APP_NATIVE_LOGIN_USING ) :

			case 'EMAIL' :

				$_name			= lang( 'form_label_email' );
				$_placeholder	= lang( 'auth_forgot_email_placeholder' );

			break;

			case 'USERNAME' :

				$_name			= lang( 'form_label_username' );
				$_placeholder	= lang( 'auth_forgot_username_placeholder' );

			break;

			case 'BOTH' :
			default :

				$_name			= lang( 'auth_forgot_both' );
				$_placeholder	= lang( 'auth_forgot_both_placeholder' );

			break;

		endswitch;

		$_field			= 'identifier';
		$_error			= form_error( $_field ) ? 'error' : NULL

	?>
	<div class="row <?=$_error?>">
		<?=form_label( $_name, 'input-' . $_field, array( 'class' => 'two columns first' ) ); ?>
		<div class="four columns last">
			<?=form_input( $_field, set_value( $_field ), 'id="input-' . $_field . '" placeholder="' . $_placeholder . '"' )?>
			<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
		</div>
	</div>

	<div class="row button-row">
		<label class="two columns first">&nbsp;</label>
		<div class="four columns last">
			<?=form_submit( 'submit', lang( 'auth_forgot_action_reset' ), 'class="awesome"' )?>
		</div>
	</div>


<?php

	// --------------------------------------------------------------------------

	//	Close the form
	echo form_close();