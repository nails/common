<?php

	/**
	 *	THE ACTIVATION FAILED PAGE
	 *	
	 *	This view contains the information show to a user whos activation failed. The controller
	 *	will look for an app version of the file first and load that up. It will fall back
	 *	to the empty Nails view if not available (which includes some basic styling so as not
	 *	to look totally rubbish).
	 *	
	 *	You can completely overload this view by creating a view at:
	 *	
	 *	application/views/auth/activate/fail
	 *	
	 **/
	 
	
	// --------------------------------------------------------------------------
	
	//	Write the HTML for the activation failed page
?>
	
	
	<p class="system-alert error">
		<strong>Oh snap!</strong> We couldn't activate your account.
	</p>
	<p>
		There was a problem activating this account. This can happen for various reasons:
	</p>
	<ul>
		<li>Account already active.</li>
		<li>Invalid or expired activation code</li>
		<?php if ( ! $user->is_logged_in() ) : ?>
		<li>In many cases your account has already been activated, <?=anchor( 'auth/login', 'please try logging in' )?>.</li>
		<?php endif; ?>
	</ul>
	
	
<?php
