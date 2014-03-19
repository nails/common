<p>
	<?=lang( 'auth_twofactor_question_set_body' )?>
</p>
<?php

	$_login_method	= $login_method && $login_method != 'native' ? '/' . $login_method : '';

	$_query					= array();
	$_query['return_to']	= $return_to;
	$_query['remember']		= $remember;

	$_query = array_filter( $_query );

	if ( $_query ) :

		$_query = '?' . http_build_query( $_query );

	else :

		$_query = '';

	endif;

	echo form_open( 'auth/security_questions/' . $user_id . '/' . $token['salt'] . '/' . $token['token'] . $_login_method . $_query );


		if ( $num_questions ) :

			echo '<p>';
				echo lang( 'auth_twofactor_question_set_system_body' );
			echo '</p>';

			echo '<fieldset>';
				echo $num_custom_questions ? '<legend>' . lang( 'auth_twofactor_question_set_system_legend' ) . '</legend>' : '';

			for ( $i = 0; $i < $num_questions; $i++ ) :

				echo '<fieldset>';

					$_field				= array();
					$_field['key']		= 'question[' . $i . '][question]';
					$_field['label']	= 'Question';
					$_field['class']	= 'chosen';
					$_field['required']	= TRUE;

					$_options  = array_merge( array( 'Please Choose...' ), $questions );

					echo form_field_dropdown( $_field, $_options );

					$_field					= array();
					$_field['key']			= 'question[' . $i . '][answer]';
					$_field['label']		= 'Answer';
					$_field['placeholder']	= 'Type your answer here';
					$_field['required']		= TRUE;

					echo form_field( $_field );

				echo '</fieldset>';

			endfor;

			echo '</fieldset>';

		endif;

		// --------------------------------------------------------------------------

		if ( $num_custom_questions ) :

			echo '<p>';
				echo lang( 'auth_twofactor_question_set_custom_body' );
			echo '</p>';

			echo '<fieldset>';
				echo $num_questions ? '<legend>' . lang( 'auth_twofactor_question_set_custom_legend' ) . '</legend>' : '';

				for ( $i = 0; $i < $num_custom_questions; $i++ ) :

					echo '<fieldset>';

						$_field					= array();
						$_field['key']			= 'custom_question[' . $i . '][question]';
						$_field['label']		= 'Question';
						$_field['class']		= 'chosen';
						$_field['placeholder']	= 'Type your security question here';
						$_field['required']		= TRUE;

						echo form_field( $_field );

						$_field					= array();
						$_field['key']			= 'custom_question[' . $i . '][answer]';
						$_field['label']		= 'Answer';
						$_field['placeholder']	= 'Type your answer here';
						$_field['required']		= TRUE;

						echo form_field( $_field );

					echo '</fieldset>';

				endfor;

			echo '</fieldset>';

		endif;

	?>
	<?=form_submit( 'submit', lang( 'action_continue') )?>
<?=form_close()?>