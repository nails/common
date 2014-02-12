A request was made at <?=APP_NAME?> to reset the password for <?=$identifier?>.

If you made this request and wish to create a new password, copy the link below into your browser and
follow the instructions on screen. Please note, this link expires in 24 hours.

{unwrap}<?=site_url( 'auth/forgotten_password/' . $forgotten_password_code )?>{/unwrap}

If you did not make this request, please disregard this email - your password will remain the same.