<ul>
	<?php

		for ( $i = 0; $i < count( $sitemap ); $i++ ) :

			echo '<li>';
				echo anchor( $sitemap[$i]->location, $sitemap[$i]->title );
			echo '</li>';

		endfor;

	?>
</ul>