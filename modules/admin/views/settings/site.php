<div class="group-shop settings">
	<p>
		Configure various aspects of the site.
	</p>

	<hr />

		<ul class="tabs">
			<?php $_active = $this->input->post( 'update' ) == 'analytics' || ! $this->input->post() ? 'active' : ''?>
			<li class="tab <?=$_active?>">
				<a href="#" data-tab="tab-analytics">Analytics</a>
			</li>
			<?php $_active = $this->input->post( 'update' ) == 'auth' ? 'active' : ''?>
			<li class="tab <?=$_active?>">
				<a href="#" data-tab="tab-auth">Authentication</a>
			</li>
		</ul>

		<section class="tabs pages">

			<?php $_display = $this->input->post( 'update' ) == 'analytics' || ! $this->input->post() ? 'active' : ''?>
			<div id="tab-analytics" class="tab page <?=$_display?> analytics">
				<?=form_open( NULL, 'style="margin-bottom:0;"' )?>
				<?=form_hidden( 'update', 'analytics' )?>
				<p>
					Configure your analytics accounts. If field is left empty then that provider will not be used.
				</p>
				<hr />
				<fieldset id="shop-settings-notifications">
					<legend>Google Analytics</legend>
					<?php

						//	Order Notifications
						$_field					= array();
						$_field['key']			= 'google_analytics_account';
						$_field['label']		= 'Profile ID';
						$_field['default']		= app_setting( $_field['key'] );
						$_field['placeholder']	= 'UA-XXXXX-YY';

						echo form_field( $_field );

					?>
				</fieldset>
				<p style="margin-top:1em;margin-bottom:0;">
					<?=form_submit( 'submit', lang( 'action_save_changes' ), 'style="margin-bottom:0;"' )?>
				</p>
				<?=form_close()?>
			</div>

			<?php $_display = $this->input->post( 'update' ) == 'auth' ? 'active' : ''?>
			<div id="tab-auth" class="tab page <?=$_display?> auth">
				<?=form_open( NULL, 'style="margin-bottom:0;"')?>
				<?=form_hidden( 'update', 'auth' )?>
				<p>
				Configure the site's authentication settings and defaults.
				</p>
				<hr />
				<fieldset id="site-settings-socialsignin">
					<legend>Social Sign In</legend>
					<?php

						$_field					= array();
						$_field['key']			= 'social_signin_fb_enabled';
						$_field['label']		= 'Facebook';
						$_field['class']		= 'social-signin';
						$_field['data']			= array( 'fields' => 'socialsignin-fb-settings' );
						$_field['default']		= app_setting( $_field['key'] ) ? TRUE : FALSE;

						echo form_field_boolean( $_field );

						// --------------------------------------------------------------------------

						$_display = app_setting( $_field['key'] ) ? 'block' : 'none';
						echo '<div id="socialsignin-fb-settings" style="display:' . $_display . ';border-bottom:1px solid #CCC;">';

							$_field					= array();
							$_field['key']			= 'social_signin_fb_app_id';
							$_field['label']		= 'Facebook App ID';
							$_field['default']		= app_setting( $_field['key'] );

							echo form_field( $_field );

							// --------------------------------------------------------------------------

							$_field					= array();
							$_field['key']			= 'social_signin_fb_app_secret';
							$_field['label']		= 'Facebook App Secret';
							$_field['default']		= app_setting( $_field['key'] );

							if ( $_field['default'] ) :

								$_field['default'] = $this->encrypt->decode( $_field['default'], APP_PRIVATE_KEY );

							endif;

							echo form_field( $_field );

							// --------------------------------------------------------------------------

							$_field					= array();
							$_field['key']			= 'social_signin_fb_app_scope';
							$_field['label']		= 'Facebook App Scope';
							$_field['info']			= 'Comma seperated list of scopes. The \'email\' scope will be automatically added.';
							$_field['default']		= app_setting( $_field['key'] ) ? implode( ',', app_setting( $_field['key'] ) ) : '';

							echo form_field( $_field );

							// --------------------------------------------------------------------------

							$_field					= array();
							$_field['key']			= 'social_signin_fb_settings_page';
							$_field['label']		= 'Facebook Settings Page';
							$_field['default']		= app_setting( $_field['key'] );

							echo form_field( $_field );

						echo '</div>';

						// --------------------------------------------------------------------------

						$_field					= array();
						$_field['key']			= 'social_signin_tw_enabled';
						$_field['label']		= 'Twitter';
						$_field['class']		= 'social-signin';
						$_field['data']			= array( 'fields' => 'socialsignin-tw-settings' );
						$_field['default']		= app_setting( $_field['key'] ) ? TRUE : FALSE;

						echo form_field_boolean( $_field );

						// --------------------------------------------------------------------------

						$_display = app_setting( $_field['key'] ) ? 'block' : 'none';
						echo '<div id="socialsignin-tw-settings" style="display:' . $_display . ';border-bottom:1px solid #CCC;">';

							$_field					= array();
							$_field['key']			= 'social_signin_tw_app_key';
							$_field['label']		= 'Twitter App ID';
							$_field['default']		= app_setting( $_field['key'] );

							echo form_field( $_field );

							// --------------------------------------------------------------------------

							$_field					= array();
							$_field['key']			= 'social_signin_tw_app_secret';
							$_field['label']		= 'Twitter App Secret';
							$_field['default']		= app_setting( $_field['key'] );

							if ( $_field['default'] ) :

								$_field['default'] = $this->encrypt->decode( $_field['default'], APP_PRIVATE_KEY );

							endif;

							echo form_field( $_field );

							// --------------------------------------------------------------------------

							$_field					= array();
							$_field['key']			= 'social_signin_tw_settings_page';
							$_field['label']		= 'Twitter Settings Page';
							$_field['default']		= app_setting( $_field['key'] );

							echo form_field( $_field );

						echo '</div>';

						// --------------------------------------------------------------------------

						$_field					= array();
						$_field['key']			= 'social_signin_li_enabled';
						$_field['label']		= 'LinkedIn';
						$_field['class']		= 'social-signin';
						$_field['data']			= array( 'fields' => 'socialsignin-li-settings' );
						$_field['default']		= app_setting( $_field['key'] ) ? TRUE : FALSE;

						echo form_field_boolean( $_field );

						// --------------------------------------------------------------------------

						$_display = app_setting( $_field['key'] ) ? 'block' : 'none';
						echo '<div id="socialsignin-li-settings" style="display:' . $_display . ';border-bottom:1px solid #CCC;">';

							$_field					= array();
							$_field['key']			= 'social_signin_li_app_key';
							$_field['label']		= 'LinkedIn App ID';
							$_field['default']		= app_setting( $_field['key'] );

							echo form_field( $_field );

							// --------------------------------------------------------------------------

							$_field					= array();
							$_field['key']			= 'social_signin_li_app_secret';
							$_field['label']		= 'LinkedIn App Secret';
							$_field['default']		= app_setting( $_field['key'] );

							if ( $_field['default'] ) :

								$_field['default'] = $this->encrypt->decode( $_field['default'], APP_PRIVATE_KEY );

							endif;

							echo form_field( $_field );

							// --------------------------------------------------------------------------

							$_field					= array();
							$_field['key']			= 'social_signin_li_settings_page';
							$_field['label']		= 'LinkedIn Settings Page';
							$_field['default']		= app_setting( $_field['key'] );

							echo form_field( $_field );

						echo '</div>';
					?>
				</fieldset>
				<p style="margin-top:1em;margin-bottom:0;">
					<?=form_submit( 'submit', lang( 'action_save_changes' ), 'style="margin-bottom:0;"' )?>
				</p>
				<?=form_close()?>
			</div>

		</section>
</div>
<script type="text/javascript">

	var _settings;

	$(function()
	{
		_settings = new NAILS_Admin_Site_Settings();
		_settings.init();
	});

</script>