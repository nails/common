<ul class="sidebar <?=BS_COL_MD_3?> <?=BS_COL_MD_PULL_9?> list-unstyled">
<?php

	if ( blog_setting( 'sidebar_latest_posts' ) && $widget->latest_posts ) :

		echo '<li class="widget latest-posts clearfix">';
			echo $widget->latest_posts;
			echo '<hr />';
		echo '</li>';

	endif;

	// --------------------------------------------------------------------------

	if ( blog_setting( 'categories_enabled' ) && blog_setting( 'sidebar_categories' ) &&$widget->categories ) :

		echo '<li class="widget categories clearfix">';
			echo $widget->categories;
			echo '<hr />';
		echo '</li>';

	endif;

	// --------------------------------------------------------------------------

	if ( blog_setting( 'tags_enabled' ) && blog_setting( 'sidebar_tags' ) &&$widget->tags ) :

		echo '<li class="widget tags clearfix">';
			echo $widget->tags;
			echo '<hr />';
		echo '</li>';

	endif;

	// --------------------------------------------------------------------------

	//	Post associations
	if ( isset( $post->associations ) && $post->associations ) :

		foreach ( $post->associations AS $assoc ) :

			if ( blog_setting( 'sidebar_association_' . $assoc->slug ) && $assoc->current ) :

				echo '<li class="widget associations association-' . $assoc->slug . ' clearfix">';
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

				echo '<hr />';
				echo '</li>';

			endif;

		endforeach;

	endif;

	if ( blog_setting( 'sidebar_popular_posts' ) && $widget->popular_posts ) :

		echo '<li class="widget popular-posts clearfix">';
			echo $widget->popular_posts;
			echo '<hr />';
		echo '</li>';

	endif;

	// --------------------------------------------------------------------------

	//	RSS
	if ( blog_setting( 'rss_enabled' ) ) :

		echo '<li class="text-center">';
			echo anchor( blog_setting( 'blog_url' ) . 'rss', '<span class="ion-social-rss"></span>', 'title="Subscribe via RSS"' );
		echo '<li>';

	endif;

?>
</ul>