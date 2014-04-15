<div class="row">
	<div class="well well-lg <?=BS_COL_SM_6?> <?=BS_COL_SM_OFFSET_3?>">
		<?php

			$_query = array();

			if ( $return_to ) :

				$_query['return_to'] = $return_to;

			endif;

			if ( $remember ) :

				$_query['remember'] = $remember;

			endif;

			$_query = $_query ? '?' . http_build_query( $_query ) : '';

			echo form_open( 'auth/reset_password/' . $auth->id . '/' . $auth->hash . $_query, 'class="form form-horizontal"' );


				$field			= 'new_password';
				$label			= lang( 'form_label_password' );
				$placeholder	= lang( 'auth_forgot_new_pass_placeholder' );

			?>
			<div class="form-group <?=form_error( $field ) ? 'has-error' : ''?>">
				<label class="<?=BS_COL_SM_3?> control-label" for="input-<?=$field?>"><?=$label?>:</label>
				<div class="<?=BS_COL_SM_9?>">
					<?=form_password( $field, set_value( $field ), 'id="input-<?=$field?>" placeholder="' . $placeholder . '" class="form-control "' )?>
					<?=form_error( $field, '<p class="help-block">', '</p>' )?>
				</div>
			</div>

			<?php

				$field			= 'confirm_pass';
				$label			= lang( 'form_label_password_confirm' );
				$placeholder	= lang( 'auth_forgot_new_pass_confirm_placeholder' );

			?>
			<div class="form-group <?=form_error( $field ) ? 'has-error' : ''?>">
				<label class="<?=BS_COL_SM_3?> control-label" for="input-<?=$field?>"><?=$label?>:</label>
				<div class="<?=BS_COL_SM_9?>">
					<?=form_password( $field, set_value( $field ), 'id="input-<?=$field?>" placeholder="' . $placeholder . '" class="form-control "' )?>
					<?=form_error( $field, '<p class="help-block">', '</p>' )?>
				</div>
			</div>

			<div class="form-group">
				<div class="<?=BS_COL_SM_OFFSET_3?> <?=BS_COL_SM_9?>">
					<button type="submit" class="btn btn-primary">
						<?=lang( 'auth_forgot_action_reset_continue' )?>
					</button>
				</div>
			</div>

		<?=form_close()?>
	</div>
</div>