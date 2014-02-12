<p>
	Your password has been changed, if you made this request you can safely ignore this email.
</p>
<p>
<?php

	echo 'The request was made at ' . user_datetime( $updated_at );
	echo ! empty( $updated_by['id'] ) && $updated_by['id'] != $sent_to->id ? ' by <strong>' . $updated_by['name'] . '</strong>': '';
	echo ! empty( $ip_address ) ? ' from IP address ' . $ip_address: '';
	echo '.';
?>
</p>
<?php

	switch( APP_NATIVE_LOGIN_USING ) :

		case 'EMAIL' :

			$_identifier = $sent_to->email;

		break;

		// --------------------------------------------------------------------------

		case 'USERNAME' :

			$_identifier = $sent_to->username;

		break;

		// --------------------------------------------------------------------------

		case 'BOTH' :
		default :

			$_identifier = $sent_to->email;

		break;

	endswitch;

?>
<p>
	If it was not you who made this change, or you didn't request it, please <strong>immediately</strong>
	<?=anchor( 'auth/forgotten_password?identifier=' . urlencode( $_identifier ), 'reset your password' )?> using the forgotten password facility and
	please let us know of any fraudulent activity on your account.
</p>