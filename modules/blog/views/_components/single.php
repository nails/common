<?php

	//	Post Title
	$this->load->view( 'blog/_components/single_title' );

	// --------------------------------------------------------------------------

	//	Post Featured Image
	if ( $post->image_id ) :

		$this->load->view( 'blog/_components/single_featured_image' );

	endif;

	// --------------------------------------------------------------------------

	//	Post Gallery
	if ( $post->gallery ) :

		$this->load->view( 'blog/_components/single_gallery' );

	endif;

	// --------------------------------------------------------------------------

	//	Post Body
	$this->load->view( 'blog/_components/single_body' );

	// --------------------------------------------------------------------------

	//	Post Social Tools
	if ( app_setting( 'social_enabled', 'blog' ) ) :

		$this->load->view( 'blog/_components/single_social' );

	endif;

	// --------------------------------------------------------------------------

	//	Categories & Tags
	if ( ( app_setting( 'categories_enabled', 'blog' ) && $post->categories ) || ( app_setting( 'tags_enabled', 'blog' ) && $post->tags ) ) :

		echo '<hr />';

	endif;

	if ( app_setting( 'categories_enabled', 'blog' ) && $post->categories ) :

		$this->load->view( 'blog/_components/single_categories' );

	endif;

	if ( app_setting( 'tags_enabled', 'blog' ) && $post->tags ) :

		$this->load->view( 'blog/_components/single_tags' );

	endif;

	// --------------------------------------------------------------------------

	//	Post comments
	if ( app_setting( 'comments_enabled', 'blog' ) ) :

		$this->load->view( 'blog/_components/single_comments' );

	endif;