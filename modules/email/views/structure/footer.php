	<div class="footer">
	<?php

		$_links = array();

		//	View Online link & Unsubscribe
		if ( ! empty( $email_ref ) ) :

			//	Generate the hash
			$_time = time();
			$_hash = $email_ref . '/' . $_time . '/' . md5( $_time . $secret . $email_ref );

			//	Link
			$_links[] = anchor( 'email/view_online/' . $_hash, 'View this E-mail Online' );

		endif;

		// --------------------------------------------------------------------------

		//	1-Click unsubscribe
		if ( ! empty( $sent_to->login_url ) ) :

			$_login_url	= $sent_to->login_url . '?return_to=';
			$_return	= '/email/unsubscribe?token=';

			//	Bit of a hack; keep trying until there's no + symbol in the hash, try up to 20 times before giving up
			//	TODO: make this less hacky

			$_counter	= 0;
			$_attemps	= 20;

			do
			{
				$_token = $this->encrypt->encode( $email_type_id . '|' . $email_ref . '|' . $sent_to->email, $secret );
				$_counter++;
			}
			while( $_counter <= $_attemps && strpos( $_token, '+') !== FALSE );

			//	Link
			$_links[] = anchor( $_login_url . urlencode( $_return . $_token ), 'Unsubscribe' );

		endif;

		// --------------------------------------------------------------------------

		//	Render
		if ( $_links ) :

			echo '<p><small>';
			echo implode( ' | ', $_links );
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