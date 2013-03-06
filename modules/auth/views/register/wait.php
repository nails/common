<?php

	/**
	 *	THE ACTIVATION EMAIL RESENT PAGE
	 *	
	 *	This view contains only the basic page shown when the activation email is resent. The controller
	 *	will look for an app version of the file  first and load that up. It will fall back
	 *	to the empty Nails view if not available (which includes some basic styling so
	 *	as not to look totally rubbish).
	 *	
	 *	You can completely overload this view by creating a view at:
	 *	
	 *	application/views/auth/register/resend
	 *	
	 **/
	
	
	// --------------------------------------------------------------------------
	
	//	Write the HTML for the page
?>
	
	<div class="container">
		<p>
			<?=lang( 'auth_register_wait_message' )?>
		</p>
		<p class="alert alert-info">
			<strong><?=$email?></strong>
		</p>
		
		<hr />
		
		<h3><?=lang( 'auth_register_wait_next_title' )?></h2>
		<p>
			<?=lang( 'auth_register_wait_next_message' )?>
		</p>
		
		<hr />
		
		<h3><?=lang( 'auth_register_wait_help_title' )?></h3>
		<p>
			<?=lang( 'auth_register_wait_help_message', site_url( 'auth/register/resend/' . $user_id . '/' . md5( $hash ) ) )?>.
		</p>
	</div>
	
<?php
