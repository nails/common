<?php

	if ( $datetime && $datetime != '0000-00-00 00:00:00' ) :

		echo '<td class="datetime">';
		echo '<span class="nice-time">' . $datetime . '</span>';
		echo '<small>' . date( active_user( 'pref_date_format' ) . ' ' . active_user( 'pref_time_format' ), strtotime( $datetime ) ) . '</small>';
		echo '</td>';

	else :

		if ( isset( $nodata ) ) :

			echo '<td class="datetime no-data">' . $nodata . '</td>';

		else :

			echo '<td class="datetime no-data">&mdash;</td>';

		endif;

	endif;