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
			$_field['default']		= TRUE;
			$_field['required']		= TRUE;
			$_field['default']		= $post->is_published;
			//$_field['class']		= 'chosen';
			
			echo form_field_dropdown( $_field, array( 'No', 'Yes' ) );
			
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
			$_field['key']			= 'image';
			$_field['label']		= 'Featured Image';
			$_field['default']		= $post->image;
			$_field['bucket']		= 'blog';
			
			echo form_field_mm_image( $_field );
			
			?>
		</fieldset>
		
		<fieldset id="edit-post-body">
			<legend>Post Body</legend>
			<textarea class="ckeditor" name="body"><?=set_value( 'body', $post->body )?></textarea>
			<p class="system-alert notice no-close" style="margin-top:10px;">
				<strong>Note:</strong> The editor's display might not be a true representation of the final layout
				due to application stylesheets on the front end which are not loaded here.
			</p>
		</fieldset>
		
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
	
		$( 'select.chosen' ).chosen();
	
	});

//-->
</script>