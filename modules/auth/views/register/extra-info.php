<?php

	/**
	 *	THE REGISTRATION FORM
	 *
	 *	This view contains only the basic form required for registering a user. The controller
	 *	will look for an app version of the file  first and load that up. It will fall back
	 *	to the empty Nails view if not available (which includes some basic styling so
	 *	as not to look totally rubbish).
	 *
	 *	You can completely overload this view by creating a view at:
	 *
	 *	application/views/auth/register/form
	 *
	 **/

	// --------------------------------------------------------------------------

	/**
	 *	REGISTRATION ERRORS
	 *
	 *	Only individual field errors are shown, generic erros should be handled by the
	 *	containing header files.
	 *
	 **/


	//	Form attributes
	$attr = array(

		'id'	=> 'register-form',
		'class'	=> 'container nails-default-form'

	);

	if ( $return_to || $return_to_fail ) :

		$_returns = '?';
		$_returns .= $return_to ? 'return_to=' . urlencode( $return_to ) : '';
		$_returns .= $return_to_fail ? '&return_to_fail=' . urlencode( $return_to_fail ) : '';

	else :

		$_returns = '';

	endif;

	//It also needs to be possible to collect fields dynamically via a config.' );

	echo form_open( 'auth/' . $this->uri->segment( 2 ) . '/connect/verify' . $_returns, $attr );

	// --------------------------------------------------------------------------

	//	Write the HTML for the register form
?>
	<p>
		<?=lang( 'auth_register_extra_message' )?>
	</p>

	<?php

		if ( APP_NATIVE_LOGIN_USING == 'EMAIL' || APP_NATIVE_LOGIN_USING == 'BOTH' ) :

			$_field			= 'email';
			$_name			= lang( 'form_label_email' );
			$_placeholder	= lang( 'auth_register_email_placeholder' );
			$_error			= form_error( $_field ) ? 'error' : NULL;
			$_default		= ! empty( $email ) ? $email : '';

			?>
			<div class="row <?=$_error?>">
				<?=form_label( $_name, 'input-' . $_field, array( 'class' => 'two columns first' ) ); ?>
				<div class="four columns">
					<?=form_input( $_field, set_value( $_field, $_default ), 'id="input-' . $_field . '" placeholder="' . $_placeholder . '"' )?>
					<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
				</div>
			</div>
			<?php

		endif;

		// --------------------------------------------------------------------------

		if ( APP_NATIVE_LOGIN_USING == 'USERNAME' || APP_NATIVE_LOGIN_USING == 'BOTH' ) :

			$_field			= 'username';
			$_name			= lang( 'form_label_username' );
			$_placeholder	= lang( 'auth_register_username_placeholder' );
			$_error			= form_error( $_field ) ? 'error' : NULL;
			$_default		= ! empty( $username ) ? $username : '';

			?>
			<div class="row <?=$_error?>">
				<?=form_label( $_name, 'input-' . $_field, array( 'class' => 'two columns first' ) ); ?>
				<div class="four columns">
					<?=form_input( $_field, set_value( $_field, $_default ), 'id="input-' . $_field . '" placeholder="' . $_placeholder . '"' )?>
					<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
				</div>
			</div>
			<?php

		endif;

		// --------------------------------------------------------------------------

		if ( ! $first_name || ! $last_name ) :

			$_field			= 'first_name';
			$_name			= lang( 'form_label_first_name' );
			$_placeholder	= lang( 'auth_register_first_name_placeholder' );
			$_error			= form_error( $_field ) ? 'error' : NULL

			?>
			<div class="row <?=$_error?>">
				<?=form_label( $_name, 'input-' . $_field, array( 'class' => 'two columns first' ) ); ?>
				<div class="four columns">
					<?=form_input( $_field, set_value( $_field, $first_name ), 'id="input-' . $_field . '" placeholder="' . $_placeholder . '"' )?>
					<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
				</div>
			</div>
			<?php

			// --------------------------------------------------------------------------

			$_field			= 'last_name';
			$_name			= lang( 'form_label_last_name' );
			$_placeholder	= lang( 'auth_register_last_name_placeholder' );
			$_error			= form_error( $_field ) ? 'error' : NULL

			?>
			<div class="row <?=$_error?>">
				<?=form_label( $_name, 'input-' . $_field, array( 'class' => 'two columns first' ) ); ?>
				<div class="four columns">
					<?=form_input( $_field, set_value( $_field, $last_name ), 'id="input-' . $_field . '" placeholder="' . $_placeholder . '"' )?>
					<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
				</div>
			</div>
			<?php

		endif;

	?>

	<div class="row button-row">
		<label class="two columns first">&nbsp;</label>
		<div class="four columns last">
			<?=form_submit( 'submit', lang( 'action_continue' ), 'class="awesome"' )?>
		</div>
	</div>

<?php

	// --------------------------------------------------------------------------

	//	Close the form
	echo form_close();