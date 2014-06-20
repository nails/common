<p>
	This email address has just been added to your account,
	we would appreciate it if you could take a second to verify
	it using the link below, we do this to ensure the integrity
	of our database.
</p>
<p>
	<?=anchor( 'email/verify/' . $user_id . '/' . $code, 'Verify Email' )?>
</p>