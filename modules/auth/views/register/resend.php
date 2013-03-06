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
		<p class="alert alert-success">
			<?=lang( 'auth_register_resend_message', $email )?>
		</p>
		
		<h3><?=lang( 'auth_register_resend_next_title' )?></h3>
		<p>
			<?=lang( 'auth_register_resend_next_message' )?>
		</p>
	</div>
	
<?php
