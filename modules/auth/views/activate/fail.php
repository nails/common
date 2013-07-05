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
	
	<div id="nails-activation-failed" class="container">
		<p>
			<?=lang( 'auth_verify_fail_message' )?>
		</p>
		<ul>
			<li><?=lang( 'auth_verify_fail_reason_active' )?></li>
			<li><?=lang( 'auth_verify_fail_reason_invalid' )?></li>
		</ul>
		<?php if ( ! $user->is_logged_in() ) : ?>
		<p>
			<?=lang( 'auth_verify_fail_try_login', site_url( 'auth/login' ) )?>
		</p>
		<?php endif; ?>
	</div>
	
<?php
