<div class="blog archive container">
<?php
	
	//	Sidebar enabled? If it's not we'll adjust columns accordingly
	if ( blog_setting( 'sidebar_enabled' ) ) :

		//	Left hand sidebar?
		if ( blog_setting( 'sidebar_position' ) == 'left' ) :

			$this->load->view( 'blog/sidebar' );

			echo '<ul class="eleven columns offset-by-one last">';

		else :

			echo '<ul class="eleven columns first">';

		endif;

	else :

		echo '<ul class="sixteen columns first last">';

	endif;


	// --------------------------------------------------------------------------

	if ( $posts ) :

		$_year	= '';
		$_month	= '';

		foreach ( $posts AS $post ) :

			//	New Year?
			if ( $_year != date( 'Y', strtotime( $post->published ) ) )  :

				echo '<li class="year">' . date( 'Y', strtotime( $post->published ) ) . '</li>';
				$_year = date( 'Y', strtotime( $post->published ) );

			endif;

			// --------------------------------------------------------------------------

			//	New Month?
			if ( $_month != date( 'm', strtotime( $post->published ) ) )  :

				echo '<li class="month">' . date( 'F', strtotime( $post->published ) ) . '</li>';
				$_month = date( 'm', strtotime( $post->published ) );

			endif;

			// --------------------------------------------------------------------------

			//	Post
			echo '<li class="post">';
			$_img = $post->image ? img( cdn_thumb( $post->image, 32, 32 ) ) : '';
			echo anchor( $post->url, $_img . $post->title );
			echo '</li>';

		endforeach;

	else :

		echo '<li class="no-posts">';
		echo 'No Posts Found';
		echo '</li>';

	endif;

	// --------------------------------------------------------------------------

	echo '</ul>';
	
	// --------------------------------------------------------------------------
	
	if ( blog_setting( 'sidebar_enabled' ) && blog_setting( 'sidebar_position' ) == 'right' ) :

		$this->load->view( 'blog/sidebar' );

	endif;

?>
</div>