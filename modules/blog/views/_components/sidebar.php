<ul class="sidebar <?=BS_COL_MD_3?> <?=BS_COL_MD_PULL_9?> list-unstyled">
<?php

	if ( app_setting( 'sidebar_latest_posts', 'blog' ) && $widget->latest_posts ) :

		echo '<li class="widget latest-posts clearfix">';
			echo $widget->latest_posts;
			echo '<hr />';
		echo '</li>';

	endif;

	// --------------------------------------------------------------------------

	if ( app_setting( 'categories_enabled', 'blog' ) && app_setting( 'sidebar_categories', 'blog' ) && $widget->categories ) :

		echo '<li class="widget categories clearfix">';
			echo $widget->categories;
			echo '<hr />';
		echo '</li>';

	endif;

	// --------------------------------------------------------------------------

	if ( app_setting( 'tags_enabled', 'blog' ) && app_setting( 'sidebar_tags', 'blog' ) && $widget->tags ) :

		echo '<li class="widget tags clearfix">';
			echo $widget->tags;
			echo '<hr />';
		echo '</li>';

	endif;

	// --------------------------------------------------------------------------

	//	Post associations
	if ( isset( $post->associations ) && $post->associations ) :

		foreach ( $post->associations AS $assoc ) :

			if ( app_setting( 'sidebar_association_' . $assoc->slug, 'blog' ) && $assoc->current ) :

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

	if ( app_setting( 'sidebar_popular_posts', 'blog' ) && $widget->popular_posts ) :

		echo '<li class="widget popular-posts clearfix">';
			echo $widget->popular_posts;
			echo '<hr />';
		echo '</li>';

	endif;

	// --------------------------------------------------------------------------

	//	RSS
	if ( app_setting( 'rss_enabled', 'blog' ) ) :

		echo '<li class="text-center">';
			echo anchor( app_setting( 'url', 'blog' ) . 'rss', '<span class="ion-social-rss"></span>', 'title="Subscribe via RSS"' );
		echo '<li>';

	endif;

?>
</ul>