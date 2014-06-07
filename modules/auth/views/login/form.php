<?php

	$_return_to = $return_to ? '?return_to=' . urlencode( $return_to ) : '';

?>
<div class="row ">
	<div class="well well-lg <?=BS_COL_SM_6?> <?=BS_COL_SM_OFFSET_3?>">
		<!--	SOCIAL NETWORK BUTTONS	-->
		<?php

			if ( app_setting( 'social_signin_enabled' ) ) :

				echo '<p class="text-center" style="margin:1em 0 2em 0;">';
					echo 'Sign in using your preferred social network.';
				echo '</p>';

				echo '<div class="row" style="margin-top:1em;">';

					$_buttons = array();

					//	FACEBOOK
					if ( app_setting( 'social_signin_fb_enabled' ) ) :

						$_buttons[] = array( 'auth/fb/connect' . $_return_to, 'Facebook' );

					endif;

					//	TWITTER
					if ( app_setting( 'social_signin_tw_enabled' ) ) :

						$_buttons[] = array( 'auth/tw/connect' . $_return_to, 'Twitter' );

					endif;

					//	LINKEDIN
					if ( app_setting( 'social_signin_li_enabled' ) ) :

						$_buttons[] = array( 'auth/li/connect' . $_return_to, 'LinkedIn' );

					endif;

					// --------------------------------------------------------------------------

					//	Render the buttons
					$_cols_each = floor( APP_BOOTSTRAP_GRID / count( $_buttons ) );

					foreach ( $_buttons AS $btn ) :

						$_class = $_cols_each == ( APP_BOOTSTRAP_GRID / 3 ) ? 'md' : 'sm';

						echo '<div class="col-' . $_class . '-' . $_cols_each . ' text-center" style="margin-bottom:1em;">';
							echo anchor( $btn[0], $btn[1], 'class="btn btn-primary btn-lg btn-block"' );
						echo '</div>';

					endforeach;

				echo '</div>';

				echo '<hr />';

				echo '<p class="text-center" style="margin:1em 0 2em 0;">';
					switch ( APP_NATIVE_LOGIN_USING ) :

						case 'EMAIL' :

							echo 'Or sign in using your email address and password.';

						break;

						case 'USERNAME' :

							echo 'Or sign in using your username and password.';

						break;

						case 'BOTH' :
						default :

							echo 'Or sign in using your email address or username and password.';

						break;

					endswitch;
				echo '</p>';

			endif;

		?>
		<?=form_open( site_url( 'auth/login' . $_return_to ), 'class="form form-horizontal"' )?>
			<?php

				switch ( APP_NATIVE_LOGIN_USING ) :

					case 'EMAIL' :

						$_label			= lang( 'form_label_email' );
						$_placeholder	= lang( 'auth_login_email_placeholder' );
						$_input_type	= 'form_email';

					break;

					case 'USERNAME' :

						$_label			= lang( 'form_label_username' );
						$_placeholder	= lang( 'auth_login_username_placeholder' );
						$_input_type	= 'form_input';

					break;

					case 'BOTH' :
					default :

						$_label			= lang( 'auth_login_both' );
						$_placeholder	= lang( 'auth_login_both_placeholder' );
						$_input_type	= 'form_input';

					break;

				endswitch;

				$_field	= 'identifier';
				$_error	= form_error( $_field ) ? 'error' : NULL

			?>
			<div class="form-group <?=form_error( $_field ) ? 'has-error' : ''?>">
				<label class="<?=BS_COL_SM_3?> control-label" for="input-<?=$_field?>"><?=$_label?></label>
				<div class="<?=BS_COL_SM_9?>">
					<?=$_input_type( $_field, set_value( $_field ), 'id="input-' . $_field . '" placeholder="' . $_placeholder . '" class="form-control "' )?>
					<?=form_error( $_field, '<p class="help-block">', '</p>' )?>
				</div>
			</div>
			<?php

				$_field			= 'password';
				$_label			= lang( 'form_label_password' );
				$_placeholder	= lang( 'auth_login_password_placeholder' );

			?>
			<div class="form-group <?=form_error( $_field ) ? 'has-error' : ''?>">
				<label class="<?=BS_COL_SM_3?> control-label" for="input-<?=$_field?>"><?=$_label?></label>
				<div class="<?=BS_COL_SM_9?>">
					<?=form_password( $_field, set_value( $_field ), 'id="input-' . $_field . '" placeholder="' . $_placeholder . '" class="form-control "' )?>
					<?=form_error( $_field, '<p class="help-block">', '</p>' )?>
				</div>
			</div>
			<div class="form-group">
				<div class="<?=BS_COL_SM_OFFSET_3?> <?=BS_COL_SM_9?>">
					<div class="checkbox">
						<label class="popover-hover" title="Keep your account secure" data-content="Uncheck this when using a shared computer.">
							<input type="checkbox" name="rememberme" <?=set_checkbox( 'rememberme' )?>> Remember me
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="<?=BS_COL_SM_OFFSET_3?> <?=BS_COL_SM_9?>">
					<button type="submit" class="btn btn-primary">Sign in</button>
					<?=anchor( 'auth/forgotten_password', 'Forgotten Your Password?', 'class="btn btn-default"' )?>
				</div>
			</div>
		<?=form_close()?>
		<?php if ( APP_USER_ALLOW_REGISTRATION ) : ?>
		<hr />
		<p class="text-center">
			Not got an account? <?=anchor( 'auth/register', 'Register now' )?>.
		</p>
		<?php endif; ?>
	</div>
</div>