<div class="blog browse container">
<?php

	echo '<ul class="posts ' . BS_COL_MD_9 . ' ' . BS_COL_MD_PUSH_3 . ' list-unstyled">';

	// --------------------------------------------------------------------------

	//	Are we on an 'archive' page? i.e Categories, tags or associations
	if ( ! empty( $archive_title ) ) :

		echo '<li class="archive-title">';
			$this->load->view( 'blog/_components/browse_archive_title' );
		echo '</li>';

	endif;

	// --------------------------------------------------------------------------

	//	Render Posts
	if ( $posts ) :

		foreach ( $posts AS $post ) :

			echo '<li class="post clearfix">';
				$this->load->view( 'blog/_components/browse', array( 'post' => &$post ) );
			echo '</li>';

		endforeach;

	else :

		echo '<li class="no-posts">';
			echo 'No Posts Found';
		echo '</li>';

	endif;

	// --------------------------------------------------------------------------

	//	Pagination
	$this->load->view( 'blog/_components/browse_pagination' );

	echo '</ul>';

	// --------------------------------------------------------------------------

	//	Load Sidebar
	$this->load->view( 'blog/_components/sidebar');

?>
</div>