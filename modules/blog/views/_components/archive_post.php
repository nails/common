<?php

	$_img = $post->image_id ? img( cdn_thumb( $post->image_id, 32, 32 ) ) : '';
	echo anchor( $post->url, $_img . $post->title );