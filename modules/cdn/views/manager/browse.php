<?php

	$_filter_view = $this->input->get( 'filter-view' );

	if ( ! $_filter_view ) :

		$_filter_view = $this->session->userdata( 'cdn-manager-view' );

		if ( ! $_filter_view ) :

			$_filter_view = 'thumb';

		endif;

	endif;

	$this->session->set_userdata( 'cdn-manager-view', $_filter_view );

	// --------------------------------------------------------------------------

	$_query_string = $this->input->server( 'QUERY_STRING' ) ? '?' . $this->input->server( 'QUERY_STRING' ) : '';

?><!DOCTYPE html>
<html>
	<head>
		<title>Media Manager</title>
		<meta charset="utf-8">
		<style type="text/css">

			html,body
			{
				height:100%;
			}

		</style>
		<!--	JS GLOBALS	-->
		<script type="text/javascript">
			var ENVIRONMENT		= '<?=ENVIRONMENT?>';
			window.SITE_URL		= '<?=site_url( '', page_is_secure() )?>';
			window.NAILS_ASSETS_URL	= '<?=NAILS_ASSETS_URL?>';
			window.NAILS_LANG	= {};
		</script>

		<?php

			//	Spit out assets
			$this->asset->output();

		?>
	</head>
	<body>
		<div class="group-cdn manager <?=$this->input->get( 'is_fancybox' ) ? 'is-fancybox' : ''?>">
			<div id="mask"></div>
			<div id="alert" <?= $success || $error || $notice || $message  ? 'style="display:block;"' : ''?>>
			<?php

				if ( $success ) :

					echo '<p class="system-alert success">';
					echo $success;
					echo '<br /><a href="#" class="awesome small ok">OK</a>';
					echo '</p>';

				endif;

				if ( $error ) :

					echo '<p class="system-alert error">';
					echo $error;
					echo '<br /><a href="#" class="awesome small ok">OK</a>';
					echo '<a href="#" class="awesome small cancel">Cancel</a>';

					echo '</p>';

				endif;

				if ( $notice ) :

					echo '<p class="system-alert notice">';
					echo $notice;
					echo '<br /><a href="#" class="awesome small ok">OK</a>';
					echo '</p>';

				endif;

				if ( $message ) :

					echo '<p class="system-alert message">';
					echo $message;
					echo '<br /><a href="#" class="awesome small ok">OK</a>';
					echo '</p>';

				endif;
			?>
			</div>
			<div class="browser-outer">
				<div class="browser-inner">
					<div class="enabled">
						<ul class="layout">
							<li class="tags">

								<?php if ( $this->input->get( 'filter-tag' ) && $bucket->objects ) : ?>
									<div class="info remove-object-tag" data-id="<?=$this->input->get( 'filter-tag' )?>">
										<p>Drag files here to remove from tag</p>
									</div>
								<?php elseif( $bucket->tags && $bucket->objects ): ?>
									<div class="info">
										<p>Drag files onto tags to organise</p>
									</div>
								<?php else : ?>
									<div class="info">
										<p>Use tags to organise your uploads</p>
									</div>
								<?php endif; ?>

								<ul>
									<?php

										//	Get the current Query string
										parse_str( $this->input->server( 'QUERY_STRING' ), $_query );

										//	Filter out any existing filter-tag, if any
										unset( $_query['filter-tag'] );

										//	Item is selected?
										$_selected	= ! $this->input->get( 'filter-tag' ) ? 'selected' : '';

										//	Build the URI for this item
										$_uri = site_url( uri_string(), page_is_secure() );
										$_uri .= $_query ? '?' . http_build_query( $_query ) : '';

										echo '<li class="tag ' . $_selected . '">';
											echo '<a href="' . $_uri . '" class="tag">';
												echo 'All My Files';
												echo '<span class="count">' . $bucket->object_count . '</span>';
											echo '</a>';
										echo '</li>';

										// --------------------------------------------------------------------------

										foreach( $bucket->tags AS $tag ) :

											//	Item is selected?
											$_selected = $this->input->get( 'filter-tag' ) == $tag->id ? 'selected' : '';

											//	Build the URIs for this item
											$_query['filter-tag'] = $tag->id;
											$_uri = site_url( uri_string(), page_is_secure() );
											$_uri .= $_query ? '?' . http_build_query( $_query ) : '';

											$_uri_delete = site_url( 'cdn/manager/delete_tag/' . $tag->id . $_query_string, page_is_secure() );

											echo '<li class="tag droppable ' . $_selected . '" data-id="' . $tag->id . '">';
												echo '<a href="' . $_uri_delete .'" class="confirm delete-tag" data-title="Are you sure?" data-body="If you continue this tag will be deleted. No files will be removed.\n\nContinue?"></a>';
												echo '<a href="' . $_uri . '" class="tag">';
													echo $tag->label;
													echo '<span class="count">' . $tag->total . '</span>';
												echo '</a>';
											echo '</li>';

										endforeach;

										// --------------------------------------------------------------------------

										echo '<li class="new-tag">';
										echo form_open( site_url( 'cdn/manager/new_tag' . $_query_string, page_is_secure() ) );
										echo form_input( 'label', '', 'placeholder="New Tag"' );
										echo form_submit( 'submit', 'Add', 'class="awesome small green"' );
										echo form_close();
										echo '</li>';

									?>
								</ul>
							</li>
							<li class="toolbar">
								<?php

									echo form_open_multipart( site_url( 'cdn/manager/upload' . $_query_string, page_is_secure() ) );
									echo form_hidden( 'tag-id', $this->input->get( 'filter-tag' ) );
									echo form_submit( 'submit', 'Upload', 'class="awesome green"' );
									echo form_upload( 'userfile' );
									echo form_close();

									if ( $bucket->objects ) :

										echo '<input type="text" class="search" id="search-text" placeholder="Search files">';

									endif;
								?>
							</li>
							<li class="bucket-info">
								Browsing bucket: <strong><?=$bucket->label?></strong>
								<?php

									if ( $this->input->get( 'filter-tag' ) ) :

										foreach ( $bucket->tags AS $tag ) :

											if ( $tag->id == $this->input->get( 'filter-tag' ) ) :

												echo ' &rsaquo; <strong>' . $tag->label . '</strong>';
												break;

											endif;

										endforeach;

									else :

										echo ' &rsaquo; <strong>All My Files</strong>';

									endif;

								?>
								<span class="view-swap">
									<?php

										//	Get the query string into an array for mutation
										parse_str( $this->input->server( 'QUERY_STRING' ), $_query );

										//	Filter out any existing filter-view=
										unset( $_query['filter-view'] );

										// --------------------------------------------------------------------------

										//	Item is selected?
										$_selected	= $_filter_view == 'thumb' ? 'selected' : '';

										//	Build the URI for this item
										$_query['filter-view'] = 'thumb';
										$_uri = site_url( uri_string(), page_is_secure() );
										$_uri .= $_query ? '?' . http_build_query( $_query ) : '';

										echo anchor( $_uri, 'Thumbnails', 'class="thumbnail ' . $_selected . '"' );

										// --------------------------------------------------------------------------

										//	Item is selected?
										$_selected	= $_filter_view == 'list' ? 'selected' : '';


										//	Build the URI for this item
										$_query['filter-view'] = 'list';
										$_uri = site_url( uri_string(), page_is_secure() );
										$_uri .= $_query ? '?' . http_build_query( $_query ) : '';

										echo anchor( $_uri, 'List', 'class="list ' . $_selected . '"' );

										// --------------------------------------------------------------------------

										//	Item is selected?
										$_selected	= $_filter_view == 'detail' ? 'selected' : '';

										//	Build the URI for this item
										$_query['filter-view'] = 'detail';
										$_uri = site_url( uri_string(), page_is_secure() );
										$_uri .= $_query ? '?' . http_build_query( $_query ) : '';

										echo anchor( $_uri, 'Details', 'class="detail ' . $_selected . '"' );
									?>
								</span>
							</li>
							<li class="progress">
								uploading &rsaquo;
								<span class="track">
									<span class="bar" style="width:75%;"></span>
								</span>
							</li>
							<li class="files">
							<?php

								if ( $bucket->objects ) :

									if ( $_filter_view == 'list' ) :

										echo '<table>';
										echo '<thead>';
										echo '<tr class="file list head">';
										echo '<th class="filename">File</th>';
										echo '<th class="mime">Type</th>';
										echo '<th class="filesize">Filesize</th>';
										echo '<th class="modified">Modified</th>';
										echo '<th class="actions">Actions</th>';
										echo '</tr>';
										echo '</thead>';
										echo '<tbody>';

									else :

										echo '<ul>';

									endif;

									// --------------------------------------------------------------------------

									foreach ( $bucket->objects AS $object ) :

										switch ( $_filter_view ) :

											case 'detail' :

												$this->load->view( 'cdn/manager/file-detail', array( 'object' => &$object, '_query_string' => &$_query_string ) );

											break;

											// --------------------------------------------------------------------------

											case 'list' :

												$this->load->view( 'cdn/manager/file-list', array( 'object' => &$object, '_query_string' => &$_query_string ) );

											break;

											// --------------------------------------------------------------------------

											case 'thumb' :
											default :

												$this->load->view( 'cdn/manager/file-thumb', array( 'object' => &$object, '_query_string' => &$_query_string ) );

											break;

										endswitch;

									endforeach;

									if ( $_filter_view == 'list' ) :

										echo '</tbody>';
										echo '</table>';

									else :

										echo '</ul>';

									endif;

								else :

									echo '<div class="no-files">';

									if ( $this->input->get( 'filter-tag' ) ) :

										echo '<h1>No Files in this Tag</h1>';
										echo '<p>Either drag a file in from the \'All My Files\' tag or upload a new file using the form above.</p>';

									else :


										echo '<h1>No Files</h1>';
										echo '<p>Upload your first file using the form above.</p>';


									endif;

									echo '</div>';

								endif;

							?>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<script type="text/javascript">
		<!--//

			var _nails,_manager;

			$(function(){

				//	Initialise NAILS_JS
				_nails = new NAILS_JS();
				_nails.init();

				// --------------------------------------------------------------------------

				//	Initialise CDN Manager

				var _urlscheme			= {};

				_urlscheme.serve		= '<?=$this->cdn->url_serve_scheme()?>';
				_urlscheme.thumb		= '<?=$this->cdn->url_thumb_scheme()?>';
				_urlscheme.scale		= '<?=$this->cdn->url_scale_scheme()?>';
				_urlscheme.placeholder	= '<?=$this->cdn->url_placeholder_scheme()?>';
				_urlscheme.blank_avatar	= '<?=$this->cdn->url_blank_avatar_scheme()?>';

				_manager = new NAILS_CDN_Manager();
				<?php

					$is_fancybox		= $this->input->get( 'is_fancybox' ) ? 'true' : 'false';
					$reopen_fancybox	= $this->input->get( 'reopen_fancybox' ) ? $this->input->get( 'reopen_fancybox' ) : '';

					if ( isset( $_GET['CKEditorFuncNum'] ) ) :

						echo '_manager.init( \'ckeditor\', ' . $_GET['CKEditorFuncNum'] . ', _urlscheme, ' . $is_fancybox . ', \'' . $reopen_fancybox . '\' );';

						if ( $this->input->get( 'deleted' ) ) :

							echo '_manager._insert_ckeditor( \'\', \'\' );';

						endif;

					else :

						echo '_manager.init( \'native\', \'' . $this->input->get( 'callback' ) . '\', {}, ' . $is_fancybox . ', \'' . $reopen_fancybox . '\' );';

						if ( $this->input->get( 'deleted' ) ) :

							echo '_manager._insert_native( \'\', \'\' );';

						endif;

					endif;

				?>

			});

		//-->
		</script>
	</body>
</html>
