<!--	CUSTOM CSS	-->
<style type="text/css">
	
	label {
		min-width:50px;
	}
	
	.leftCol
	{
		vertical-align:top;
		width:685px;
	}
	
	.rightCol
	{
		vertical-align:top;
		width:200px;
	}
	
	.leftCol input[type=text],
	.leftCol textarea
	{
		width:600px;
	}
	
	.rightCol input[type=text],
	.rightCol textarea
	{
		width:200px;
	}
	
	.rightCol textarea
	{
		height: 68px;
	}
	
	td.mceIframeContainer.mceFirst.mceLast
	{
		border-bottom:1px solid #ccc !important;
		height:336px;
	}
	
	td.mceIframeContainer.mceFirst.mceLast iframe
	{
		height:336px !important;
	}
	
	.button
	{
		width:190px;
		text-align:center;
	}
	
	.button input
	{
		display:none;
	}
	
	div.uploader {
		width: 215px;
	}
	
	div.uploader span.filename
	{
		width:107px;
	}
	

</style>

<!--	CUSTOM JS	-->
<script type="text/javascript">

	var year = <?=date('Y')?>;
	var month = <?=date('m')?>;
	var day = <?=date('d')?>;
	
	$(function() {
		
		$('#title').change(function() {
			//	parse into a suitable slug
			$('#slug').val(prep_string($(this).val()));
			$( '#date' ).html( year + '/' + month + '/' + day + '/');
		});
		
		$('#slug').blur(function() {
			$(this).val(prep_string($(this).val()));
		});
		
		$('a.back').click( function() {
			return confirm( 'Leaving this page will cause any unsaved edits to be lost.\n\nContinue?' );
		});
		
		$( '*[rel=tooltip-form]' ).tipsy({
			'trigger' : 'focus',
			'html' : true,
			'fade' : true,
			'gravity' : 'n'
		});
		
		$( '#uniform-save_changes' ).tipsy({
			'trigger' : 'hover',
			'html' : true,
			'fade' : true,
			'gravity' : 'n',
			'fallback' : "<strong style=\"text-transform:uppercase\">Save this post as a draft</strong><br />(Unpublishes previously published posts.)"
		});
		
		$( '#uniform-publish_changes' ).tipsy({
			'trigger' : 'hover',
			'html' : true,
			'fade' : true,
			'gravity' : 'n',
			'fallback' : "<strong style=\"text-transform:uppercase\">Publish this post</strong><br />This post will be viewable on the live site."
		});
		
	});
	
	function prep_string(str)
	{
		str = str.replace(/([^a-z0-9-_ ])+/gi, '');
		str = str.replace(/[ ]/g, '-');
		return str.toLowerCase();
	}
	
	function strip_tags (str, allowed_tags)
	{
	 
	    var key = '', allowed = false;
	    var matches = [];    var allowed_array = [];
	    var allowed_tag = '';
	    var i = 0;
	    var k = '';
	    var html = ''; 
	    var replacer = function (search, replace, str) {
	        return str.split(search).join(replace);
	    };
	    // Build allowes tags associative array
	    if (allowed_tags) {
	        allowed_array = allowed_tags.match(/([a-zA-Z0-9]+)/gi);
	    }
	    str += '';
	 
	    // Match tags
	    matches = str.match(/(<\/?[\S][^>]*>)/gi);
	    // Go through all HTML tags
	    for (key in matches) {
	        if (isNaN(key)) {
	                // IE7 Hack
	            continue;
	        }
	 
	        // Save HTML tag
	        html = matches[key].toString();
	        // Is tag not in allowed list? Remove from str!
	        allowed = false;
	 
	        // Go through all allowed tags
	        for (k in allowed_array) {            // Init
	            allowed_tag = allowed_array[k];
	            i = -1;
	 
	            if (i != 0) { i = html.toLowerCase().indexOf('<'+allowed_tag+'>');}
	            if (i != 0) { i = html.toLowerCase().indexOf('<'+allowed_tag+' ');}
	            if (i != 0) { i = html.toLowerCase().indexOf('</'+allowed_tag)   ;}
	 
	            // Determine
	            if (i == 0) {                allowed = true;
	                break;
	            }
	        }
	        if (!allowed) {
	            str = replacer(html, "", str); // Custom replace. No regexing
	        }
	    }
	    return str;
	}
	
	
</script>

<p>
	Compose your new blog post using this form.
	<?=anchor( 'admin/blog', 'Back to Post Listing', 'class="a-button a-button-small right"' )?>
</p>

<hr />

<section>
			
		
	<?=form_open_multipart( 'admin/blog/add' )?>
	<?=form_hidden( 'save', TRUE )?>
	
	<?php
	
		if ( validation_errors() || isset( $fileerror ) ) :
		
			echo '<div class="error" style="margin:10px;">';
			echo '<strong>There were errors:</strong>';
			echo validation_errors();
			echo ( isset( $fileerror ) ) ? $fileerror : NULL;
			echo '</div>';
		
		endif;
	
	?>
	
	<div style="margin-left:10px;margin-right:10px;">
	
		<div class="box specific" style="padding-bottom:10px;">
		
			<h2>Basic Content</h2>
			
			<div style="padding:0 12px;">
			
				<table class="blank">
					<tr>
						<!--	LEFT HAND COL	-->
						<td class="leftCol">
						
							<!--	POST TITLE	-->
							<div style="margin-bottom:5px;">
							
								<?=form_label( 'Title' , 'title' )?>
								<?=form_input( 'title', set_value( 'title' ), 'tabindex="1" id="title" rel="tooltip-form" title="Give your blog post an eye catching title; keep it short and concise!"' );?>
							
							</div>
							
							
							<!--	POST SLUG	-->
							<div style="margin-bottom:5px;">
							
								<?=form_label( 'Slug' , 'url_slug' )?>
								<?=form_input( 'url_slug', set_value( 'url_slug' ), 'id="slug" rel="tooltip-form" title="The Slug is the identifier for this post which is displayed in the URL.<br />Two or more posts published on the same day cannot share the same Slug."' );?>
							
							</div>
							
							
							<!--	POST BODY	-->
							<div style="margin-bottom:5px;">
							
								<?=form_label( 'Body' , 'post_body' )?>
								<?=form_textarea( 'post_body', set_value( 'post_body' ), 'tabindex="2" id="post_body"' );?>
							
							</div>
						
						</td>
					
						<!--	RIGHT HAND COL	-->
						<td class="rightCol">
						
							<?=form_submit( 'submit', lang('blog_create_options_save'), 'id="save_changes"' )?>
							<?=form_submit( 'submit', lang('blog_create_options_publishchanges'), 'id="publish_changes"' )?>
							
							<hr style="margin:5px;" />
							
							<?=form_label( 'Featured Image:' )?>
							<?=form_upload( 'featured_img' )?>
							
							<?=form_label( 'SEO Title:' )?>
							<?=form_input( 'seo_title', set_value( 'seo_title' ), 'rel="tooltip-form" title="<strong>Max 100 characters</strong> - Provide an SEO optimised page title; will only override the normal title in the posts meta tags."' )?>
							
							<?=form_label( 'SEO Description:' )?>
							<?=form_textarea( 'seo_description', set_value( 'seo_description' ), 'rel="tooltip-form" title="<strong>Max 250 characters</strong> - This description may appear on search engine index pages."' )?>
							
							<?=form_label( 'SEO Keywords:' )?>
							<?=form_textarea( 'seo_keywords', set_value( 'seo_keywords' ), 'rel="tooltip-form" title="<strong>Max 250 characters</strong> - Help search engines analyse this page by providing <u>relevant</u> keywords."' )?>
							
						</td>
						
					</tr>
				</table>
				
			</div>
			
		</div>
		
	</div>

	<?=form_close()?>

</section>