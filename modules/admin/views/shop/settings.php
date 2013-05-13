<div class="group-shop settings">
	<p>
		Configure various aspects of the shop.
	</p>
	
	<hr />
	
		<ul class="tabs">
			<?php $_active = $this->input->post( 'update' ) == 'settings' || ! $this->input->post() ? 'active' : ''?>
			<li class="tab <?=$_active?>">
				<a href="#" data-tab="tab-general">General</a>
			</li>

			<?php $_active = $this->input->post( 'update' ) == 'paymentgateways' ? 'active' : ''?>
			<li class="tab <?=$_active?>">
				<a href="#" data-tab="tab-paymentgateway">Payment Gateways</a>
			</li>

			<?php $_active = $this->input->post( 'update' ) == 'currencies' ? 'active' : ''?>
			<li class="tab <?=$_active?>">
				<a href="#" data-tab="tab-currencies">Currencies</a>
			</li>
		</ul>
		
		<section class="tabs pages">

			<?php $_display = $this->input->post( 'update' ) == 'settings' || ! $this->input->post() ? 'block' : 'none'?>
			<div id="tab-general" class="tab page general" style="display:<?=$_display?>;">
				<?=form_open()?>
				<?=form_hidden( 'update', 'settings' )?>
				<p>
					Generic store settings. Use these to control some store behaviours.
				</p>
				<hr />
				<fieldset id="shop-settings-notifications">
					<legend>Notifications</legend>
					<?php

						//	Order Notifications
						$_field					= array();
						$_field['key']			= 'notify_order';
						$_field['label']		= 'Order Notifications';
						$_field['default']		= $settings['notify_order'];
						$_field['placeholder']	= 'Who should be notified of new orders';
						
						echo form_field( $_field, 'Specify multiple addresses with a comma.' );

					?>
				</fieldset>
				<?=form_submit( 'submit', lang( 'action_save_changes' ) )?>
				<?=form_close()?>
			</div>
			
			<?php $_display = $this->input->post( 'update' ) == 'paymentgateways' ? 'block' : 'none'?>
			<div id="tab-paymentgateway" class="tab page paymentgateway" style="display:<?=$_display?>;">
				<p>
					Set Payment Gateway credentials.
				</p>
				<hr />
				<?php

				if ( $payment_gateways ) :

					echo form_open();
					echo form_hidden( 'update', 'paymentgateways' );

					foreach ( $payment_gateways AS $pg ) :

						echo '<fieldset id="shop-settings-pg-' . $pg->slug . '">';
						echo '<legend>' . $pg->label . '</legend>';

						//	Only superusers can change the 'enabled' status of a payment gateway
						if ( $user->is_superuser() ) :

							//	Enabled
							$_field					= array();
							$_field['key']			= 'paymentgateway[' . $pg->id . '][enabled]';
							$_field['label']		= 'Supported';
							$_field['default']		= $pg->enabled;
							//$_field['class']		= 'chosen';
							
							echo form_field_dropdown( $_field, array( 'No', 'Yes' ) );

						endif;

						// --------------------------------------------------------------------------

						//	Account ID
						$_field					= array();
						$_field['key']			= 'paymentgateway[' . $pg->id . '][account_id]';
						$_field['label']		= 'Account ID';
						$_field['default']		= $pg->account_id;
						$_field['placeholder']	= 'The unique account identifier';
						
						echo form_field( $_field );

						// --------------------------------------------------------------------------

						//	API KEY
						$_field					= array();
						$_field['key']			= 'paymentgateway[' . $pg->id . '][api_key]';
						$_field['label']		= 'API Key';
						$_field['default']		= $pg->api_key;
						$_field['placeholder']	= 'The key for accessing this payment gateway\'s API';
						
						echo form_field( $_field );

						// --------------------------------------------------------------------------

						//	API Secret
						$_field					= array();
						$_field['key']			= 'paymentgateway[' . $pg->id . '][api_secret]';
						$_field['label']		= 'API Secret';
						$_field['default']		= $pg->api_secret;
						$_field['placeholder']	= 'The secret or password for accessing this payment gateway\'s API';
						
						echo form_field( $_field );

						// --------------------------------------------------------------------------

						if ( $user->is_superuser() ) :

							//	Sandbox Account ID
							$_field					= array();
							$_field['key']			= 'paymentgateway[' . $pg->id . '][sandbox_account_id]';
							$_field['label']		= 'Sandbox Account ID';
							$_field['default']		= $pg->sandbox_account_id;
							$_field['placeholder']	= 'The unique account identifier';
							
							echo form_field( $_field );

							// --------------------------------------------------------------------------

							//	Sandbox API KEY
							$_field					= array();
							$_field['key']			= 'paymentgateway[' . $pg->id . '][sandbox_api_key]';
							$_field['label']		= 'Sandbox API Key';
							$_field['default']		= $pg->sandbox_api_key;
							$_field['placeholder']	= 'The key for accessing this payment gateway\'s API';
							
							echo form_field( $_field );

							// --------------------------------------------------------------------------

							//	Sandbox API Secret
							$_field					= array();
							$_field['key']			= 'paymentgateway[' . $pg->id . '][sandbox_api_secret]';
							$_field['label']		= 'Sandbox API Secret';
							$_field['default']		= $pg->sandbox_api_secret;
							$_field['placeholder']	= 'The secret or password for accessing this payment gateway\'s API';
							
							echo form_field( $_field );

						endif;

						echo '</fieldset>';

					endforeach;

					echo form_submit( 'submit', lang( 'action_save_changes' ) );
					echo form_close();

				else :

					echo '<p class="system-alert message no-close">';
					echo '<strong>No Payment gateways have been enabled.</strong>';
					echo '<br />No payment gateways ahve been enabled for this site. Please contact the developers on ' . mailto( APP_EMAIL_DEVELOPER ) . ' for assistance.';
					echo '</p>';

				endif;

				?>
			</div>

			<?php $_display = $this->input->post( 'update' ) == 'currencies' ? 'block' : 'none'?>
			<div id="tab-currencies" class="tab page currencies" style="display:<?=$_display?>;">
				<!-- <?=form_open()?> -->
				<!-- <?=form_hidden( 'update', 'currencies' )?> -->
				<p>
					Configure supported currencies.
				</p>
				<hr />
				<p class="system-alert message no-close">
					<strong>Coming soon!</strong>
					<br />Choose which currencies you wish to support in your online shop.
				</p>
				<!-- <?=form_close()?> -->
			</div>
		</section>
</div>