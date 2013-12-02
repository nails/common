<p>
	Your password has been changed, if you made this request you can safely ignore this email.
</p>
<p>
	The request was made at <?=user_datetime( $updated_at )?><?=! empty( $updated_by['id'] ) && $updated_by['id'] != $sent_to->id ? ' by <strong>' . $updated_by['name'] . '</strong>': ''?>.
</p>
<p>
	If it was not you who made this change, or you didn't request it, please <strong>immediately</strong>
	<?=anchor( 'auth/forgotten_password?email=' . urlencode( $sent_to->email ), 'reset your password' )?> using the forgotten password facility and
	please let us know of any fraudulent activity on your account.
</p>