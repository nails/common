<?php

	$_query					= array();
	$_query['return_to']	= $return_to;
	$_query['remember']		= $remember;

	$_query = array_filter( $_query );

?>
<div class="row">
	<div class="well well-lg <?=BS_COL_SM_6?> <?=BS_COL_SM_OFFSET_3?>">
		<p>
			Please set security questions for your account.
		</p>
		<?php

			$_query					= array();
			$_query['return_to']	= $return_to;
			$_query['remember']		= $remember;

			$_query = array_filter( $_query );

			if ( $_query ) :

				$_query = '?' . http_build_query( $_query );

			else :

				$_query = '';

			endif;

			echo form_open( 'auth/security_questions/' . $user_id . '/' . $token['salt'] . '/' . $token['token'] . $_query );

				if ( $num_questions ) :

					echo '<p>';
						echo lang( 'auth_twofactor_question_set_system_body' );
					echo '</p>';

					echo '<fieldset>';
						echo $num_custom_questions ? '<legend style="padding-top:20px">' . lang( 'auth_twofactor_question_set_system_legend' ) . '</legend>' : '';

					for ( $i = 0; $i < $num_questions; $i++ ) :

						$_field			= 'question[' . $i . '][question]';
						$_name			= 'Question ' . ( $i + 1 );
						$_error			= form_error( $_field ) ? 'has-error' : NULL;
						$_options  		= array_merge( array( 'Please Choose...' ), $questions );

						echo '<br>';
						echo '<div class="' . $_error . '">';
							echo '<label for="password">' . $_name . '</label>';
							echo form_dropdown( $_field, $_options, set_value( $_field ), 'class="form-control"' );
							echo form_error( $_field, '<span class="help-block">', '</span>' );
						echo '</div>';

						// --------------------------------------------------------------------------

						$_field			= 'question[' . $i . '][answer]';
						$_name			= 'Answer ' . ( $i + 1 );
						$_error			= form_error( $_field ) ? 'has-error' : NULL;
						$_placeholder	= 'Type your answer here';
						$_options  		= array_merge( array( 'Please Choose...' ), $questions );

						echo '<br>';
						echo '<div class="' . $_error . '">';
							echo '<label for="password">' . $_name . '</label>';
							echo form_input( $_field, set_value( $_field ), 'autocomplete="off" class="form-control" placeholder="' . $_placeholder . '"' );
							echo form_error( $_field, '<span class="help-block">', '</span>' );
						echo '</div>';

						echo '<hr />';

					endfor;

					echo '</fieldset>';

				endif;

				// --------------------------------------------------------------------------

				if ( $num_custom_questions ) :

					echo '<p>';
						echo lang( 'auth_twofactor_question_set_custom_body' );
					echo '</p>';

					echo '<fieldset>';
						echo $num_questions ? '<legend style="padding-top:20px">' . lang( 'auth_twofactor_question_set_custom_legend' ) . '</legend>' : '';

						for ( $i = 0; $i < $num_custom_questions; $i++ ) :

							$_field			= 'custom_question[' . $i . '][answer]';
							$_name			= 'Answer ' . ( $i + 1 );
							$_error			= form_error( $_field ) ? 'has-error' : NULL;
							$_placeholder	= 'Type your question here';

							echo '<br>';
							echo '<div class="' . $_error . '">';
								echo '<label for="password">' . $_name . '</label>';
								echo form_input( $_field, set_value( $_field ), 'autocomplete="off" class="form-control" placeholder="' . $_placeholder . '"' );
								echo form_error( $_field, '<span class="help-block">', '</span>' );
							echo '</div>';

							// --------------------------------------------------------------------------

							$_field			= 'custom_question[' . $i . '][answer]';
							$_name			= 'Answer ' . ( $i + 1 );
							$_error			= form_error( $_field ) ? 'has-error' : NULL;
							$_placeholder	= 'Type your answer here';
							$_options  		= array_merge( array( 'Please Choose...' ), $questions );

							echo '<br>';
							echo '<div class="' . $_error . '">';
								echo '<label for="password">' . $_name . '</label>';
								echo form_input( $_field, set_value( $_field ), 'autocomplete="off" class="form-control" placeholder="' . $_placeholder . '"' );
								echo form_error( $_field, '<span class="help-block">', '</span>' );
							echo '</div>';

							echo '<hr />';

						endfor;

					echo '</fieldset>';

				endif;

			?>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Set Questions &amp; Sign In</button>
		<?=form_close()?>
	</div>
</div>