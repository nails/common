<?php

	if ( isset( $object_id ) ) :

		echo '<li class="gallery-item">';
		echo img( cdn_thumb( $object_id, 100, 100 ) );
		echo '<a href="#" class="delete" data-object_id="' . $object_id . '"></a>';
		echo form_hidden( 'gallery[]', $object_id );
		echo '</li>';

	else :

		echo '<li class="gallery-item crunching">';
		echo '<div class="crunching"></div>';
		echo form_hidden( 'gallery[]' );
		echo '</li>';

	endif;