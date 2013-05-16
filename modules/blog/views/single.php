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
		if ( $post->image ) :
		
			echo '<div class="img four columns first">';
			echo img( array( 'src' => cdn_scale( 'blog', $post->image, 200, 200 ), 'class' => 'scale-with-grid' ) );
			echo '</div>';

			if ( blog_setting( 'sidebar_enabled' ) ) :

				echo '<div class="eight columns last">';

			else :

				echo '<div class="twelve columns last">';

			endif;
		
		endif;
		echo '<h1 class="title">' . $post->title . '</h1>';
		echo '<p class="date-author">';
		echo 'Published ' . date( 'jS F Y, H:i', strtotime( $post->published ) ) . ', ';
		echo 'by ' . $post->author->first_name . ' ' . $post->author->last_name;
		echo '</p>';
		echo '<p class="excerpt">' . $post->excerpt . '</p>';
		echo '<hr />';
		echo '<div class="body">';
		echo $post->body;
		echo '</div>';
		
		if ( $post->image ) :
		
			echo '</div>';
		
		endif;

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
		
		echo '</li>';

	echo '</ul>';
	
	// --------------------------------------------------------------------------
	
	if ( blog_setting( 'sidebar_enabled' ) && blog_setting( 'sidebar_position' ) == 'right' ) :

		$this->load->view( 'blog/sidebar' );

	endif;

?>
</div>