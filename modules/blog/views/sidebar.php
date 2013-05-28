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
	
	if (blog_setting( 'categories_enabled' ) &&  $widget->categories ) :

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

?>
</ul>