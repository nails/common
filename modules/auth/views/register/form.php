<div class="row">
	<div class="well well-lg col-sm-6 col-sm-offset-3">
		<!--	SOCIAL NETWORK BUTTONS	-->
		<?php

			if ( module_is_enabled( 'auth[facebook]' ) || module_is_enabled( 'auth[facebook]' ) || module_is_enabled( 'auth[facebook]' ) ) :

				echo '<p class="text-center" style="margin:1em 0 2em 0;">';
					echo 'Register using your preferred social network.';
				echo '</p>';

				echo '<div class="row" style="margin-top:1em;">';

					//	This is technically not needed for the default group, but left here by
					//	way of an example

					$_token				= array();
					$_token['nonce']	= time();
					$_token['ip']		= $this->input->ip_address();
					$_token['group']	= APP_USER_DEFAULT_GROUP;

					$_token = urlencode( $this->encrypt->encode( serialize($_token) . '|' . $_token['ip'] . '|' . $_token['nonce'], APP_PRIVATE_KEY ) );

					$_buttons = array();

					//	FACEBOOK
					if ( module_is_enabled( 'auth[facebook]' ) ) :

						$_buttons[] = array( 'auth/fb/connect?token=' . $_token, 'Facebook' );

					endif;

					//	TWITTER
					if ( module_is_enabled( 'auth[twitter]' ) ) :

						$_buttons[] = array( 'auth/tw/connect?token=' . $_token, 'Twitter' );

					endif;

					//	LINKEDIN
					if ( module_is_enabled( 'auth[linkedin]' ) ) :

						$_buttons[] = array( 'auth/li/connect?token=' . $_token, 'LinkedIn' );

					endif;

					// --------------------------------------------------------------------------

					//	Render the buttons
					$_cols_each = floor( 12 / count( $_buttons ) );

					foreach ( $_buttons AS $btn ) :

						$_class = $_cols_each == 4 ? 'md' : 'sm';

						echo '<div class="col-' . $_class . '-' . $_cols_each . ' text-center" style="margin-bottom:1em;">';
							echo anchor( $btn[0], $btn[1], 'class="btn btn-primary btn-lg btn-block"' );
						echo '</div>';

					endforeach;

				echo '</div>';

				echo '<hr />';

				echo '<p class="text-center" style="margin:1em 0 2em 0;">';
					switch ( APP_NATIVE_LOGIN_USING ) :

						case 'EMAIL' :

							echo 'Or register using your email address.';

						break;

						case 'USERNAME' :

							echo 'Or register using a username.';

						break;

						case 'BOTH' :
						default :

							echo 'Or register using your email address and username.';

						break;

					endswitch;
				echo '</p>';

			endif;

			// --------------------------------------------------------------------------

			echo form_open( site_url( 'auth/register' ), 'class="form form-horizontal"' );
			echo form_hidden( 'registerme', TRUE );

			// --------------------------------------------------------------------------

			if ( APP_NATIVE_LOGIN_USING == 'EMAIL' || APP_NATIVE_LOGIN_USING == 'BOTH' ) :

				$_field			= 'email';
				$_label			= lang( 'form_label_email' );
				$_placeholder	= lang( 'auth_register_email_placeholder' );

				?>
				<div class="form-group <?=form_error( $_field ) ? 'has-error' : ''?>">
					<label class="col-sm-3 control-label" for="input-<?=$_field?>"><?=$_label?></label>
					<div class="col-sm-9">
						<?=form_input( $_field, set_value( $_field ), 'id="input-<?=$_field?>" placeholder="' . $_placeholder . '" class="form-control "' )?>
						<?=form_error( $_field, '<p class="help-block">', '</p>' )?>
					</div>
				</div>
				<?php

			endif;

			if ( APP_NATIVE_LOGIN_USING == 'USERNAME' || APP_NATIVE_LOGIN_USING == 'BOTH' ) :

				$_field			= 'username';
				$_label			= lang( 'form_label_username' );
				$_placeholder	= lang( 'auth_register_username_placeholder' );

				?>
				<div class="form-group <?=form_error( $_field ) ? 'has-error' : ''?>">
					<label class="col-sm-3 control-label" for="input-<?=$_field?>"><?=$_label?></label>
					<div class="col-sm-9">
						<?=form_input( $_field, set_value( $_field ), 'id="input-<?=$_field?>" placeholder="' . $_placeholder . '" class="form-control "' )?>
						<?=form_error( $_field, '<p class="help-block">', '</p>' )?>
					</div>
				</div>
				<?php

			endif;

		// --------------------------------------------------------------------------


		$_field			= 'password';
		$_label			= lang( 'form_label_password' );
		$_placeholder	= lang( 'auth_register_password_placeholder' );

		?>
		<div class="form-group <?=form_error( $_field ) ? 'has-error' : ''?>">
			<label class="col-sm-3 control-label" for="input-<?=$_field?>"><?=$_label?></label>
			<div class="col-sm-9">
				<?=form_password( $_field, set_value( $_field ), 'id="input-<?=$_field?>" placeholder="' . $_placeholder . '" class="form-control "' )?>
				<?=form_error( $_field, '<p class="help-block">', '</p>' )?>
			</div>
		</div>
		<?php


		$_field			= 'first_name';
		$_label			= lang( 'form_label_first_name' );
		$_placeholder	= lang( 'auth_register_first_name_placeholder' );

		?>
		<div class="form-group <?=form_error( $_field ) ? 'has-error' : ''?>">
			<label class="col-sm-3 control-label" for="input-<?=$_field?>"><?=$_label?></label>
			<div class="col-sm-9">
				<?=form_input( $_field, set_value( $_field ), 'id="input-<?=$_field?>" placeholder="' . $_placeholder . '" class="form-control "' )?>
				<?=form_error( $_field, '<p class="help-block">', '</p>' )?>
			</div>
		</div>
		<?php


		$_field			= 'last_name';
		$_label			= lang( 'form_label_last_name' );
		$_placeholder	= lang( 'auth_register_last_name_placeholder' );

		?>
		<div class="form-group <?=form_error( $_field ) ? 'has-error' : ''?>">
			<label class="col-sm-3 control-label" for="input-<?=$_field?>"><?=$_label?></label>
			<div class="col-sm-9">
				<?=form_input( $_field, set_value( $_field ), 'id="input-<?=$_field?>" placeholder="' . $_placeholder . '" class="form-control "' )?>
				<?=form_error( $_field, '<p class="help-block">', '</p>' )?>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
				<button type="submit" class="btn btn-primary"><?=lang( 'action_register' )?></button>
			</div>
		</div>
		<hr />
		<p class="text-center">
			Already got an account? <?=anchor( 'auth/login', 'Sign in now' )?>.
		</p>
	</div>
</div>
<?php

	// --------------------------------------------------------------------------

	//	Close the form
	echo form_close();