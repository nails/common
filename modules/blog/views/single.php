<div class="blog single container">
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
	
		echo '<li class="post clearfix">';

		// --------------------------------------------------------------------------

		echo '<h1 class="title">' . $post->title . '</h1>';
		echo '<p class="date-author">';

		if ( $post->is_published ) :

			echo 'Published ' . date( 'jS F Y, H:i', strtotime( $post->published ) ) . ', ';
			echo 'by ' . $post->author->first_name . ' ' . $post->author->last_name;

		else :

			echo 'This post has not  yet been published.';

		endif;

		echo '</p>';
		echo '<p class="excerpt">' . $post->excerpt . '</p>';
		echo '<hr />';

		// --------------------------------------------------------------------------

		//	Is there a gallery? And is it at the top?
		if ( $post->gallery && $post->gallery_position == 'top' ) :

			$this->load->view( 'blog/_single_gallery' );

		endif;

		// --------------------------------------------------------------------------

		echo '<div class="body clearfix">';
		if ( $post->image ) :
		
			echo img( array( 'src' => cdn_scale( $post->image, 200, 200 ), 'class' => 'featured-img' ) );
		
		endif;
		echo $post->body;
		echo '</div>';

		// --------------------------------------------------------------------------

		//	Categories & Tags

		if ( blog_setting( 'categories_enabled' ) && $post->categories ) :

			echo '<ul class="categories">';

				echo '<li class="label">Categories:</li>';
				
				foreach ( $post->categories AS $cat ) :

					echo '<li class="category">';
					echo anchor( $blog_url . 'category/' . $cat->slug, $cat->label );
					echo '</li>';

				endforeach;

			echo '</ul>';

		endif;

		if ( blog_setting( 'tags_enabled' ) && $post->tags ) :

			echo '<ul class="tags">';

				echo '<li class="label">Tags:</li>';
				
				foreach ( $post->tags AS $tag ) :

					echo '<li class="tag">';
					echo anchor( $blog_url . 'tag/' . $tag->slug, $tag->label );
					echo '</li>';

				endforeach;

			echo '</ul>';

		endif;

		// --------------------------------------------------------------------------

		//	Is there a gallery? And is it at the top?
		if ( $post->gallery && $post->gallery_position == 'bottom' ) :

			$this->load->view( 'blog/_single_gallery' );

		endif;
		
		echo '</li>';

	echo '</ul>';
	
	// --------------------------------------------------------------------------
	
	if ( blog_setting( 'sidebar_enabled' ) && blog_setting( 'sidebar_position' ) == 'right' ) :

		$this->load->view( 'blog/sidebar' );

	endif;

?>
</div>