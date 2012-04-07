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
	
	<p>
		An activation email with a link to activate your account has been sent to:
	</p>
	<p class="alert alert-info">
		<strong><?=$email?></strong>
	</p>
	
	<hr>
	
	<h3>What to do next</h2>
	<p>
		Check your e-mail (including spam folders) and click on the link to activate your account!
		It can take up to 60 seconds to receive your activation e-mail. If you have not received
		it, use the link below.
	</p>
	
	<br>
	
	<h3>Help! I Didn't Receive an E-mail</h3>
	<p>
		If you haven't received your activation e-mail after a few moments, you can
		<?=anchor( 'auth/register/resend/' . $user_id . '/' . md5( $hash ), 'send it again' )?>.
	</p>
	
<?php
