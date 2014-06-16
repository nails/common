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

			<?php $_active = $this->input->post( 'update' ) == 'skin' ? 'active' : ''?>
			<li class="tab <?=$_active?>">
				<a href="#" data-tab="tab-skin">Skin</a>
			</li>

			<?php $_active = $this->input->post( 'update' ) == 'commenting' ? 'active' : ''?>
			<li class="tab <?=$_active?>">
				<a href="#" data-tab="tab-commenting">Commenting</a>
			</li>

			<?php $_active = $this->input->post( 'update' ) == 'social' ? 'active' : ''?>
			<li class="tab <?=$_active?>">
				<a href="#" data-tab="tab-social">Social Tools</a>
			</li>

			<?php $_active = $this->input->post( 'update' ) == 'sidebar' ? 'active' : ''?>
			<li class="tab <?=$_active?>">
				<a href="#" data-tab="tab-sidebar">Sidebar</a>
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
				<fieldset id="blog-settings-name">
					<legend>Name</legend>
					<p>
						Is this a blog? Or is it news? Or something else altogether...
					</p>
					<?php

						//	Blog Name
						$_field					= array();
						$_field['key']			= 'name';
						$_field['label']		= 'Blog Name';
						$_field['default']		= app_setting( 'name', 'blog' ) ? app_setting( 'name', 'blog' ) : 'Blog';
						$_field['placeholder']	= 'Customise the Blog\'s Name';

						echo form_field( $_field );

					?>
				</fieldset>

				<fieldset id="blog-settings-url">
					<legend>URL</legend>
					<p>
						Customise the blog's URL by specifying it here.
					</p>
					<?php

						//	Blog URL
						$_field					= array();
						$_field['key']			= 'url';
						$_field['label']		= 'Blog URL';
						$_field['default']		= app_setting( 'url', 'blog' ) ? app_setting( 'url', 'blog' ) : 'blog/';
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
						$_field['default']		= app_setting( $_field['key'], 'blog' );

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
						$_field['default']		= app_setting( $_field['key'], 'blog' );

						echo form_field_boolean( $_field );

						// --------------------------------------------------------------------------

						//	Enable/disable tags
						$_field					= array();
						$_field['key']			= 'tags_enabled';
						$_field['label']		= 'Tags';
						$_field['default']		= app_setting( $_field['key'], 'blog' );

						echo form_field_boolean( $_field );

					?>
				</fieldset>

				<fieldset id="blog-settings-rss">
					<legend>RSS</legend>
					<?php

						$_field					= array();
						$_field['key']			= 'rss_enabled';
						$_field['label']		= 'RSS Enabled';
						$_field['default']		= app_setting( $_field['key'], 'blog' ) ? TRUE : FALSE;

						echo form_field_boolean( $_field );
					?>
				</fieldset>
				<p style="margin-top:1em;margin-bottom:0;">
					<?=form_submit( 'submit', lang( 'action_save_changes' ), 'style="margin-bottom:0;"' )?>
				</p>
				<?=form_close()?>
			</div>

			<?php $_display = $this->input->post( 'update' ) == 'skin' ? 'active' : ''?>
			<div id="tab-skin" class="tab page <?=$_display?> skin">
				<?=form_open( NULL, 'style="margin-bottom:0;"' )?>
				<?=form_hidden( 'update', 'skin' )?>
				<p>
					The following Blog skins are available to use.
				</p>
				<hr />
				<?php

					if ( $skins ) :

						$_selected_skin = app_setting( 'skin', 'blog' ) ? app_setting( 'skin', 'blog' ) : 'getting-started';

						echo '<ul class="skins">';
						foreach( $skins AS $skin ) :

							$_name			= ! empty( $skin->name ) ? $skin->name : 'Untitled';
							$_description	= ! empty( $skin->description ) ? $skin->description : '';

							if ( file_exists( $skin->path . 'icon.png' ) ) :

								$_icon = $skin->url . 'icon.png';

							elseif ( file_exists( $skin->path . 'icon.jpg' ) ) :

								$_icon = $skin->url . 'icon.jpg';

							elseif ( file_exists( $skin->path . 'icon.gif' ) ) :

								$_icon = $skin->url . 'icon.gif';

							else :

								$_icon = NAILS_ASSETS_URL . 'img/admin/modules/settings/blog-skin-no-icon.png';

							endif;

							$_selected	= $skin->slug == $_selected_skin ? TRUE : FALSE;
							$_class		= $_selected ? 'selected' : '';

							echo '<li class="skin ' . $_class . '" rel="tipsy" title="' . $_description . '">';
								echo '<div class="icon">' . img( $_icon ) . '</div>';
								echo '<div class="name">';
									echo $_name;
									echo '<span class="ion-checkmark-circled"></span>';
								echo '</div>';
								echo form_radio( 'skin', $skin->slug, $_selected );
							echo '</li>';

						endforeach;
						echo '</ul>';

						echo '<hr class="clearfix" />';

					else :

						echo '<p class="system-alert error no-close">';
							echo '<strong>Error:</strong> ';
							echo 'I\'m sorry, but I couldn\'t find any skins to use. This is a configuration error and should be raised with the developer.';
						echo '</p>';

					endif;

				?>
				<p>
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
						$_field['default']		= app_setting( $_field['key'], 'blog' ) ? TRUE : FALSE;

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
						$_field['default']	= ! app_setting( $_field['key'], 'blog' ) ? 'NATIVE' : app_setting( $_field['key'], 'blog' );
						$_field['class']	= 'select2';
						$_field['id']		= 'comment-engine';

						$_options			= array();
						$_options['NATIVE']	= 'Native';
						$_options['DISQUS']	= 'Disqus';

						echo form_field_dropdown( $_field, $_options );
					?>

					<hr />

					<div id="native-settings" style="display:<?=! app_setting( $_field['key'], 'blog' ) || app_setting( $_field['key'], 'blog' ) == 'NATIVE' ? 'block' : 'none'?>">
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

					<div id="disqus-settings" style="display:<?=app_setting( $_field['key'], 'blog' ) && app_setting( $_field['key'], 'blog' ) == 'DISQUS' ? 'block' : 'none'?>">
					<?php

						//	Blog URL
						$_field					= array();
						$_field['key']			= 'comments_disqus_shortname';
						$_field['label']		= 'Disqus Shortname';
						$_field['default']		= app_setting( $_field['key'], 'blog' );
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
						$_field['default']		= app_setting( $_field['key'], 'blog' ) ? TRUE : FALSE;

						echo form_field_boolean( $_field );

						// --------------------------------------------------------------------------

						$_field					= array();
						$_field['key']			= 'social_twitter_enabled';
						$_field['label']		= 'Twitter';
						$_field['id']			= 'social-service-twitter';
						$_field['default']		= app_setting( $_field['key'], 'blog' ) ? TRUE : FALSE;

						echo form_field_boolean( $_field );

						// --------------------------------------------------------------------------

						$_field					= array();
						$_field['key']			= 'social_googleplus_enabled';
						$_field['label']		= 'Google+';
						$_field['id']			= 'social-service-googleplus';
						$_field['default']		= app_setting( $_field['key'], 'blog' ) ? TRUE : FALSE;

						echo form_field_boolean( $_field );

						// --------------------------------------------------------------------------

						$_field					= array();
						$_field['key']			= 'social_pinterest_enabled';
						$_field['label']		= 'Pinterest';
						$_field['id']			= 'social-service-pinterest';
						$_field['default']		= app_setting( $_field['key'], 'blog' ) ? TRUE : FALSE;

						echo form_field_boolean( $_field );
					?>
				</fieldset>
				<fieldset id="blog-settings-social-twitter" style="display:<?=app_setting( 'social_twitter_enabled', 'blog' ) ? 'block' : 'none' ?>">
					<legend>Twitter Settings</legend>
					<?php

						$_field					= array();
						$_field['key']			= 'social_twitter_via';
						$_field['label']		= 'Via';
						$_field['default']		= app_setting( $_field['key'], 'blog' );
						$_field['placeholder']	= 'Put your @username here to add it to the tweet';

						echo form_field( $_field );
					?>
				</fieldset>
				<fieldset id="blog-settings-social-config" style="display:<?=app_setting( 'social_enabled', 'blog' ) ? 'block' : 'none' ?>">
					<legend>Customisation</legend>
					<?php

						$_field					= array();
						$_field['key']			= 'social_skin';
						$_field['label']		= 'Skin';
						$_field['class']		= 'select2';
						$_field['default']		= app_setting( $_field['key'], 'blog' ) ? app_setting( $_field['key'], 'blog' ) : 'CLASSIC';

						$_options				= array();
						$_options['CLASSIC']	= 'Classic';
						$_options['FLAT']		= 'Flat';
						$_options['BIRMAN']		= 'Birman';

						echo form_field_dropdown( $_field, $_options );

						// --------------------------------------------------------------------------

						$_field					= array();
						$_field['key']			= 'social_layout';
						$_field['label']		= 'Layout';
						$_field['class']		= 'select2';
						$_field['id']			= 'blog-settings-social-layout';
						$_field['default']		= app_setting( $_field['key'], 'blog' ) ? app_setting( $_field['key'], 'blog' ) : 'HORIZONTAL';

						$_options				= array();
						$_options['HORIZONTAL']	= 'Horizontal';
						$_options['VERTICAL']	= 'Vertical';
						$_options['SINGLE']		= 'Single Button';

						echo form_field_dropdown( $_field, $_options );

						// --------------------------------------------------------------------------

						$_display = app_setting( $_field['key'], 'blog' ) && app_setting( $_field['key'], 'blog' ) == 'SINGLE' ? 'block' : 'none';

						echo '<div id="blog-settings-social-layout-single-text" style="display:' . $_display . '">';

							$_field					= array();
							$_field['key']			= 'social_layout_single_text';
							$_field['label']		= 'Button Text';
							$_field['default']		= app_setting( $_field['key'], 'blog' ) ? app_setting( $_field['key'], 'blog' ) : 'Share';
							$_field['placeholder']	= 'Specify what text should be rendered on the button';

							echo form_field( $_field );

						echo '</div>';


						// --------------------------------------------------------------------------

						$_field					= array();
						$_field['key']			= 'social_counters';
						$_field['label']		= 'Show Counters';
						$_field['id']			= 'social-counters';
						$_field['default']		= app_setting( $_field['key'], 'blog' ) ? TRUE : FALSE;

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
				<?=form_open( NULL, 'style="margin-bottom:0;"')?>
				<?=form_hidden( 'update', 'sidebar' )?>
				<p>
					Configure the sidebar widgets.
				</p>
				<hr />
				<fieldset id="blog-settings-blog-sidebar">
					<legend>Enable Widgets</legend>
					<?php

						$_field					= array();
						$_field['key']			= 'sidebar_latest_posts';
						$_field['label']		= 'Latest Posts';
						$_field['default']		= app_setting( $_field['key'], 'blog' ) ? TRUE : FALSE;

						echo form_field_boolean( $_field );

						// --------------------------------------------------------------------------

						if ( app_setting( 'categories_enabled', 'blog' ) ) :

							$_field					= array();
							$_field['key']			= 'sidebar_categories';
							$_field['label']		= 'Categories';
							$_field['default']		= app_setting( $_field['key'], 'blog' ) ? TRUE : FALSE;

							echo form_field_boolean( $_field );

						endif;

						// --------------------------------------------------------------------------

						if ( app_setting( 'tags_enabled', 'blog' ) ) :

							$_field					= array();
							$_field['key']			= 'sidebar_tags';
							$_field['label']		= 'Tags';
							$_field['default']		= app_setting( $_field['key'], 'blog' ) ? TRUE : FALSE;

							echo form_field_boolean( $_field );

						endif;

						// --------------------------------------------------------------------------

						$_field					= array();
						$_field['key']			= 'sidebar_popular_posts';
						$_field['label']		= 'Popular Posts';
						$_field['default']		= app_setting( $_field['key'], 'blog' ) ? TRUE : FALSE;

						echo form_field_boolean( $_field );

						$_associations = $this->config->item( 'blog_post_associations' );

						if ( is_array( $_associations ) ) :

							foreach( $_associations AS $assoc ) :

								$_field					= array();
								$_field['key']			= 'sidebar_association_' . $assoc->slug;
								$_field['label']		= $assoc->sidebar_title;
								$_field['default']		= app_setting( $_field['key'], 'blog' ) ? TRUE : FALSE;

								echo form_field_boolean( $_field );

							endforeach;

						endif;

					?>
				</fieldset>
				<p style="margin-top:1em;margin-bottom:0;">
					<?=form_submit( 'submit', lang( 'action_save_changes' ), 'style="margin-bottom:0;"' )?>
				</p>
				<?=form_close()?>
			</div>

		</section>
</div>