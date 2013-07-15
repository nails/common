<div class="group-blog edit">

	<p>
		Use this page to create a new post on site.
	</p>
	
	<hr />
	
	<?=form_open()?>
	
		<fieldset id="edit-post-meta">
			<legend>Meta Data</legend>
			<?php
			
			//	Published
			$_field					= array();
			$_field['key']			= 'is_published';
			$_field['label']		= 'Published';
			$_field['required']		= TRUE;
			$_field['text_on']		= 'YES';
			$_field['text_off']		= 'NO';
			$_field['default']		= $post->is_published;
			
			echo form_field_boolean( $_field );
			
			// --------------------------------------------------------------------------
			
			//	Title
			$_field					= array();
			$_field['key']			= 'title';
			$_field['label']		= 'Title';
			$_field['required']		= TRUE;
			$_field['default']		= $post->title;
			$_field['placeholder']	= 'The title of the post';
			
			echo form_field( $_field );
			
			// --------------------------------------------------------------------------
			
			//	Excerpt
			$_field					= array();
			$_field['key']			= 'excerpt';
			$_field['type']			= 'textarea';
			$_field['label']		= 'Excerpt';
			$_field['required']		= TRUE;
			$_field['default']		= $post->excerpt;
			$_field['placeholder']	= 'A short excerpt of the post, this will be shown first and in locations where a summary is required.';
			
			echo form_field( $_field );
			
			// --------------------------------------------------------------------------
			
			//	Featured Image
			$_field					= array();
			$_field['key']			= 'image_id';
			$_field['label']		= 'Featured Image';
			$_field['default']		= $post->image_id;
			$_field['bucket']		= 'blog';
			
			echo form_field_mm_image( $_field );
			
			?>
		</fieldset>
		
		<fieldset id="edit-post-body">
			<legend>Post Body</legend>
			<?=form_error( 'body', '<p class="system-alert error no-close">', '</p>' )?>
			<textarea class="ckeditor" name="body"><?=set_value( 'body', $post->body )?></textarea>
			<p class="system-alert notice no-close" style="margin-top:10px;">
				<strong>Note:</strong> The editor's display might not be a true representation of the final layout
				due to application stylesheets on the front end which are not loaded here.
			</p>
		</fieldset>
		<?php 

		if ( blog_setting( 'categories_enabled' ) || blog_setting( 'tags_enabled' ) ) :

			echo '<fieldset id="create-post-cats-tags" class="categories-tags">';

			if ( blog_setting( 'categories_enabled' ) && blog_setting( 'tags_enabled' ) ) :

				echo '<legend>Categories &amp; Tags</legend>';

			elseif ( blog_setting( 'categories_enabled' ) ) :

				echo '<legend>Categories</legend>';

			elseif ( blog_setting( 'tagss_enabled' ) ) :

				echo '<legend>Tags</legend>';

			endif;


			// --------------------------------------------------------------------------

			echo '<p>';

			echo 'Organise your posts and help user\'s find them by assigning ';

			if ( blog_setting( 'categories_enabled' ) ) :

				echo '<u rel="tipsy" title="Categories allow for a broad grouping of post topics and should be considered top-level \'containers\' for posts of similar content.">categories</u> ';

				if ( blog_setting( 'tags_enabled' ) ) :

					echo 'and ';

				else :

					echo '.';

				endif;

			endif;


			if ( blog_setting( 'tags_enabled' ) ) :

				echo '<u rel="tipsy" title="Tags are similar to categories, but they are generally used to describe your post in more detail.">tags</u>.';

			endif;

			echo '</p>';

			// --------------------------------------------------------------------------

			if ( blog_setting( 'categories_enabled' ) ) :

			?>
			<fieldset class="categories">
				<legend>Categories</legend>
				<select name="categories[]" multiple="multiple" class="chosen">
				<?php

					$_post_cats = array();
					foreach ( $post->categories AS $cat ) :

						$_post_cats[] = $cat->id;

					endforeach;

					$_post_raw	= $this->input->post( 'categories' ) ? $this->input->post( 'categories' ) : $_post_cats;
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
				<p>
					<small><?=anchor( 'admin/blog/manager_category', 'Manage Categories', 'data-fancybox-type="iframe" class="fancybox"')?></small>
				</p>
			</fieldset>
			<?php endif; ?>

			<?php if ( blog_setting( 'tags_enabled' ) ) : ?>
			<fieldset class="tags">
				<legend>Tags</legend>
				<select name="tags[]" multiple="multiple" class="chosen">
				<?php

					$_post_tags = array();
					foreach ( $post->tags AS $tag ) :

						$_post_tags[] = $tag->id;

					endforeach;

					$_post_raw	= $this->input->post( 'tags' ) ? $this->input->post( 'tags' ) : $_post_tags;
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
				<p>
					<small><?=anchor( 'admin/blog/manager_tag', 'Manage Tags', 'data-fancybox-type="iframe" class="fancybox"')?></small>
				</p>
			</fieldset>
			<?php endif; ?>

			<div class="clearfix"></div>
		</fieldset>
		<?php endif; ?>

		<fieldset id="edit-post-seo">
			<legend>Search Engine Optimisation</legend>
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
			$_field['default']		= $post->seo_description;
			$_field['placeholder']	= 'The post\'s SEO description';
			
			echo form_field( $_field, 'This should be kept short (< 160 characters) and concise. It\'ll be shown in search result listings and search engines will use it to help determine the post\'s content.' );
			
			// --------------------------------------------------------------------------
			
			//	Keywords
			$_field					= array();
			$_field['key']			= 'seo_keywords';
			$_field['label']		= 'Keywords';
			$_field['required']		= TRUE;
			$_field['default']		= $post->seo_keywords;
			$_field['placeholder']	= 'Comma separated keywords relating to the content of the post.';
			
			echo form_field( $_field, 'SEO good practice recommend keeping the number of keyword phrases below 10 and less than 160 characters in total.' );
			
			?>
		</fieldset>
		
		<p>
			<?=form_submit( 'submit', lang( 'action_save_changes' ) )?>
		</p>
	
	<?=form_close()?>
</div>
<script style="text/javascript">
<!--//

	$(function(){
	
		$( 'select.chosen' ).chosen({
			'no_results_text' : 'Add items using the manager. No results for'
		});
	
	});

	function rebuild_select( id, options)
	{
		//	Take a note of the currently selected items
		var _selected = $( 'select[name="' + id + '[]"] option:selected' );

		//	Empty the list
		$( 'select[name="' + id + '[]"] option' ).remove();

		//	Repopulate the list, marking as selected if needed
		var _opt;

		for ( i=0;i<options.length;i++)
		{
			_opt = $( '<option>' ).val( options[i].id ).html( options[i].label );

			//	Test to see if this tag should be selected or not
			for (x=0;x<_selected.length;x++)
			{
				if ( options[i].id == $(_selected[x]).val() )
				{
					_opt.attr( 'selected', 'selcted' );
					break;
				}
			}

			$( 'select[name="' + id + '[]"]' ).append( _opt );
		}

		$( 'select.chosen' ).trigger( 'liszt:updated' );
	}

//-->
</script>