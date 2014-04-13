<ul class="sidebar col-md-3 col-md-pull-9 list-unstyled">
<?php

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
				echo '<h5>' . $assoc->widget->title . '</h5>';

				echo '<ul>';
				foreach( $assoc->current AS $item_index => $current ) :

					//	If a callback has been defined and is callable then use that,
					//	otherwise a simple text label will do nicely

					echo '<li class="item-id-' . $item_index . '">';
					if ( isset( $assoc->widget->callback ) && is_callable( $assoc->widget->callback ) ) :

						echo call_user_func( $assoc->widget->callback, $current, $item_index );

					else :

						$current->label;

					endif;
					echo '</li>';

				endforeach;
				echo '</ul>';

				echo '</li>';

			endif;

		endforeach;

	endif;

?>
</ul>