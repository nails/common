<div class="group-blog settings">
	<p>
		Configure various aspects of the blog.
	</p>
	
	<hr />
	
		<ul class="tabs">
			<?php $_active = $this->input->post( 'update' ) == 'settings' || ! $this->input->post() ? 'active' : ''?>
			<li class="tab <?=$_active?>">
				<a href="#" data-tab="tab-general">General</a>
			</li>

			<?php $_active = $this->input->post( 'update' ) == 'sidebar' ? 'active' : ''?>
			<li class="tab <?=$_active?>">
				<a href="#" data-tab="tab-blog-sidebar">Sidebar</a>
			</li>
		</ul>
		
		<section class="tabs pages">

			<?php $_display = $this->input->post( 'update' ) == 'settings' || ! $this->input->post() ? 'block' : 'none'?>
			<div id="tab-general" class="tab page general" style="display:<?=$_display?>;">
				<?=form_open()?>
				<?=form_hidden( 'update', 'settings' )?>
				<p>
					Generic blog settings. Use these to control some blog behaviours.
				</p>
				<hr />
				<fieldset id="blog-settings-url">
					<legend>URL</legend>
					<p>
						Customise the blog's URL by specifying it here.
					</p>
					<p class="system-alert message no-close">
						<strong>Please Note:</strong> Any deviations from <code>blog/</code> will require that the appropriate route be set in the app's <code>routes.php</code> file.
					</p>
					<?php

						//	Blog URL
						$_field					= array();
						$_field['key']			= 'blog_url';
						$_field['label']		= 'Blog URL';
						$_field['default']		= $settings['blog_url'];
						$_field['placeholder']	= 'Customise the Blog\'s URL (include trialing slash)';
						
						echo form_field( $_field );

					?>
				</fieldset>

				<fieldset id="blog-settings-cattag">
					<legend>Categories &amp; Tags</legend>
					<?php

						//	Enable/disable categories
						$_field					= array();
						$_field['key']			= 'categories_enabled';
						$_field['label']		= 'Categories';
						$_field['default']		= $settings['categories_enabled'];
						
						echo form_field_dropdown( $_field, array( 'Disabled', 'Enabled' ) );

						// --------------------------------------------------------------------------

						//	Enable/disable tags
						$_field					= array();
						$_field['key']			= 'tags_enabled';
						$_field['label']		= 'Tags';
						$_field['default']		= $settings['tags_enabled'];
						
						echo form_field_dropdown( $_field, array( 'Disabled', 'Enabled' ) );

					?>
				</fieldset>
				<?=form_submit( 'submit', lang( 'action_save_changes' ) )?>
				<?=form_close()?>
			</div>

			<?php $_display = $this->input->post( 'update' ) == 'sidebar'  ? 'block' : 'none'?>
			<div id="tab-blog-sidebar" class="tab page blog-sidebar" style="display:<?=$_display?>;">
				<?=form_open()?>
				<?=form_hidden( 'update', 'sidebar' )?>
				<fieldset id="blog-settings-blog-sidebar">
					<legend>Sidebar</legend>
					<?php

						//	Enable/disable categories
						$_field					= array();
						$_field['key']			= 'sidebar_enabled';
						$_field['label']		= 'Enabled';
						$_field['default']		= $settings['sidebar_enabled'];
						
						echo form_field_dropdown( $_field, array( 'Disabled', 'Enabled' ) );

						// --------------------------------------------------------------------------

						//	Enable/disable categories
						$_field					= array();
						$_field['key']			= 'sidebar_position';
						$_field['label']		= 'Position';
						$_field['default']		= $settings['sidebar_position'];
						
						echo form_field_dropdown( $_field, array( 'left' => 'Left', 'right' => 'Right' ) );

					?>
				</fieldset>
				<?=form_submit( 'submit', lang( 'action_save_changes' ) )?>
				<?=form_close()?>
			</div>
		</section>
</div>