<?php

	//	Defaults
	$_layout		= '';
	$_single_title	= blog_setting( 'social_layout_single_text' ) ? blog_setting( 'social_layout_single_text' ) : 'Share';
	$_counters		= blog_setting( 'social_counters' ) ? 'data-zeroes="yes"' : 'data-counters="no"';
	$_twitter_via	= blog_setting( 'social_twitter_via' ) ? blog_setting( 'social_twitter_via' ) : '';

	// --------------------------------------------------------------------------

	//	Layout
	switch( blog_setting( 'social_layout' ) ) :

		case 'HORIZONTAL' :	$_layout = '';						break;
		case 'VERTICAL' :	$_layout = 'social-likes_vertical';	break;
		case 'SINGLE' :		$_layout = 'social-likes_single';	break;

	endswitch;

?>
<hr />
<div class="social-likes <?=$_layout?>" <?=$_counters?> data-url="<?=$post->url?>" data-single-title="<?=$_single_title?>" data-title="<?=$post->title?>">
	<?=blog_setting( 'social_facebook_enabled' ) ? '<div class="facebook" title="Share link on Facebook">Facebook</div>' : '';?>
	<?=blog_setting( 'social_twitter_enabled' ) ? '<div class="twitter" data-via="' . $_twitter_via . '" title="Share link on Twitter">Twitter</div>' : '';?>
	<?=blog_setting( 'social_googleplus_enabled' ) ? '<div class="plusone" title="Share link on Google+">Google+</div>' : '';?>
	<?=blog_setting( 'social_pinterest_enabled' ) && $post->image_id ? '<div class="pinterest" data-media="' . cdn_serve( $post->image_id ) . '" title="Share image on Pinterest">Pinterest</div>' : '';?>
</div>