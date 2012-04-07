<!--	CUSTOM CSS	-->
<style type="text/css">
	
	label {
		min-width:50px;
	}
	
	.rightCol label
	{
		min-width:200px;
		text-align:left;
		float:none;
	}
	
	.leftCol
	{
		vertical-align:top;
		width:685px;
	}
	
	.rightCol
	{
		vertical-align:top;
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
		height:409px;
	}
	
	td.mceIframeContainer.mceFirst.mceLast iframe
	{
		height:409px !important;
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
	
	div.selector
	{
		width:55px;
		position:relative;
		top:-4px;
	}
	div.selector span
	{
		width:28px;
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
		
		
		//	Fancybox for featured img
		$( '.featured-img' ).fancybox();
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


<h1>
	Blog &rsaquo; Edit Post
</h1>

<p>
	Update a post using the form below.
	<?=anchor( 'admin/blog', 'Back to Post Listing', 'class="a-button a-button-small right back"' )?>
	<?=anchor( 'admin/blog/add', 'Create New Post', 'class="a-button a-button-small right back"' )?>
</p>

<hr />

<section>
			
		
	<?=form_open_multipart( 'admin/blog/edit/'.$this->uri->segment( 4 ) )?>
	<?=form_hidden( 'id', $editor->id )?>
	<?=form_hidden( 'slug_orig', $editor->slug )?>
	<?=form_hidden( 'publish_orig', reformat_date( $editor->published, 'Y-m-d' ) )?>
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
								<?=form_input( 'title', set_value( 'title', $editor->title ), 'tabindex="1" id="title" rel="tooltip-form" title="Give your blog post an eye catching title; keep it short and concise!"' );?>
							
							</div>
							
							
							<!--	POST SLUG	-->
							<div style="margin-bottom:5px;">
							
								<?=form_label( 'Slug' , 'url_slug' )?>
								<?=form_input( 'url_slug', set_value( 'url_slug', $editor->slug ), 'id="slug" rel="tooltip-form" title="The Slug is the identifier for this post which is displayed in the URL.<br />Two or more posts published on the same day cannot share the same Slug."' );?>
							
							</div>
							
							
							<!--	POST BODY	-->
							<div style="margin-bottom:5px;">
							
								<?=form_label( 'Body' , 'post_body' )?>
								<?=form_textarea( 'post_body', set_value( 'post_body', $editor->body ), 'tabindex="2" id="post_body"' );?>
							
							</div>
						
						</td>
					
						<!--	RIGHT HAND COL	-->
						<td class="rightCol">
						
							<?=form_submit( 'submit', lang('blog_create_options_save'), 'id="save_changes"' )?>
							<?=form_submit( 'submit', lang('blog_create_options_publishchanges'), 'id="publish_changes"' )?>
							
							<hr style="margin:5px;" />
							
							<?=form_label( 'Publish Date:' )?>
							<?php
							
								$date = explode( '-', $editor->published );
								$date = ( count( $date ) == 1 ) ? array( date( 'Y' ), date( 'm' ), date( 'd H:i:s' ) ) : $date;
								
								$time		= explode( ':', substr( $date[2], 3 ) );
								$date[2]	= substr( $date[2], 0, 2 );
								
								$day_default = ( $this->input->post( 'publish_day' ) ) ? $this->input->post( 'publish_day' ) : $date[2] ;
							?>
							<?=dropdown_days(	'publish_day',		$day_default )?>
							-
							<?=dropdown_months(	'publish_month',	TRUE, $date[1] )?>
							-
							<?=dropdown_years(	'publish_year',		date('Y'), 1970, $date[0] )?>
							
							
							
							<?=form_label( 'Publish time:' )?>
							<?=dropdown_hours(	'publish_hour',		$time[0] )?>
							:
							<?=dropdown_minutes('publish_minute',	NULL, $time[1] )?>
							<?=form_hidden( 	'publish_second',	$time[2] )?>

							
							<hr style="margin:5px;" />
							
							<?php if ( ! empty( $editor->featured_img ) ) : ?>
							<?=form_label( 'New Featured Image:', '', array( 'style' => 'display:inline;float:left;min-width:0px;' ) )?>
							<?=anchor( CDN_SERVER.'blog/featured/'.$editor->featured_img, 'View current', 'class="a-button a-button-small right featured-img" style="position:relative;top:5px;"' )?>
							<?=form_upload( 'featured_img' )?>
							<?php else : ?>
							<?=form_label( 'Featured Image:' )?>
							<?=form_upload( 'featured_img' )?>
							<?php endif; ?>
							
							<?=form_label( 'SEO Title:' )?>
							<?=form_input( 'seo_title', $editor->seo_title, 'rel="tooltip-form" title="<strong>Max 100 characters</strong> - Provide an SEO optimised page title; will only override the normal title in the posts meta tags."' )?>
							
							<?=form_label( 'SEO Description:' )?>
							<?=form_textarea( 'seo_description', $editor->seo_description, 'rel="tooltip-form" title="<strong>Max 250 characters</strong> - This description may appear on search engine index pages."' )?>
							
							<?=form_label( 'SEO Keywords:' )?>
							<?=form_textarea( 'seo_keywords', $editor->seo_keywords, 'rel="tooltip-form" title="<strong>Max 250 characters</strong> - Help search engines analyse this page by providing <u>relevant</u> keywords."' )?>
							
						</td>
					</tr>
				</table>
				
			</div>
			
		</div>
		
	</div>
	
	<?=form_close()?>

</section>