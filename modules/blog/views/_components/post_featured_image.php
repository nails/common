<?php

	echo '<a href="' . cdn_serve( $post->image_id ) . '" class="fancybox" target="_blank">';
		echo img( array( 'src' => cdn_scale( $post->image_id, 350, 350 ), 'class' => 'pull-left thumbnail', 'style' => 'margin-right:1em;margin-bottom:1em;' ) );
	echo '</a>';