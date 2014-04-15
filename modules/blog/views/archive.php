<div class="blog archive container">
<?php

	echo '<ul class="posts ' . BS_COL_MD_9 . ' ' . BS_COL_MD_PUSH_3 . ' list-unstyled">';

	// --------------------------------------------------------------------------

	if ( $posts ) :

		$_year	= '';
		$_month	= '';

		foreach ( $posts AS $post ) :

			$this->load->view( 'blog/_components/archive', array( 'post' => &$post, '_year' => &$_year, '_month' => &$_month ) );

			$_year	= date( 'Y', strtotime( $post->published ) );
			$_month	= date( 'm', strtotime( $post->published ) );

		endforeach;

	else :

		echo '<li class="no-posts">';
		echo 'No Posts Found';
		echo '</li>';

	endif;

	// --------------------------------------------------------------------------

	echo '</ul>';

	//	Load Sidebar
	$this->load->view( 'blog/_components/sidebar' );

?>
</div>