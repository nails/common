<?php

	echo '<p class="excerpt">';
		echo $post->excerpt;
	echo '</p>';
	echo '<p class="meta">';
		echo anchor( $post->url, 'Read More', 'class="read-more"' );
	echo '</p>';