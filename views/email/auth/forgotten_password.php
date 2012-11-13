<p>
	A request was made at <?=anchor( '', APP_NAME )?> to reset the password for <strong><?=$sent_to->email?></strong>.
</p>
<p>
	If you made this request and wish to create a new password, click on the link below
	and follow the instructions on screen. Please note, this link expires in 24 hours.
</p>
<p>
	<a href="<?=site_url( 'auth/forgotten_password/' . $forgotten_password_code )?>" class="large">Reset your Password</a>
</p>
<p>
	If you did not make this request, please disregard this email - your password will remain the same.
</p>