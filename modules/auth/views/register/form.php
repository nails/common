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

	echo form_open( 'auth/register', $attr );

	// --------------------------------------------------------------------------

	//	Write the HTML for the register form
?>

	<div class="row">

		<!--	FORCE REGISTER CHECK	-->
		<?=form_hidden( 'registerme', TRUE )?>


		<!--	INPUT FIELDS	-->
		<div class="first seven columns">

			<p>
				<?=lang( 'auth_register_message', APP_NAME )?>
			</p>

			<?php

				$_field			= 'first_name';
				$_name			= lang( 'form_label_first_name' );
				$_placeholder	= lang( 'auth_register_first_name_placeholder' );
				$_error			= form_error( $_field ) ? 'error' : NULL

			?>
			<div class="row <?=$_error?>">
				<?=form_label( $_name, 'input-' . $_field, array( 'class' => 'two columns first' ) ); ?>
				<div class="four columns last">
					<?=form_input( $_field, set_value( $_field ), 'id="input-' . $_field . '" placeholder="' . $_placeholder . '"' )?>
					<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
				</div>
			</div>

			<?php

				$_field			= 'last_name';
				$_name			= lang( 'form_label_last_name' );
				$_placeholder	= lang( 'auth_register_last_name_placeholder' );
				$_error			= form_error( $_field ) ? 'error' : NULL

			?>
			<div class="row <?=$_error?>">
				<?=form_label( $_name, 'input-' . $_field, array( 'class' => 'two columns first' ) ); ?>
				<div class="four columns last">
					<?=form_input( $_field, set_value( $_field ), 'id="input-' . $_field . '" placeholder="' . $_placeholder . '"' )?>
					<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
				</div>
			</div>

			<?php

				if ( APP_NATIVE_LOGIN_USING == 'EMAIL' || APP_NATIVE_LOGIN_USING == 'BOTH' ) :

					$_field			= 'email';
					$_name			= lang( 'form_label_email' );
					$_placeholder	= lang( 'auth_register_email_placeholder' );
					$_error			= form_error( $_field ) ? 'error' : NULL

					?>
					<div class="row <?=$_error?>">
						<?=form_label( $_name, 'input-' . $_field, array( 'class' => 'two columns first' ) ); ?>
						<div class="four columns last">
							<?=form_input( $_field, set_value( $_field ), 'id="input-' . $_field . '" placeholder="' . $_placeholder . '"' )?>
							<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
						</div>
					</div>
					<?php

				endif;

				if ( APP_NATIVE_LOGIN_USING == 'USERNAME' || APP_NATIVE_LOGIN_USING == 'BOTH' ) :

					$_field			= 'username';
					$_name			= lang( 'form_label_username' );
					$_placeholder	= lang( 'auth_register_username_placeholder' );
					$_error			= form_error( $_field ) ? 'error' : NULL

					?>
					<div class="row <?=$_error?>">
						<?=form_label( $_name, 'input-' . $_field, array( 'class' => 'two columns first' ) ); ?>
						<div class="four columns last">
							<?=form_input( $_field, set_value( $_field ), 'id="input-' . $_field . '" placeholder="' . $_placeholder . '"' )?>
							<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
						</div>
					</div>
					<?php

				endif;

				$_field			= 'password';
				$_name			= lang( 'form_label_password' );
				$_placeholder	= lang( 'auth_register_password_placeholder' );
				$_error			= form_error( $_field ) ? 'error' : NULL

			?>
			<div class="row <?=$_error?>">
				<?=form_label( $_name, 'input-' . $_field, array( 'class' => 'two columns first' ) ); ?>
				<div class="four columns last">
					<?=form_password( $_field, NULL, 'id="input-' . $_field . '" placeholder="' . $_placeholder . '"' )?>
					<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
				</div>
			</div>

			<!--	ACCEPT T&C's	-->
			<?php

				$_field	= 'terms';
				$_name	= lang ( 'auth_register_label_accept_tc', site_url( 'legal/terms' ) );
				$_error = form_error( $_field ) ? 'error' : NULL

			?>
			<div class="row <?=$_error?>">
				<label class="two columns first">&nbsp;</label>
				<div class="four columns last">
					<label class="checkbox">
						<?=form_checkbox( $_field, TRUE, set_checkbox( $_field, TRUE, FALSE ) )?>
						<?=$_name?>
						<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
					</label>
				</div>
			</div>

			<!--	SUBMIT BUTTON	-->
			<div class="row button-row">
				<label class="two columns first">&nbsp;</label>
				<div class="four columns last">
					<?=form_submit( 'submit', lang( 'action_register' ), 'class="awesome"' )?>
				</div>
			</div>

		</div>

		<!--	SOCIAL NETWORK BUTTONS	-->
		<?php

			if ( module_is_enabled( 'auth[facebook]' ) || module_is_enabled( 'auth[facebook]' ) || module_is_enabled( 'auth[facebook]' ) ) :

				echo '<div class="eight columns last offset-by-one">';
				echo '<p style="text-align:center;">' . lang( 'auth_register_social_message' ) . '</p>';

				// --------------------------------------------------------------------------

				//	This is technically not needed for the default group, but left here by
				//	way of an example

				$_token				= array();
				$_token['nonce']	= time();
				$_token['ip']		= $this->input->ip_address();
				$_token['group']	= APP_USER_DEFAULT_GROUP;

				$_token = urlencode( $this->encrypt->encode( serialize($_token) . '|' . $_token['ip'] . '|' . $_token['nonce'], APP_PRIVATE_KEY ) );

				//	FACEBOOK
				if ( module_is_enabled( 'auth[facebook]' ) ) :

					echo '<p style="text-align:center;">' . anchor( 'auth/fb/connect?token=' . $_token, lang( 'auth_register_social_register', 'Facebook' ), 'class="social-signin fb"' ) . '</p>';

				endif;

				//	TWITTER
				if ( module_is_enabled( 'auth[twitter]' ) ) :

					echo '<p style="text-align:center;">' . anchor( 'auth/tw/connect?token=' . $_token, lang( 'auth_register_social_register', 'Twitter' ), 'class="social-signin tw"' ) . '</p>';

				endif;

				//	LINKEDIN
				if ( module_is_enabled( 'auth[linkedin]' ) ) :

					echo '<p style="text-align:center;">' . anchor( 'auth/li/connect?token=' . $_token, lang( 'auth_register_social_register', 'LinkedIn' ), 'class="social-signin li"' ) . '</p>';

				endif;

				echo '</div>';

			endif;

		?>

	</div>

<?php

	// --------------------------------------------------------------------------

	//	Close the form
	echo form_close();