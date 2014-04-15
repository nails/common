<div class="blog browse container">
<?php

	echo '<ul class="posts ' . BS_COL_MD_9 . ' ' . BS_COL_MD_PUSH_3 . ' list-unstyled">';

	// --------------------------------------------------------------------------

	//	Render Posts
	foreach ( $posts AS $post ) :

		echo '<li class="post clearfix">';
			$this->load->view( 'blog/_components/browse', array( 'post' => &$post ) );
		echo '</li>';

	endforeach;

	// --------------------------------------------------------------------------

	//	Pagination
	$this->load->view( 'blog/_components/browse_pagination' );

	echo '</ul>';

	// --------------------------------------------------------------------------

	//	Load Sidebar
	$this->load->view( 'blog/_components/sidebar');

?>
</div>