<?php

	//	Post Title
	$this->load->view( 'blog/_components/single_title' );

	// --------------------------------------------------------------------------

	//	Post Gallery
	if ( $post->gallery ) :

		$this->load->view( 'blog/_components/single_gallery' );

	endif;

	// --------------------------------------------------------------------------

	//	Post Featured Image
	if ( $post->image_id ) :

		$this->load->view( 'blog/_components/single_featured_image' );

	endif;

	// --------------------------------------------------------------------------

	//	Post Body
	$this->load->view( 'blog/_components/single_body' );

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