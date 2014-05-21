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
	if ( blog_setting( 'social_enabled' ) ) :

		$this->load->view( 'blog/_components/single_social' );

	endif;

	// --------------------------------------------------------------------------

	//	Categories & Tags
	if ( ( blog_setting( 'categories_enabled' ) && $post->categories ) || ( blog_setting( 'tags_enabled' ) && $post->tags ) ) :

		echo '<hr />';

	endif;

	if ( blog_setting( 'categories_enabled' ) && $post->categories ) :

		$this->load->view( 'blog/_components/single_categories' );

	endif;

	if ( blog_setting( 'tags_enabled' ) && $post->tags ) :

		$this->load->view( 'blog/_components/single_tags' );

	endif;

	// --------------------------------------------------------------------------

	//	Post comments
	if ( blog_setting( 'comments_enabled' ) ) :

		$this->load->view( 'blog/_components/single_comments' );

	endif;