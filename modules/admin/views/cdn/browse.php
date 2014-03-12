<?php

	parse_str( $this->input->server( 'QUERY_STRING' ), $_query );
	$_query = array_filter( $_query );
	$_query = $_query ? '?' . http_build_query( $_query ) : '';
	$_return = $_query ? '?return=' . urlencode( uri_string() . $_query ) : '';

?>
<div class="group-cdn browse">
	<p>
		Browse all items stored in the site's CDN.
		<?=anchor( 'admin/cdnadmin/create' . $_return, 'Upload Items', 'class="awesome small green fancybox" data-fancybox-type="iframe" style="float:right;"' )?>
	</p>

	<hr />

	<?php

		$this->load->view( 'admin/_utilities/pagination' );

	?>
	<div class="table-responsive">
	<table>
		<thead>
			<tr>
				<th class="id">ID</th>
				<th class="thumbnail"></th>
				<th class="bucket">Bucket</th>
				<th class="mime">Type</th>
				<th class="filename">Filename</th>
				<th class="user">Uploader</th>
				<th class="created datetime">Created</th>
				<th class="modified datetime">Modified</th>
				<th class="filesize">Filesize</th>
				<th class="actions">Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php

			if ( $objects ) :

				foreach ( $objects AS $object ) :

					echo '<tr>';
						echo '<td class="id">' . number_format( $object->id ) . '</td>';
						echo '<td class="thumbnail">';

							switch( $object->mime ) :

								case 'image/png' :
								case 'image/jpeg' :
								case 'image/gif' :

									echo anchor( cdn_serve( $object->id ), img( cdn_scale( $object->id, 64, 64 ) ), 'class="fancybox"' );

								break;

								default :

									echo '<span class="ion-document"></span>';

								break;

							endswitch;

						echo '</td>';
						echo '<td class="bucket">' . $object->bucket->label . '</td>';
						echo '<td class="mime">' . $object->mime . '</td>';
						echo '<td class="filename">' . $object->filename_display . '</td>';
						$this->load->view( 'admin/_utilities/table-cell-user',		$object->creator );
						$this->load->view( 'admin/_utilities/table-cell-datetime',	array( 'datetime' => $object->created ) );
						$this->load->view( 'admin/_utilities/table-cell-datetime',	array( 'datetime' => $object->modified ) );
						echo '<td class="filesize">' . format_bytes( $object->filesize ) . '</td>';
						echo '<td class="actions">';
							echo anchor( 'admin/cdnadmin/edit/' . $object->id . $_return, 'Edit', 'class="awesome small"' );
							echo anchor( 'admin/cdnadmin/delete/' . $object->id . $_return, 'Delete', 'data-title="Are you sure?" data-body="Deleting an item will attempt to disconnect it from resources which depend on it. The object wil be recoverable but dependencies won\'t." class="confirm awesome small red"' );
							echo anchor( cdn_serve( $object->id ), 'View', 'class="awesome small fancybox" data-fancybox-type="iframe"' );
						echo '</td>';
					echo '</tr>';

				endforeach;

			else :

				echo '<tr>';
				echo '<td colspan="9" class="no-data">';
				echo 'No Items found';
				echo '</td>';
				echo '</tr>';

			endif;

		?>
		</tbody>
	</table>
	</div>

	<?php

		$this->load->view( 'admin/_utilities/pagination' );

	?>
</div>