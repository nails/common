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
	
	<p class="alert alert-success">
		An email with a link to verify your email has been re-sent to:
		<strong><?=$email?></strong>
	</p>
	
	<h3>What to do next</h3>
	<p>
		Check your email (including spam folders) and click on the link to verify your email address.
		It can sometimes take a while to receive your verification email.
	</p>
	
<?php
