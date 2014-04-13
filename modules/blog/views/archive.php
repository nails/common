<div class="blog archive container">
<?php

	echo '<ul class="posts col-md-9 col-md-push-3 list-unstyled">';

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
			$_img = $post->image_id ? img( cdn_thumb( $post->image_id, 32, 32 ) ) : '';
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

	//	Load Sidebar
	$this->load->view( 'blog/sidebar' );

?>
</div>