<?php

	echo '<ul class="tags list-inline list-unstyled">';

		echo '<li class="title">Tags:</li>';

		foreach ( $post->tags AS $tag ) :

			echo '<li class="tag">';
			echo anchor( $tag->url, $tag->label );
			echo '</li>';

		endforeach;

	echo '</ul>';