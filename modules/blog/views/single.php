<div class="blog single">
<?php

	echo '<ul class="posts twelve columns first">';
	
		echo '<li class="post clearfix">';
		if ( $post->image ) :
		
			echo '<div class="img four columns first">';
			echo img( array( 'src' => cdn_scale( 'blog', $post->image, 200, 200 ), 'class' => 'scale-with-grid' ) );
			echo '</div>';
			echo '<div class="eight columns last">';
		
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
		
		echo '</li>';

	echo '</ul>';
	
	// --------------------------------------------------------------------------
	
	$this->load->view( 'blog/sidebar' );

?>
</div>