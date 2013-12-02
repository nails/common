Your password has been changed, if you made this request you can safely ignore this email.

The request was made at <?=user_datetime( $updated_at )?><?=! empty( $updated_by['id'] ) && $updated_by['id'] != $sent_to->id ? ' by ' . strtoupper( $updated_by['name'] ): ''?>.

If it was not you who made this change, or you didn't request it, please IMMEDIATELY reset your password using the forgotten password facility (link below) and please let us know of any fraudulent activity on your account.

{unwrap}<?=site_url( 'auth/forgotten_password?email=' . urlencode( $sent_to->email ) )?>{/unwrap}