<ul>
	<?php

		for ( $i = 0; $i < count( $sitemap->pages ); $i++ ) :

			if ( ! empty( $sitemap->pages[$i]->breadcrumbs ) && count( $sitemap->pages[$i]->breadcrumbs ) - 1 > 0 ) :

				echo '<li>';

					//	Breadcrumbs, fancy it up a little
					$_bc = $sitemap->pages[$i]->breadcrumbs;
					array_pop( $_bc );

					foreach ( $_bc AS $crumb ) :

						echo '<span class="crumb">' . $crumb->title . '</span> ';

					endforeach;

					echo anchor( $sitemap->pages[$i]->location, $sitemap->pages[$i]->title );

				echo '</li>';

			else :

				//	No breadcrumbs, just use basic details
				echo '<li class="top-level">';
					echo anchor( $sitemap->pages[$i]->location, $sitemap->pages[$i]->title );
				echo '</li>';

			endif;

		endfor;

	?>
</ul>