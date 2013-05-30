<?php
	
	if ( $rendered_page['sidebar'] ) :

		switch( $position ) :

			case 'left' :

				echo '<div class="four columns first">';

			break;

			case 'right' :

				echo '<div class="four columns last">';

			break;

		endswitch;

	?>
		<div id="cms-page-sidebar">
			<?=$rendered_page['sidebar']?>
		</div>
	</div>
	<?php

	endif;

?>