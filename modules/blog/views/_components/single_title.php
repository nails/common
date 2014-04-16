<?php

	echo '<h2 class="title">' . $post->title . '</h2>';
	echo '<p class="date-author">';

	if ( $post->is_published ) :

		echo 'Published ' . date( 'jS F Y, H:i', strtotime( user_mysql_datetime( $post->published ) ) ) . ', ';
		echo 'by ' . $post->author->first_name . ' ' . $post->author->last_name;

	else :

		echo 'This post has not yet been published.';

	endif;

	echo '</p>';