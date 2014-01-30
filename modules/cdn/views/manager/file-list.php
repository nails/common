<?php

	echo '<tr class="file list" data-title="' . $object->filename_display . '" data-id="' . $object->id . '">';

		echo '<td class="filename">';

			if ( $object->is_img ) :

				echo img( cdn_thumb( $object->id, 30, 30 ) );
				$_action_download = 'View';

			else :

				$_action_download = 'Download';

			endif;

			echo $object->filename_display;

		echo '</td>';

		echo '<td class="mime">' . $object->mime . '</td>';

		echo '<td class="filesize">' . format_bytes( $object->filesize ) . '</td>';

		echo '<td class="modified">' . user_datetime( $object->modified ) . '</td>';

		echo '<td class="actions">';

			echo '<a href="#" data-id="' . $object->id . '" data-bucket="' . $bucket->slug .'" data-file="' . $object->filename .'" class="awesome green small insert">Insert</a>';
			echo anchor( site_url( 'cdn/manager/delete/' . $object->id . '?' . $_SERVER['QUERY_STRING'], page_is_secure() ), 'Delete', 'class="awesome red small delete"' );
			echo anchor( cdn_serve( $object->id ), $_action_download, 'class="fancybox awesome small"' );

		echo '</td>';

	echo '</tr>';