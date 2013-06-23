<?php

	if ( $datetime && $datetime != '0000-00-00 00:00:00' ) :

		echo '<td class="datetime">';
		echo '<span class="nice-time">' . user_date( $datetime, 'Y-m-d', 'H:i:s' ) . '</span>';
		echo '<small>' . user_date( $datetime ) . '</small>';
		echo '</td>';

	else :

		if ( isset( $nodata ) ) :

			echo '<td class="datetime no-data">' . $nodata . '</td>';

		else :

			echo '<td class="datetime no-data">&mdash;</td>';

		endif;

	endif;