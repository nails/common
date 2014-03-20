<?php

	if ( $return_to || $return_to_fail ) :

		$_returns = '?';
		$_returns .= $return_to ? 'return_to=' . urlencode( $return_to ) : '';
		$_returns .= $return_to_fail ? '&return_to_fail=' . urlencode( $return_to_fail ) : '';

	else :

		$_returns = '';

	endif;

	// --------------------------------------------------------------------------

	//	Write the HTML for the register form
?>
<div class="row">
	<div class="well well-lg col-sm-6 col-sm-offset-3">
		<?=form_open( 'auth/' . $this->uri->segment( 2 ) . '/connect/verify' . $_returns, 'class="form form-horizontal"'  )?>
		<p>
			<?=lang( 'auth_register_extra_message' )?>
		</p>
		<?php

			if ( APP_NATIVE_LOGIN_USING == 'EMAIL' || APP_NATIVE_LOGIN_USING == 'BOTH' ) :


				$_field			= 'email';
				$_label			= lang( 'form_label_email' );
				$_placeholder	= lang( 'auth_register_email_placeholder' );
				$_default		= ! empty( $email ) ? $email : '';

				?>
				<div class="form-group <?=form_error( $_field ) ? 'has-error' : ''?>">
					<label class="col-sm-3 control-label" for="input-<?=$_field?>"><?=$_label?></label>
					<div class="col-sm-9">
						<?=form_input( $_field, set_value( $_field, $_default ), 'id="input-<?=$_field?>" placeholder="' . $_placeholder . '" class="form-control "' )?>
						<?=form_error( $_field, '<p class="help-block">', '</p>' )?>
					</div>
				</div>
				<?php

			endif;

			// --------------------------------------------------------------------------

			if ( APP_NATIVE_LOGIN_USING == 'USERNAME' || APP_NATIVE_LOGIN_USING == 'BOTH' ) :

				$_field			= 'username';
				$_label			= lang( 'form_label_username' );
				$_placeholder	= lang( 'auth_register_username_placeholder' );
				$_default		= ! empty( $username ) ? $username : '';

				?>
				<div class="form-group <?=form_error( $_field ) ? 'has-error' : ''?>">
					<label class="col-sm-3 control-label" for="input-<?=$_field?>"><?=$_label?></label>
					<div class="col-sm-9">
						<?=form_input( $_field, set_value( $_field, $_default ), 'id="input-<?=$_field?>" placeholder="' . $_placeholder . '" class="form-control "' )?>
						<?=form_error( $_field, '<p class="help-block">', '</p>' )?>
					</div>
				</div>
				<?php

			endif;

			// --------------------------------------------------------------------------

			if ( ! $first_name || ! $last_name ) :

				$_field			= 'first_name';
				$_label			= lang( 'form_label_first_name' );
				$_placeholder	= lang( 'auth_register_first_name_placeholder' );
				$_default		= ! empty( $first_name ) ? $first_name : '';

				?>
				<div class="form-group <?=form_error( $_field ) ? 'has-error' : ''?>">
					<label class="col-sm-3 control-label" for="input-<?=$_field?>"><?=$_label?></label>
					<div class="col-sm-9">
						<?=form_input( $_field, set_value( $_field, $_default ), 'id="input-<?=$_field?>" placeholder="' . $_placeholder . '" class="form-control "' )?>
						<?=form_error( $_field, '<p class="help-block">', '</p>' )?>
					</div>
				</div>
				<?php

				// --------------------------------------------------------------------------

				$_field			= 'last_name';
				$_label			= lang( 'form_label_last_name' );
				$_placeholder	= lang( 'auth_register_last_name_placeholder' );
				$_default		= ! empty( $last_name ) ? $last_name : '';

				?>
				<div class="form-group <?=form_error( $_field ) ? 'has-error' : ''?>">
					<label class="col-sm-3 control-label" for="input-<?=$_field?>"><?=$_label?></label>
					<div class="col-sm-9">
						<?=form_input( $_field, set_value( $_field, $_default ), 'id="input-<?=$_field?>" placeholder="' . $_placeholder . '" class="form-control "' )?>
						<?=form_error( $_field, '<p class="help-block">', '</p>' )?>
					</div>
				</div>
				<?php

			endif;

		?>
		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
				<button type="submit" class="btn btn-primary"><?=lang( 'action_continue' )?></button>
			</div>
		</div>
		<?=form_close()?>
	</div>
</div>