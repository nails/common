<?php

	/**
	 *	THE LOGIN FORM
	 *
	 *	This view contains only the basic form required for logging a user in. The controller
	 *	will look for an app version of the file  first and load that up. It will fall back
	 *	to the empty Nails view if not available (which includes some basic styling so
	 *	as not to look totally rubbish).
	 *
	 *	You can completely overload this view by creating a view at:
	 *
	 *	application/views/auth/login/form
	 *
	 **/

	// --------------------------------------------------------------------------

	/**
	 *	LOGIN ERRORS
	 *
	 *	Only individual field errors are shown, generic erros (such as too many attempted logins)
	 *	should be handled by the containing header files.
	 *
	 **/


	//	Form attributes
	$attr = array(

		'id'	=> 'login-form',
		'class'	=> 'container nails-default-form'

	);

	//	If there's a 'return_to' variable set it as a GET variable in case there's a form
	//	validation error. Otherwise don't show it - cleaner. Using site_url() as the return_to variable
	//	will most likely contain a full URL, causing form_open() not to site_url() it (resulting in the
	//	form posting to auth/login rather than /auth/login).

	echo $return_to? form_open( site_url( 'auth/login?return_to=' . $return_to ), $attr ) : form_open( 'auth/login', $attr );

	// --------------------------------------------------------------------------

	//	Write the HTML for the login form
?>

	<div class="row">

		<!--	LOGIN FIELDS	-->
		<div class="first seven columns">

			<p>
				<?php

					if ( defined( 'APP_USER_ALLOW_REGISTRATION' ) && APP_USER_ALLOW_REGISTRATION ) :

						echo lang( 'auth_login_message', array( APP_NAME, site_url( 'auth/register' ) ) );

					else :

						echo lang( 'auth_login_message_no_register', array( APP_NAME ) );

					endif;

				?>
			</p>

			<?php

				$_field			= 'email';
				$_name			= lang( 'form_label_email' );
				$_placeholder	= lang( 'auth_login_email_placeholder' );
				$_error			= form_error( $_field ) ? 'error' : NULL

			?>
			<div class="row <?=$_error?>">
				<?=form_label( $_name, 'input-' . $_field, array( 'class' => 'two columns first' ) ); ?>
				<div class="four columns">
					<?=form_input( $_field, set_value( $_field ), 'id="input-' . $_field . '" placeholder="' . $_placeholder . '"' )?>
					<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
				</div>
			</div>

			<?php

				$_field			= 'password';
				$_name			= lang( 'form_label_password' );
				$_placeholder	= lang( 'auth_login_pass_placeholder' );
				$_error			= form_error( $_field ) ? 'error' : NULL

			?>
			<div class="row <?=$_error?>">
				<?=form_label( $_name, 'input-' . $_field, array( 'class' => 'two columns first' ) ); ?>
				<div class="four columns">
					<?=form_password( $_field, NULL, 'id="input-' . $_field . '" placeholder="' . $_placeholder . '"' )?>
					<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
				</div>
			</div>

			<!--	REMEMBER ME CHECKBOX	-->
			<?php

				$_field	= 'remember';
				$_name	= lang( 'auth_login_label_remember_me' );
				$_error = form_error( $_field ) ? 'error' : NULL

			?>
			<div class="row">
				<label class="two columns first">&nbsp;</label>
				<div class="four columns last">
					<label class="checkbox">
						<?=form_checkbox( $_field, TRUE, TRUE )?>
						<?=$_name?>
					</label>
				</div>
			</div>


			<div class="row button-row">
				<label class="two columns first">&nbsp;</label>
				<div class="four columns last">
					<?=form_submit( 'submit', 'Log In', 'class="awesome"' )?>
					<small style="margin-left:15px;">
						<?=anchor( 'auth/forgotten_password', lang ('auth_login_forgot' ) )?>
					</small>
				</div>
			</div>

		</div>

		<!--	SOCIAL NETWORK BUTTONS	-->
		<?php

			if ( module_is_enabled( 'auth[facebook]' ) || module_is_enabled( 'auth[facebook]' ) || module_is_enabled( 'auth[facebook]' ) ) :

				echo '<div class="eight columns last offset-by-one">';
				echo '<p style="text-align:center;">' . lang( 'auth_login_social_message' ) . '</p>';

				// --------------------------------------------------------------------------

				//	FACEBOOK
				if ( module_is_enabled( 'auth[facebook]' ) ) :

					echo '<p style="text-align:center;">' . anchor( 'auth/fb/connect', lang( 'auth_login_social_signin', 'Facebook' ), 'class="social-signin fb"' ) . '</p>';

				endif;

				//	TWITTER
				if ( module_is_enabled( 'auth[twitter]' ) ) :

					echo '<p style="text-align:center;">' . anchor( 'auth/tw/connect', lang( 'auth_login_social_signin', 'Twitter' ), 'class="social-signin tw"' ) . '</p>';

				endif;

				//	LINKEDIN
				if ( module_is_enabled( 'auth[linkedin]' ) ) :

					echo '<p style="text-align:center;">' . anchor( 'auth/li/connect', lang( 'auth_login_social_signin', 'LinkedIn' ), 'class="social-signin li"' ) . '</p>';

				endif;

				echo '</div>';

			endif;

		?>
	</div>

<?php

	// --------------------------------------------------------------------------

	//	Close the form
	echo form_close();