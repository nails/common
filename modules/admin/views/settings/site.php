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
		</ul>

		<section class="tabs pages">

			<?php $_display = $this->input->post( 'update' ) == 'analytics' || ! $this->input->post() ? 'active' : ''?>
			<div id="tab-general" class="tab page <?=$_display?> analytics">
				<?=form_open()?>
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
						$_field['default']		= $settings[$_field['key']];
						$_field['placeholder']	= 'UA-XXXXX-YY';

						echo form_field( $_field );

					?>
				</fieldset>

				<?=form_submit( 'submit', lang( 'action_save_changes' ) )?>
				<?=form_close()?>
			</div>

		</section>
</div>