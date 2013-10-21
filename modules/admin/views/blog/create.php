<div class="group-blog create">

	<?=form_open( NULL, 'id="post-form"' )?>

		<ul class="tabs">
			<li class="tab active">
				<a href="#" data-tab="tab-meta" id="tabber-meta">Basic Information</a>
			</li>
			<li class="tab">
				<a href="#" data-tab="tab-body" id="tabber-body">Body</a>
			</li>

			<?php if ( blog_setting( 'categories_enabled' ) ) : ?>
			<li class="tab">
				<a href="#" data-tab="tab-categories" id="tabber-categories">Categories</a>
			</li>
			<?php endif; ?>

			<?php if ( blog_setting( 'tags_enabled' ) ) : ?>
			<li class="tab">
				<a href="#" data-tab="tab-tags" id="tabber-tags">Tags</a>
			</li>
			<?php endif; ?>

			<?php if ( $associations ) : ?>
			<li class="tab">
				<a href="#" data-tab="tab-associations" id="tabber-associations">Associations</a>
			</li>
			<?php endif; ?>

			<li class="tab">
				<a href="#" data-tab="tab-gallery" id="tabber-gallery">Gallery</a>
			</li>
			<li class="tab">
				<a href="#" data-tab="tab-seo" id="tabber-seo">SEO</a>
			</li>
		</ul>
		<section class="tabs pages">

			<div class="tab page fieldset" id="tab-meta">
				<?php

				//	Published
				$_field					= array();
				$_field['key']			= 'is_published';
				$_field['label']		= 'Published';
				$_field['text_on']		= 'YES';
				$_field['text_off']		= 'NO';
				$_field['default']		= TRUE;
				$_field['required']		= TRUE;

				echo form_field_boolean( $_field, array( 'No', 'Yes' ) );

				// --------------------------------------------------------------------------

				//	Title
				$_field					= array();
				$_field['key']			= 'title';
				$_field['label']		= 'Title';
				$_field['required']		= TRUE;
				$_field['placeholder']	= 'The title of the post';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				//	Excerpt
				$_field					= array();
				$_field['key']			= 'excerpt';
				$_field['type']			= 'textarea';
				$_field['label']		= 'Excerpt';
				$_field['required']		= TRUE;
				$_field['placeholder']	= 'A short excerpt of the post, this will be shown in locations where a summary is required.';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				//	Featured Image
				$_field					= array();
				$_field['key']			= 'image_id';
				$_field['label']		= 'Featured Image';
				$_field['bucket']		= 'blog';

				echo form_field_mm_image( $_field );

				?>
			</div>

			<div class="tab page" id="tab-body">
				<p id="body-error" class="system-alert error no-close" style="display:<?=form_error( 'body' ) ? 'block' : 'none'?>">
					<?=form_error( 'body', ' ', ' ' )?>
				</p>
				<textarea class="ckeditor" name="body"><?=set_value( 'body' )?></textarea>
				<p class="system-alert notice no-close" style="margin-top:10px;">
					<strong>Note:</strong> The editor's display might not be a true representation of the final layout
					due to application stylesheets on the front end which are not loaded here.
				</p>
			</div>

			<?php if ( blog_setting( 'categories_enabled' ) ) : ?>
				<div class="tab page" id="tab-categories">
					<p>
						Organise your posts and help user's find them by assigning <u rel="tipsy" title="Categories allow for a broad grouping of post topics and should be considered top-level 'containers' for posts of similar content.">categories</u>.
					</p>

					<select name="categories[]" multiple="multiple" class="categories">
					<?php

						$_post_raw	= $this->input->post( 'categories' ) ? $this->input->post( 'categories' ) : array();
						$_post		= array();

						foreach ( $_post_raw AS $key => $value ) :

							$_post[$value] = TRUE;

						endforeach;

						foreach ( $categories AS $category ) :

							$_selected = isset( $_post[$category->id] ) ? 'selected="selected"' : '';
							echo '<option value="' . $category->id . '" ' . $_selected . '>' . $category->label . '</option>';

						endforeach;

					?>
					</select>
				</div>
			<?php endif; ?>

			<?php if ( blog_setting( 'tags_enabled' ) ) : ?>
				<div class="tab page" id="tab-tags">
					<p>
						Organise your posts and help user's find them by assigning <u rel="tipsy" title="Tags are generally used to describe your post in more detail.">tags</u>.
					</p>

					<select name="tags[]" multiple="multiple" class="tags">
					<?php

						$_post_raw	= $this->input->post( 'tags' ) ? $this->input->post( 'tags' ) : array();
						$_post		= array();

						foreach ( $_post_raw AS $key => $value ) :

							$_post[$value] = TRUE;

						endforeach;

						foreach ( $tags AS $tag ) :

							$_selected = isset( $_post[$tag->id] ) ? 'selected="selected"' : '';
							echo '<option value="' . $tag->id . '" ' . $_selected . '>' . $tag->label . '</option>';

						endforeach;

					?>
					</select>
				</div>
			<?php endif; ?>


			<?php if ( $associations ) : ?>
			<div class="tab page" id="tab-associations">

				<p>
					It's possible for you to associate this blog post with other bits of related content. The following associations can be defined.
				</p>
				<?php

					foreach( $associations AS $index => $assoc ) :

						echo '<fieldset class="association" id="create-blog-post-association-' . $index . '">';
						echo isset( $assoc->legend ) && $assoc->legend ? '<legend>' . $assoc->legend . '</legend>' : '';
						echo isset( $assoc->description ) && $assoc->description ? '<p>' . $assoc->description . '</p>' : '';

						$_multiple = isset( $assoc->multiple ) && $assoc->multiple ? 'multiple="multiple"' : '';

						echo '<select name="associations[' . $index . '][]" ' . $_multiple . '>';

							foreach( $assoc->data AS $data ) :

								echo '<option value="' . $data->id . '">' . $data->label . '</option>';

							endforeach;

						echo '</select>';
						echo '</fiedset>';

					endforeach;

				?>

			</div>
			<?php endif; ?>

			<div class="tab page" id="tab-gallery">
				<p class="system-alert no-close message">
					<strong>Coming soon!</strong> We're working on a simple way of attaching images to your blog posts which can be displayed in an attractive slideshow.
				</p>
			</div>

			<div class="tab page fieldset" id="tab-seo">
				<p>
					These fields are not visible anywhere but help Search Engines index and understand the page.
				</p>
				<?php

				//	Description
				$_field					= array();
				$_field['key']			= 'seo_description';
				$_field['type']			= 'textarea';
				$_field['label']		= 'Description';
				$_field['required']		= TRUE;
				$_field['placeholder']	= 'The post\'s SEO description';

				echo form_field( $_field, 'This should be kept short (< 160 characters) and concise. It\'ll be shown in search result listings and search engines will use it to help determine the post\'s content.' );

				// --------------------------------------------------------------------------

				//	Keywords
				$_field					= array();
				$_field['key']			= 'seo_keywords';
				$_field['label']		= 'Keywords';
				$_field['required']		= TRUE;
				$_field['placeholder']	= 'Comma separated keywords relating to the content of the post.';

				echo form_field( $_field, 'SEO good practice recommend keeping the number of keyword phrases below 10 and less than 160 characters in total.' );

				?>
			</div>

		</section>

		<p>
			<?=form_submit( 'submit', lang( 'action_create' ) )?>
		</p>

	<?=form_close()?>
</div>
<script type="text/javascript">
	var _CREATE;
	$(function(){

		_CREATE	= new NAILS_Admin_Blog_Create_Edit();
		_CREATE.init( '_CREATE', '<?=$this->cdn->generate_api_upload_token( active_user( 'id' ) ) ?>' );

	});
</script>