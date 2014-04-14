<?php

	//	Post Featured Image
	if ( $post->image_id ) :

		echo '<div class="img featured-image col-md-3 text-center">';
			$this->load->view( 'blog/_components/browse_featured_image' );
		echo '</div>';

		echo '<div class="col-md-9">';

	endif;

	// --------------------------------------------------------------------------

	//	Post Title
	$this->load->view( 'blog/_components/browse_title' );

	// --------------------------------------------------------------------------

	//	Post Excerpt, or Post Body
	if ( isset( $post->body ) ) :

		$this->load->view( 'blog/_components/browse_body' );

	else :

		$this->load->view( 'blog/_components/browse_excerpt' );

	endif;

	// --------------------------------------------------------------------------

	echo '<hr />';

	if ( $post->image_id ) :

		echo '</div>';

	endif;