<?php

	if ( $rendered_page['hero'] ) :

		?>
		<div class="row">
			<div class="sixteen columns first last">
				<div id="cms-page-hero">
					<?=$rendered_page['hero']?>
				</div>
			</div>
		</div>
		<?php

	endif;

?>