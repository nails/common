<div class="blog browse">
<?php

	echo '<ul class="posts twelve columns first">';

	foreach ( $posts AS $post ) :
	
		echo '<li class="post clearfix">';
		if ( $post->image ) :
		
			echo '<div class="img four columns first">';
			echo img( array( 'src' => cdn_scale( 'blog', $post->image, 200, 200 ), 'class' => 'scale-with-grid' ) );
			echo '</div>';
			echo '<div class="eight columns last">';
		
		endif;
		echo '<h2 class="title">' . anchor( 'blog/' . $post->slug, $post->title ) . '</h2>';
		echo '<p class="date-author">';
		echo 'Published ' . date( 'jS F Y, H:i', strtotime( $post->published ) ) . ', ';
		echo 'by ' . $post->author->first_name . ' ' . $post->author->last_name;
		echo '</p>';
		echo '<p class="excerpt">' . $post->excerpt . '</p>';
		echo '<p class="meta">';
		echo anchor( 'blog/' . $post->slug, 'Read More', 'class="read-more"' );
		echo '</p>';
		
		if ( $post->image ) :
		
			echo '</div>';
		
		endif;
		
		echo '</li>';
	
	endforeach;

	echo '</ul>';
	
	// --------------------------------------------------------------------------
	
	$this->load->view( 'blog/sidebar' );

?>
</div>