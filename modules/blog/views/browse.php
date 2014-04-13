<div class="container">
<?php

	echo '<ul class="posts col-md-9 col-md-push-3 list-unstyled">';

	// --------------------------------------------------------------------------

	//	Render Posts
	foreach ( $posts AS $post ) :

		echo '<li class="post clearfix">';
		if ( $post->image_id ) :

			echo '<div class="img featured-image col-md-3 text-center">';
				echo anchor( $blog_url . $post->slug, img( array( 'src' => cdn_scale( $post->image_id, 300, 300 ), 'class' => 'thumbnail img-responsive center-block' ) ) );
			echo '</div>';
			echo '<div class="col-md-9">';

		endif;
		echo '<h2 class="title">' . anchor( $blog_url . $post->slug, $post->title ) . '</h2>';
		echo '<p class="date-author">';
			echo 'Published ' . date( 'jS F Y, H:i', strtotime( $post->published ) ) . ', ';
			echo 'by ' . $post->author->first_name . ' ' . $post->author->last_name;
		echo '</p>';

		//	Excerpts or not?
		if ( isset( $post->body ) ) :

			echo $post->body;

		else :

			echo '<p class="excerpt">';
				echo $post->excerpt;
			echo '</p>';
			echo '<p class="meta">';
				echo anchor( $blog_url . $post->slug, 'Read More', 'class="read-more"' );
			echo '</p>';

		endif;


		if ( $post->image_id ) :

			echo '</div>';

		endif;

		echo '</li>';

	endforeach;

	// --------------------------------------------------------------------------

	//	Pagination
	$this->load->library('pagination');

	$_config						= array();
	$_config['base_url']			= site_url( blog_setting( 'blog_url' ) );
	$_config['total_rows']			= $pagination->total;
	$_config['per_page']			= $pagination->per_page;
	$_config['use_page_numbers']	= TRUE;
	$_config['use_rsegment']		= TRUE;
	$_config['uri_segment']			= 2;
	$_config['full_tag_open']		= '<li class="pagination">';
	$_config['full_tag_close']		= '</li>';

	$this->pagination->initialize( $_config );

	echo '<li>' . $this->pagination->create_links() . '</li>';

	echo '</ul>';

	// --------------------------------------------------------------------------

	//	Load Sidebar
	$this->load->view( 'blog/sidebar');

?>
</div>