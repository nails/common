<?php

	if ( $rendered_page['body'] ) :

		//	Calculate width
		$_width = 16-$page->sidebar_width;

		$_words = array();
		$_words[0]	= 'zero';
		$_words[1]	= 'one';
		$_words[2]	= 'two';
		$_words[3]	= 'three';
		$_words[4]	= 'four';
		$_words[5]	= 'five';
		$_words[6]	= 'six';
		$_words[7]	= 'seven';
		$_words[8]	= 'eight';
		$_words[9]	= 'nine';
		$_words[10]	= 'ten';
		$_words[11]	= 'eleven';
		$_words[12]	= 'twelve';
		$_words[13]	= 'thirteen';
		$_words[14]	= 'fourteen';
		$_words[15]	= 'fifteen';
		$_words[16]	= 'sixteen';

		switch( $position ) :

			case 'left' :

				echo '<div class="' . $_words[$_width] . ' columns first">';

			break;

			case 'right' :

				echo '<div class="' . $_words[$_width] . ' columns last">';

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