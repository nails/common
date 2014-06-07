<?php

	echo '<ul class="categories list-inline list-unstyled">';

		echo '<li class="title">Categories:</li>';

		foreach ( $post->categories AS $cat ) :

			echo '<li class="category">';
			echo anchor( $cat->url, $cat->label );
			echo '</li>';

		endforeach;

	echo '</ul>';