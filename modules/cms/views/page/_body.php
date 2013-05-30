<?php

	if ( $rendered_page['body'] ) :

		switch( $position ) :

			case 'left' :

				echo '<div class="twelve columns first">';

			break;

			case 'right' :

				echo '<div class="twelve columns last">';

			break;

			case 'full-width' :

				echo '<div class="sixteen columns first last">';

			break;

		endswitch;

	?>
		<div id="cms-page-body">
			<?=$rendered_page['body']?>
		</div>
	</div>
	<?php

	endif;

?>