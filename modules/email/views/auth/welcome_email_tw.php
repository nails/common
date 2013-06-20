<p>
	Thank you for registering at <?=APP_NAME?>'s website.
</p>
<p>
	This email confirms that you used your Twitter account to register.
</p>
<p>
	We would appreciate it if you could take a second to verify your email address using the link below, we do this to ensure the integrity of our database.
</p>
<p>
	<?=anchor( 'auth/activate/' . $user->id . '/' . $user->activation_code, 'Verify Email', 'class="large"' )?>
</p>