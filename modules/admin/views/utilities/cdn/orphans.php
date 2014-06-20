<div class="group-utilities cdn orphans">
	<p>
		It is possible for CDN objects in the database to become disconnected from the physical files on disk.
		If you notice files seem to be missing when they shouldn't (e.g error triangle or 404s) then use this utlity
		to find broken objects.
	</p>
	<p>
		You can choose to specify wether to look for database items which are missing files, or the opposite,
		files which aren't in the database.
	</p>
	<p class="system-alert message">
		<strong>Please note:</strong> This process can take some time to execute on large CDNs and may time out. If
		you are experiencing timeouts consider increasing the timeout limit for PHP temporarily or executing
		<u rel="tipsy" title="Use command: `php index.php admin utilities cms orphans`">via the command line</u>.
	</p>

	<hr />

	<?=form_open( NULL, 'id="search-form"' )?>
		<fieldset>
			<legend>Search Options</legend>
			<?php

				$_field				= array();
				$_field['key']		= 'type';
				$_field['label']	= 'Search For';
				$_field['class']	= 'select2';

				$_options = array(
					'db'	=>	'Database objects for which the file does not exist.',
					'file'	=>	'Files whch do not exist in the database.'
				);

				echo form_field_dropdown( $_field, $_options );

				// --------------------------------------------------------------------------

				$_field				= array();
				$_field['key']		= 'parser';
				$_field['label']	= 'With the results';
				$_field['class']	= 'select2';

				$_options = array(
					'list'		=> 'Show list of results',
					'purge'		=> 'Permanently delete',
					'create'	=> 'Add to database (applicable to File search only)'
				);

				echo form_field_dropdown( $_field, $_options );

			?>
		</fieldset>
		<?=form_submit( 'submit', lang( 'action_search' ) )?>
	<?=form_close()?>

	<?php if ( isset( $orphans ) ) : ?>

		<hr />

		<h2 style="margin-bottom:1em;">
			Results <?=! empty( $orphans['elapsed_time'] ) ? '(search took ' . $orphans['elapsed_time'] . ' seconds)' : '' ?>
		</h2>

		<div class="table-responsive">
			<table>
				<thead>
					<tr>
						<th>Bucket</th>
						<th>Filename</th>
						<th>Filesize</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php

					if ( ! empty( $orphans['orphans'] ) ) :

						foreach ( $orphans['orphans'] AS $orphan ) :

							echo '<tr>';
								echo '<td>' . $orphan->bucket . '</td>';
								echo '<td>' . $orphan->filename_display . '</td>';
								echo '<td>' . format_bytes( $orphan->filesize ) . '</td>';
								echo '<td>';
									if ( ! empty( $orphan->id ) ) :

										echo anchor( '#', lang( 'action_delete' ), 'data-title="Are you sure?" data-body="This action is permanent and cannot be undone." class="confirm awesome small red"' );

									else :

										echo anchor( '#', lang( 'action_delete' ), 'data-title="Are you sure?" data-body="This action is permanent and cannot be undone." class="confirm awesome small red"' );

									endif;
								echo '</td>';
							echo '</tr>';

						endforeach;

					else :

						echo '<tr>';
							echo '<td colspan="4" class="no-data">No orphaned items were found.</td>';
						echo '</tr>';

					endif;

				?>
				</tbody>
			</table>
		</div>

	<?php endif; ?>

	<div id="search-mask" class="mask"></div>
</div>
<script type="text/javascript">
<!--//

	$(function(){

		var _Admin_Utilities_Cdn_Orphans = new NAILS_Admin_Utilities_Cdn_Orphans();
		_Admin_Utilities_Cdn_Orphans.init();

	});

//-->
</script>