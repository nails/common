<div class="row">
	<div class="well well-lg <?=BS_COL_SM_6?> <?=BS_COL_SM_OFFSET_3?>">
		<?=form_open( 'auth/forgotten_password', 'class="form form-horizontal"' )?>

			<p>
				<?=lang( 'auth_forgot_message' )?>
			</p>

			<hr />

			<?php

				switch ( APP_NATIVE_LOGIN_USING ) :

					case 'EMAIL' :

						$_label			= lang( 'form_label_email' );
						$_placeholder	= lang( 'auth_forgot_email_placeholder' );
						$_input_type	= 'form_email';

					break;

					case 'USERNAME' :

						$_label			= lang( 'form_label_username' );
						$_placeholder	= lang( 'auth_forgot_username_placeholder' );
						$_input_type	= 'form_input';

					break;

					case 'BOTH' :
					default :

						$_label			= lang( 'auth_forgot_both' );
						$_placeholder	= lang( 'auth_forgot_both_placeholder' );
						$_input_type	= 'form_input';

					break;

				endswitch;

				$_field			= 'identifier';
				$_error			= form_error( $_field ) ? 'error' : NULL

			?>
			<div class="form-group <?=form_error( $_field ) ? 'has-error' : ''?>">
				<label class="<?=BS_COL_SM_3?> control-label" for="input-email"><?=$_label?>: </label>
				<div class="<?=BS_COL_SM_9?>">
					<?=$_input_type( $_field, set_value( $_field, $this->input->get( 'email' ) ), 'id="input-email" placeholder="' . $_placeholder . '" class="form-control "' )?>
					<?=form_error( $_field, '<p class="help-block">', '</p>' )?>
				</div>
			</div>

			<div class="form-group">
				<div class="<?=BS_COL_SM_OFFSET_3?> <?=BS_COL_SM_9?>">
					<button type="submit" class="btn btn-primary"><?=lang( 'auth_forgot_action_reset' )?></button>
				</div>
			</div>


		<?=form_close()?>
	</div>
</div>