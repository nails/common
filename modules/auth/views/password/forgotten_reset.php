<?php

	/**
	 *	THE PASSWORD RESET PAGE
	 *
	 *	This view contains only the basic form required for showing the reset password. The controller
	 *	will look for an app version of the file  first and load that up. It will fall back
	 *	to the empty Nails view if not available (which includes some basic styling so as not
	 *	to look totally rubbish).
	 *
	 *	You can completely overload this view by creating a view at:
	 *
	 *	application/views/auth/password/forgotten_reset
	 *
	 **/


	// --------------------------------------------------------------------------

	//	Write the HTML for the password successfully reset page
?>


	<div id="nails-password-reset-successfully" class="container">
		<p>
			<?=lang( 'auth_forgot_reset_ok' )?>
		</p>
		<p>
			<input type="text" value="<?=htmlentities( $new_password )?>" id="temp-password" style="font-size:1.5em;display:block;text-align:center;" />
		</p>
		<hr />
		<p>
			<?=anchor( 'auth/login', lang( 'auth_forgot_action_proceed' ), 'class="awesome"' )?>
		</p>
	</div>
	<script type="text/javascript">

		var textBox = document.getElementById("temp-password");
		textBox.onfocus = function() {
			textBox.select();

			// Work around Chrome's little problem
			textBox.onmouseup = function() {
				// Prevent further mouseup intervention
				textBox.onmouseup = null;
				return false;
			};
		};
	</script>

<?php
