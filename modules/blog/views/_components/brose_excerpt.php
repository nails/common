<?php

	echo '<p class="excerpt">';
		echo $post->excerpt;
	echo '</p>';
	echo '<p class="meta">';
		echo anchor( $blog_url . $post->slug, 'Read More', 'class="read-more"' );
	echo '</p>';