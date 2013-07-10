<div class="blog browse container">
<?php
	
	//	Sidebar enabled? If it's not we'll adjust columns accordingly
	if ( blog_setting( 'sidebar_enabled' ) ) :

		//	Left hand sidebar?
		if ( blog_setting( 'sidebar_position' ) == 'left' ) :

			$this->load->view( 'blog/sidebar' );

			echo '<ul class="posts eleven columns offset-by-one last">';

		else :

			echo '<ul class="posts eleven columns first">';

		endif;

	else :

		echo '<ul class="posts sixteen columns first last">';

	endif;

	// --------------------------------------------------------------------------

	//	Render Posts
	foreach ( $posts AS $post ) :
	
		echo '<li class="post clearfix">';
		if ( $post->image ) :
		
			echo '<div class="img three columns first featured-image">';
			echo anchor( $blog_url . $post->slug, img( array( 'src' => cdn_scale( $post->image, 200, 200 ), 'class' => 'scale-with-grid' ) ) );
			echo '</div>';
			echo '<div class="eight columns last">';
		
		endif;
		echo '<h2 class="title">' . anchor( $blog_url . $post->slug, $post->title ) . '</h2>';
		echo '<p class="date-author">';
		echo 'Published ' . date( 'jS F Y, H:i', strtotime( $post->published ) ) . ', ';
		echo 'by ' . $post->author->first_name . ' ' . $post->author->last_name;
		echo '</p>';
		echo '<p class="excerpt">' . $post->excerpt . '</p>';
		echo '<p class="meta">';
		echo anchor( $blog_url . $post->slug, 'Read More', 'class="read-more"' );
		echo '</p>';
		
		if ( $post->image ) :
		
			echo '</div>';
		
		endif;
		
		echo '</li>';
	
	endforeach;

	echo '</ul>';
	
	// --------------------------------------------------------------------------
	
	if ( blog_setting( 'sidebar_enabled' ) && blog_setting( 'sidebar_position' ) == 'right' ) :

		$this->load->view( 'blog/sidebar' );

	endif;

?>

	<div class="clearfix"></div>
	
</div>