<?php

	//	Post Title
	$this->load->view( 'blog/_components/post_title' );

	// --------------------------------------------------------------------------

	//	Post Gallery
	if ( $post->gallery ) :

		$this->load->view( 'blog/_components/post_gallery' );

	endif;

	// --------------------------------------------------------------------------

	//	Post Featured Image
	if ( $post->image_id ) :

		$this->load->view( 'blog/_components/post_featured_image' );

	endif;

	// --------------------------------------------------------------------------

	//	Post Body
	$this->load->view( 'blog/_components/post_body' );

	// --------------------------------------------------------------------------

	//	Categories & Tags
	if ( ( blog_setting( 'categories_enabled' ) && $post->categories ) || ( blog_setting( 'tags_enabled' ) && $post->tags ) ) :

		echo '<hr />';

	endif;

	if ( blog_setting( 'categories_enabled' ) && $post->categories ) :

		$this->load->view( 'blog/_components/post_categories' );

	endif;

	if ( blog_setting( 'tags_enabled' ) && $post->tags ) :

		$this->load->view( 'blog/_components/post_tags' );

	endif;