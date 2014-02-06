<div class="blog browse container">
<?php

	//	Sidebar enabled? If it's not we'll adjust columns accordingly
	if ( blog_setting( 'sidebar_enabled' ) ) :

		//	Left hand sidebar?
		if ( blog_setting( 'sidebar_position' ) == 'left' ) :

			echo $this->load->view( 'blog/sidebar', NULL, TRUE );

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
		if ( $post->image_id ) :

			echo '<div class="img three columns first featured-image">';
			echo anchor( $blog_url . $post->slug, img( array( 'src' => cdn_scale( $post->image_id, 200, 200 ), 'class' => 'scale-with-grid' ) ) );
			echo '</div>';
			echo '<div class="eight columns last">';

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

			echo '<p class="excerpt">' . $post->excerpt . '</p>';
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

	echo $this->pagination->create_links();

	// --------------------------------------------------------------------------

	echo '</ul>';

	if ( blog_setting( 'sidebar_enabled' ) && blog_setting( 'sidebar_position' ) == 'right' ) :

		echo $this->load->view( 'blog/sidebar', NULL, TRUE );

	endif;

?>

	<div class="clearfix"></div>

</div>