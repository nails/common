<?php

	echo anchor( $blog_url . $post->slug, img( array( 'src' => cdn_scale( $post->image_id, 300, 300 ), 'class' => 'thumbnail img-responsive center-block' ) ) );