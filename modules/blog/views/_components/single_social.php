<?php

	//	Defaults
	$_layout		= '';
	$_single_title	= app_setting( 'social_layout_single_text', 'blog' ) ? app_setting( 'social_layout_single_text', 'blog' ) : 'Share';
	$_counters		= app_setting( 'social_counters', 'blog' ) ? 'data-zeroes="yes"' : 'data-counters="no"';
	$_twitter_via	= app_setting( 'social_twitter_via', 'blog' ) ? app_setting( 'social_twitter_via', 'blog' ) : '';

	// --------------------------------------------------------------------------

	//	Layout
	switch( app_setting( 'social_layout', 'blog' ) ) :

		case 'HORIZONTAL' :	$_layout = '';						break;
		case 'VERTICAL' :	$_layout = 'social-likes_vertical';	break;
		case 'SINGLE' :		$_layout = 'social-likes_single';	break;

	endswitch;

	$_enabled = array();
	$_enabled[]	= app_setting( 'social_facebook_enabled', 'blog' ) ? '<div class="facebook" title="Share link on Facebook">Facebook</div>' : '';
	$_enabled[] = app_setting( 'social_twitter_enabled', 'blog' ) ? '<div class="twitter" data-via="' . $_twitter_via . '" title="Share link on Twitter">Twitter</div>' : '';
	$_enabled[] = app_setting( 'social_googleplus_enabled', 'blog' ) ? '<div class="plusone" title="Share link on Google+">Google+</div>' : '';
	$_enabled[] = app_setting( 'social_pinterest_enabled', 'blog' ) && $post->image_id ? '<div class="pinterest" data-media="' . cdn_serve( $post->image_id ) . '" title="Share image on Pinterest">Pinterest</div>' : '';

	$_enabled = array_filter( $_enabled );

	if ( $_enabled ) :

		echo '<hr />';
		echo '<div class="social-likes ' . $_layout . '" ' . $_counters . ' data-url="' . $post->url . '" data-single-title="' . $_single_title . '" data-title="' . $post->title . '">';
		foreach ( $_enabled AS $enabled ) :

			echo $enabled;

		endforeach;
		echo '</div>';

	endif;