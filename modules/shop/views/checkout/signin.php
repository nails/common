<div class="container shop checkout">

	<div class="columns eight first user-checkout">
		<h2>Registered User</h2>
		<div class="box">
			<p>
				If you are already registered with <?=APP_NAME?> then log in; you'll be able to see your previous orders
				and track the progress of this order.
			</p>
			<?php
			
				if ( module_is_enabled( 'auth[facebook]' ) || module_is_enabled( 'auth[facebook]' ) || module_is_enabled( 'auth[facebook]' ) ) :
				
					//	This is technically not needed for the default group, but left here by
					//	way of an example
					
					$_token				= array();
					$_token['nonce']	= time();
					$_token['ip']		= $this->input->ip_address();
					$_token['group']	= APP_USER_DEFAULT_GROUP;
					
					$_token = urlencode( $this->encrypt->encode( serialize($_token) . '|' . $_token['ip'] . '|' . $_token['nonce'], APP_PRIVATE_KEY ) );
					
					echo '<p style="text-align:center;">';
					
					//	FACEBOOK
					if ( module_is_enabled( 'auth[facebook]' ) ) :
					
						echo anchor( 'auth/fb/connect?token=' . $_token . '&return_to=' . urlencode( 'shop/checkout' ), lang( 'auth_login_social_signin', 'Facebook' ), 'class="social-signin fb"' );
					
					endif;
					
					//	TWITTER
					if ( module_is_enabled( 'auth[twitter]' ) ) :
					
						echo anchor( 'auth/tw/connect?token=' . $_token . '&return_to=' . urlencode( 'shop/checkout' ), lang( 'auth_login_social_signin', 'Twitter' ), 'class="social-signin tw"' );
					
					endif;
					
					//	LINKEDIN
					if ( module_is_enabled( 'auth[linkedin]' ) ) :
					
						echo anchor( 'auth/li/connect?token=' . $_token . '&return_to=' . urlencode( 'shop/checkout' ), lang( 'auth_login_social_signin', 'LinkedIn' ), 'class="social-signin li"' );
					
					endif;
					
					echo '</p>';
					
					// --------------------------------------------------------------------------
					
					//	OR, natively
					echo '<p>Or, sign in with your email address and password.</p>';
				
				endif;
				
				// --------------------------------------------------------------------------
				
				echo form_open( 'auth/login?return_to=' . urlencode( 'shop/checkout' ) );
				
				// --------------------------------------------------------------------------
				
				$_field					= array();
				$_field['key']			= 'email';
				$_field['label']		= 'Email';
				$_field['placeholder']	= 'Your email address';
				
				echo form_field( $_field );
				
				// --------------------------------------------------------------------------
				
				$_field					= array();
				$_field['key']			= 'password';
				$_field['label']		= 'Password';
				$_field['placeholder']	= 'Your password';
				$_field['type']			= 'password';
				
				echo form_field( $_field );
				
				// --------------------------------------------------------------------------
				
				echo form_field_submit( lang( 'action_login' ) );
				
				echo '<p class="forgot">' . anchor( 'auth/forgotten_password', 'Forgotten your password?' ) . '</p>';
				
				// --------------------------------------------------------------------------
				
				echo form_close();
			
			?>
		</div>
	</div>
	
	<div class="columns eight first new-checkout">
		<h2>New User</h2>
		<div class="box">
			<p>
				If you don't already have an account you can register one now. We'll add this order to your account so you can
				track it's progress.
			</p>
			<?php
			
				if ( module_is_enabled( 'auth[facebook]' ) || module_is_enabled( 'auth[facebook]' ) || module_is_enabled( 'auth[facebook]' ) ) :
				
					//	This is technically not needed for the default group, but left here by
					//	way of an example
					
					$_token				= array();
					$_token['nonce']	= time();
					$_token['ip']		= $this->input->ip_address();
					$_token['group']	= APP_USER_DEFAULT_GROUP;
					
					$_token = urlencode( $this->encrypt->encode( serialize($_token) . '|' . $_token['ip'] . '|' . $_token['nonce'], APP_PRIVATE_KEY ) );
					
					echo '<p style="text-align:center;">';
					
					//	FACEBOOK
					if ( module_is_enabled( 'auth[facebook]' ) ) :
					
						echo anchor( 'auth/fb/connect?token=' . $_token . '&return_to=' . urlencode( 'shop/checkout' ), lang( 'auth_register_social_signin', 'Facebook' ), 'class="social-signin fb"' );
					
					endif;
					
					//	TWITTER
					if ( module_is_enabled( 'auth[twitter]' ) ) :
					
						echo anchor( 'auth/tw/connect?token=' . $_token . '&return_to=' . urlencode( 'shop/checkout' ), lang( 'auth_register_social_signin', 'Twitter' ), 'class="social-signin tw"' );
					
					endif;
					
					//	LINKEDIN
					if ( module_is_enabled( 'auth[linkedin]' ) ) :
					
						echo anchor( 'auth/li/connect?token=' . $_token . '&return_to=' . urlencode( 'shop/checkout' ), lang( 'auth_register_social_signin', 'LinkedIn' ), 'class="social-signin li"' );
					
					endif;
					
					echo '</p>';
					
					// --------------------------------------------------------------------------
					
					//	OR, natively
					echo '<p>Or, register with your email address.</p>';
				
				endif;
				
				// --------------------------------------------------------------------------
				
			
				echo form_open( 'auth/register?return_to=' . urlencode( 'shop/checkout' ) );
				echo form_hidden( 'new_user', TRUE );
				
				// --------------------------------------------------------------------------
				
				$_field					= array();
				$_field['key']			= 'email';
				$_field['label']		= 'Email';
				$_field['placeholder']	= 'Your email address';
				
				echo form_field( $_field );
				
				// --------------------------------------------------------------------------
				
				$_field					= array();
				$_field['key']			= 'password';
				$_field['label']		= 'Password';
				$_field['placeholder']	= 'Choose a password';
				$_field['type']			= 'password';
				
				echo form_field( $_field );
				
				// --------------------------------------------------------------------------
				
				$_field					= array();
				$_field['key']			= 'first_name';
				$_field['label']		= 'First Name';
				$_field['placeholder']	= 'Your First Name';
				
				echo form_field( $_field );
				
				// --------------------------------------------------------------------------
				
				$_field					= array();
				$_field['key']			= 'last_name';
				$_field['label']		= 'Surname';
				$_field['placeholder']	= 'Your Surname';
				
				echo form_field( $_field );
				
				// --------------------------------------------------------------------------
				
				echo form_hidden( 'terms', TRUE );

				// --------------------------------------------------------------------------

				echo form_field_submit( lang( 'action_register' ) );
				
				// --------------------------------------------------------------------------
				
				echo form_close();
			
			?>
		</div>
	</div>
	
	<div class="sixteen columns first last guest-checkout">
		<p>
			Prefer not to register? <?=anchor( shop_setting( 'shop_url' ) . 'checkout?guest=true', 'Checkout as a guest' )?>.
		</p>
	</div>

</div>