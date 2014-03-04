<p>
	<?=lang( 'auth_twofactor_answer_body' )?>
</p>
<p>
	<strong><?=$question->question?></strong>
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

	$_return_to		= $return_to ? '?return_to=' . $return_to : '';
	echo form_open( site_url() . 'auth/security_questions/' . $user_id . '/' . $token['salt'] . '/' . $token['token'] . $_login_method . $_query );

?>
	<p>
		<?=form_password( 'answer', NULL, 'placeholder="Type your answer here"' )?>
	</p>
	<?=form_submit( 'submit', lang( 'action_continue' ) )?>
<?=form_close()?>