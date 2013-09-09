<?php

	//	Left hand sidebar?
	if ( blog_setting( 'sidebar_position' ) == 'left' ) :

		echo '<ul class="sidebar four columns first">';

	else :

		echo '<ul class="sidebar four columns offset-by-one last">';

	endif;

	if ( $widget->latest_posts ) :

		echo '<li class="widget latest-posts">';
		echo $widget->latest_posts;
		echo '</li>';

	endif;

	// --------------------------------------------------------------------------

	if ( blog_setting( 'categories_enabled' ) &&  $widget->categories ) :

		echo '<li class="widget categories">';
		echo $widget->categories;
		echo '</li>';

	endif;

	// --------------------------------------------------------------------------

	if ( blog_setting( 'tags_enabled' ) && $widget->tags ) :

		echo '<li class="widget tags">';
		echo $widget->tags;
		echo '</li>';

	endif;

	// --------------------------------------------------------------------------

	//	Post associations
	if ( isset( $post->associations ) && $post->associations ) :

		foreach ( $post->associations AS $index => $assoc ) :

			if ( $assoc->current ) :

				echo '<li class="widget associations association-' . $index . '">';
				echo '<h5>' . $assoc->widget_title . '</h5>';

				echo '<ul>';
				foreach( $assoc->current AS $current ) :

					echo '<li>' . $current->label . '</li>';

				endforeach;
				echo '</ul>';

				echo '</li>';

			endif;

		endforeach;

	endif;

?>
</ul>