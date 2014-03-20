<div class="row">
	<div class="well well-lg col-sm-6 col-sm-offset-3 text-center">
		<p>
			<?=lang( 'auth_twofactor_answer_body' )?>
		</p>
		<hr />
		<h4 style="margin-bottom:1.25em;">
			<strong><?=$question->question?></strong>
		</h4>
		<p>
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

			echo form_open( site_url() . 'auth/security_questions/' . $user_id . '/' . $token['salt'] . '/' . $token['token'] . $_login_method . $_query );

		?>
		</p>
		<p>
			<?=form_password( 'answer', NULL, 'class="form-control" placeholder="Type your answer here"' )?>
		</p>
		<hr />
		<button class="btn btn-lg btn-primary" type="submit">Login</button>
		<?=form_close()?>
	</div>
</div>