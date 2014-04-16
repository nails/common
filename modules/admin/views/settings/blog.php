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
			<?php $_active = $this->input->post( 'update' ) == 'commenting' ? 'active' : ''?>
			<li class="tab <?=$_active?>">
				<a href="#" data-tab="tab-commenting">Commenting</a>
			</li>
			<?php $_active = $this->input->post( 'update' ) == 'social' ? 'active' : ''?>
			<li class="tab <?=$_active?>">
				<a href="#" data-tab="tab-social">Social Tools</a>
			</li>
		</ul>

		<section class="tabs pages">

			<?php $_display = $this->input->post( 'update' ) == 'settings' || ! $this->input->post() ? 'active' : ''?>
			<div id="tab-general" class="tab page <?=$_display?> general">
				<?=form_open( NULL, 'style="margin-bottom:0;"')?>
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
					<?php

						if ( $settings['blog_url'] != 'blog/' ) :

							$_routes_file = file_get_contents( FCPATH . APPPATH . '/config/routes.php' );
							$_pattern = '#\$route\[\'' . str_replace( '/', '\/', substr( $settings['blog_url'], 0, -1 ) ) . '\(\/\(\:any\)\?\/\?\)\?\'\]\s*?=\s*?\'blog\/\$2\'\;#';

							if ( ! preg_match( $_pattern, $_routes_file) ) :

								//	Check the routes_app file
								$_routes_file = @file_get_contents( FCPATH . APPPATH . '/config/routes_app.php' );

								if ( ! $_routes_file || ! preg_match( $_pattern, $_routes_file ) ) :

									echo '<p class="system-alert message no-close">';
									echo '<strong>Please Note:</strong> Ensure that the following route is in the app\'s <code>routes.php</code> or <code>routes_app.php</code> file or the blog may not work as expected.';
									echo '<code style="display:block;margin-top:10px;border:1px solid #CCC;background:#EFEFEF;padding:10px;">$route[\'' . substr( $settings['blog_url'], 0, -1 ) . '(/(:any)?/?)?\'] = \'blog/$2\';</code>';
									echo '</p>';

								endif;

							endif;

						endif;

						// --------------------------------------------------------------------------

						//	Blog URL
						$_field					= array();
						$_field['key']			= 'blog_url';
						$_field['label']		= 'Blog URL';
						$_field['default']		= $settings['blog_url'];
						$_field['placeholder']	= 'Customise the Blog\'s URL (include trialing slash)';

						echo form_field( $_field );

					?>
				</fieldset>

				<fieldset id="blog-settings-excerpts">
					<legend>Post Excerpts</legend>
					<p>
						Excerpts are short post summaries of posts. If enabled these sumamries will be shown
						beneath the title of the post on the main blog page (i.e to read the full post the
						user will have to click through to the post itself).
					</p>
					<?php

						//	Enable/disable post excerpts
						$_field					= array();
						$_field['key']			= 'use_excerpts';
						$_field['label']		= 'Use excerpts';
						$_field['default']		= $settings['use_excerpts'];

						echo form_field_boolean( $_field );
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

						echo form_field_boolean( $_field );

						// --------------------------------------------------------------------------

						//	Enable/disable tags
						$_field					= array();
						$_field['key']			= 'tags_enabled';
						$_field['label']		= 'Tags';
						$_field['default']		= $settings['tags_enabled'];

						echo form_field_boolean( $_field );

					?>
				</fieldset>

				<fieldset id="blog-settings-rss">
					<legend>RSS</legend>
					<?php

						$_field					= array();
						$_field['key']			= 'rss_enabled';
						$_field['label']		= 'RSS Enabled';
						$_field['default']		= ! empty( $settings['rss_enabled'] ) ? TRUE : FALSE;

						echo form_field_boolean( $_field );
					?>
				</fieldset>
				<p style="margin-top:1em;margin-bottom:0;">
					<?=form_submit( 'submit', lang( 'action_save_changes' ), 'style="margin-bottom:0;"' )?>
				</p>
				<?=form_close()?>
			</div>

			<?php $_display = $this->input->post( 'update' ) == 'commenting' ? 'active' : ''?>
			<div id="tab-commenting" class="tab page <?=$_display?> commenting">
				<?=form_open( NULL, 'style="margin-bottom:0;"')?>
				<?=form_hidden( 'update', 'commenting' )?>
				<p>
					Customise how commenting works on your blog.
				</p>
				<hr />
				<fieldset id="blog-settings-comments">
					<legend>Post comments enabled</legend>
					<?php

						$_field					= array();
						$_field['key']			= 'comments_enabled';
						$_field['label']		= 'Comments Enabled';
						$_field['default']		= ! empty( $settings['comments_enabled'] ) ? TRUE : FALSE;

						echo form_field_boolean( $_field );
					?>
				</fieldset>

				<fieldset id="blog-settings-comments-engine">
					<legend>Post comments powered by</legend>
					<p>
						Choose which engine to use for blog post commenting. Please note that
						existing comments will not be carried through to another service should
						this value be changed.
					</p>
					<?php

						$_field				= array();
						$_field['key']		= 'comments_engine';
						$_field['label']	= 'Comment Engine';
						$_field['default']	= empty( $settings[$_field['key']] ) ? 'NATIVE' : $settings[$_field['key']];
						$_field['class']	= 'chosen';
						$_field['id']		= 'comment-engine';

						$_options			= array();
						$_options['NATIVE']	= 'Native';
						$_options['DISQUS']	= 'Disqus';

						echo form_field_dropdown( $_field, $_options );
					?>

					<hr />

					<div id="native-settings" style="display:<?=empty( $settings['comments_engine'] ) || $settings['comments_engine'] == 'NATIVE' ? 'block' : 'none'?>">
						<p class="system-alert message no-close">
							<strong>Coming Soon!</strong> Native commenting is in the works and will be available soon.
							<?php

								//	TODO: Need to be able to handle a lot with native commenting, e.g
								//	- anonymous comments/forced login etc
								//	- pingbacks?
								//	- anything else WordPress might do?

							?>
						</p>
					</div>

					<div id="disqus-settings" style="display:<?=! empty( $settings['comments_engine'] ) && $settings['comments_engine'] == 'DISQUS' ? 'block' : 'none'?>">
					<?php

						//	Blog URL
						$_field					= array();
						$_field['key']			= 'comments_disqus_shortname';
						$_field['label']		= 'Disqus Shortname';
						$_field['default']		= empty( $settings[$_field['key']] ) ? '' : $settings[$_field['key']];
						$_field['placeholder']	= 'The Disqus shortname for this website.';

						echo form_field( $_field, 'Create a shortname at disqus.com.' );

					?>
					</div>
				</fieldset>
				<p style="margin-top:1em;margin-bottom:0;">
					<?=form_submit( 'submit', lang( 'action_save_changes' ), 'style="margin-bottom:0;"' )?>
				</p>
				<?=form_close()?>
			</div>

			<?php $_display = $this->input->post( 'update' ) == 'social' ? 'active' : ''?>
			<div id="tab-social" class="tab page <?=$_display?> social">
				<?=form_open( NULL, 'style="margin-bottom:0;"')?>
				<?=form_hidden( 'update', 'social' )?>
				<p>
					Place social sharing tools on your blog post pages.
				</p>
				<hr />
				<fieldset id="blog-settings-social">
					<legend>Enable Services</legend>
					<?php

						$_field					= array();
						$_field['key']			= 'social_facebook_enabled';
						$_field['label']		= 'Facebook';
						$_field['id']			= 'social-service-facebook';
						$_field['default']		= ! empty( $settings[$_field['key']] ) ? TRUE : FALSE;

						echo form_field_boolean( $_field );

						// --------------------------------------------------------------------------

						$_field					= array();
						$_field['key']			= 'social_twitter_enabled';
						$_field['label']		= 'Twitter';
						$_field['id']			= 'social-service-twitter';
						$_field['default']		= ! empty( $settings[$_field['key']] ) ? TRUE : FALSE;

						echo form_field_boolean( $_field );

						// --------------------------------------------------------------------------

						$_field					= array();
						$_field['key']			= 'social_googleplus_enabled';
						$_field['label']		= 'Google+';
						$_field['id']			= 'social-service-googleplus';
						$_field['default']		= ! empty( $settings[$_field['key']] ) ? TRUE : FALSE;

						echo form_field_boolean( $_field );

						// --------------------------------------------------------------------------

						$_field					= array();
						$_field['key']			= 'social_pinterest_enabled';
						$_field['label']		= 'Pinterest';
						$_field['id']			= 'social-service-pinterest';
						$_field['default']		= ! empty( $settings[$_field['key']] ) ? TRUE : FALSE;

						echo form_field_boolean( $_field );
					?>
				</fieldset>
				<fieldset id="blog-settings-social-twitter" style="display:<?=! empty( $settings['social_twitter_enabled'] ) ? 'block' : 'none' ?>">
					<legend>Twitter Settings</legend>
					<?php

						$_field					= array();
						$_field['key']			= 'social_twitter_via';
						$_field['label']		= 'Via';
						$_field['default']		= ! empty( $settings[$_field['key']] ) ? $settings[$_field['key']] : '';
						$_field['placeholder']	= 'Put your @username here to add it to the tweet';

						echo form_field( $_field );
					?>
				</fieldset>
				<fieldset id="blog-settings-social-config" style="display:<?=! empty( $settings['social_enabled'] ) ? 'block' : 'none' ?>">
					<legend>Customisation</legend>
					<?php

						$_field					= array();
						$_field['key']			= 'social_skin';
						$_field['label']		= 'Skin';
						$_field['class']		= 'chosen';
						$_field['default']		= ! empty( $settings[$_field['key']] ) ? $settings[$_field['key']] : 'CLASSIC';

						$_options				= array();
						$_options['CLASSIC']	= 'Classic';
						$_options['FLAT']		= 'Flat';
						$_options['BIRMAN']		= 'Birman';

						echo form_field_dropdown( $_field, $_options );

						// --------------------------------------------------------------------------

						$_field					= array();
						$_field['key']			= 'social_layout';
						$_field['label']		= 'Layout';
						$_field['class']		= 'chosen';
						$_field['id']			= 'blog-settings-social-layout';
						$_field['default']		= ! empty( $settings[$_field['key']] ) ? $settings[$_field['key']] : 'HORIZONTAL';

						$_options				= array();
						$_options['HORIZONTAL']	= 'Horizontal';
						$_options['VERTICAL']	= 'Vertical';
						$_options['SINGLE']		= 'Single Button';

						echo form_field_dropdown( $_field, $_options );

						// --------------------------------------------------------------------------

						$_display = ! empty( $settings[$_field['key']] ) && $settings[$_field['key']] == 'SINGLE' ? 'block' : 'none';

						echo '<div id="blog-settings-social-layout-single-text" style="display:' . $_display . '">';

							$_field					= array();
							$_field['key']			= 'social_layout_single_text';
							$_field['label']		= 'Button Text';
							$_field['default']		= ! empty( $settings[$_field['key']] ) ? $settings[$_field['key']] : 'Share';
							$_field['placeholder']	= 'Specify what text should be rendered on the button';

							echo form_field( $_field );

						echo '</div>';


						// --------------------------------------------------------------------------

						$_field					= array();
						$_field['key']			= 'social_counters';
						$_field['label']		= 'Show Counters';
						$_field['id']			= 'social-counters';
						$_field['default']		= ! empty( $settings[$_field['key']] ) ? TRUE : FALSE;

						echo form_field_boolean( $_field );
					?>
				</fieldset>
				<p style="margin-top:1em;margin-bottom:0;">
					<?=form_submit( 'submit', lang( 'action_save_changes' ), 'style="margin-bottom:0;"' )?>
				</p>
				<?=form_close()?>
			</div>

			<?php $_display = $this->input->post( 'update' ) == 'sidebar' ? 'active' : ''?>
			<div id="tab-sidebar" class="tab page <?=$_display?> sidebar">

				<p>
					Configure the sidebar widgets.
				</p>
				<hr />
				<p class="system-alert message no-close">
					<strong>Coming Soon!</strong> We're working on an interface to make configuring the blog sidebar as easy as possible!
				</p>
				<?php

					//	TODO: Maybe this uses the CMS areas thing

				?>

			</div>

		</section>
</div>
<script type="text/javascript">

	var _settings;

	$(function()
	{
		_settings = new NAILS_Admin_Blog_Settings();
		_settings.init();
	});

</script>