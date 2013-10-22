	<div class="footer">
	<?php

		//	View Online link
		if ( isset( $email_ref ) ) :

			$_time = time();

			echo '<p><small>';
			echo anchor( 'email/view_online/' . $email_ref . '/' . $_time . '/' . md5( $_time . $secret . $email_ref ), 'View this E-mail Online' );
			echo '</small></p>';

		endif;

		// --------------------------------------------------------------------------

		//	Tracker, production only
		if ( ENVIRONMENT == 'production' && ! $ci->user->is_admin() && ! $ci->user->was_admin() ) :

			$_time = time();
			echo '<img src="' . site_url( 'email/tracker/' . $email_ref . '/' . $_time . '/' . md5( $_time . $secret . $email_ref ) ) . '/tracker.gif" width="0" height="0" style="width:0px;height:0px;"">';

		endif;

	?>
	</div>
	</div>
	</div>
	</body>
</html>