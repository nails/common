<?php

	$_filter_view = $this->input->get( 'filter-view' );

	if ( ! $_filter_view ) :

		$_filter_view = $this->session->userdata( 'cdn-manager-view' );

		if ( ! $_filter_view ) :

			$_filter_view = 'thumb';

		endif;

	endif;

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
			window.SITE_URL		= '<?=site_url()?>';
			window.NAILS_URL	= '<?=NAILS_URL?>';
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
				
					echo '<p class="system-alert success no-close">';
					echo $success;
					echo '<br /><a href="#" class="awesome small ok">OK</a>';
					echo '</p>';
					
				endif;
				
				if ( $error ) :
				
					echo '<p class="system-alert error no-close">';
					echo $error;
					echo '<br /><a href="#" class="awesome small ok">OK</a>';
					echo '<a href="#" class="awesome small cancel">Cancel</a>';

					echo '</p>';
					
				endif;
				
				if ( $notice ) :
				
					echo '<p class="system-alert notice no-close">';
					echo $notice;
					echo '<br /><a href="#" class="awesome small ok">OK</a>';
					echo '</p>';
					
				endif;
				
				if ( $message ) :
				
					echo '<p class="system-alert message no-close">';
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
									
										$_selected		 = ! $this->input->get( 'filter-tag' ) ? 'selected' : '';
										$_uri_raw		 = $_SERVER['REQUEST_URI'];
										$_uri_raw		.= strpos( $_SERVER['REQUEST_URI'], '?' ) !== FALSE ? '&' : '?';
										
										//	Filter out any existing filter-tag=
										$_uri		 = preg_replace( '/&{1,}filter\-tag=([0-9]*)/', '', $_uri_raw );
										
										echo '<li class="tag ' . $_selected . '">';
										echo '<a href="' . preg_replace( '/&{1,}$/', '', $_uri ) . '" class="tag">';
										echo 'All My Files';
										echo '<span class="count">' . $bucket->object_count . '</span>';
										echo '</a>';
										echo '</li>';
										
										// --------------------------------------------------------------------------
										
										foreach( $bucket->tags AS $tag ) :
										
											$_selected = $this->input->get( 'filter-tag' ) == $tag->id ? 'selected' : '';
											
											echo '<li class="tag droppable ' . $_selected . '" data-id="' . $tag->id . '">';
											echo '<a href="' . site_url( 'cdn/manager/delete_tag/' . $tag->id . '/?' . $_SERVER['QUERY_STRING'] ) .'" class="confirm delete-tag" data-confirm="If you continue this tag will be deleted. No files will be removed.\n\nContinue?"></a>';
											echo '<a href="' . $_uri . 'filter-tag=' . $tag->id . '" class="tag">';
											echo $tag->label;
											echo '<span class="count">' . $tag->total . '</span>';
											echo '</a>';
											echo '</li>';
										
										endforeach;
										
										// --------------------------------------------------------------------------
										
										echo '<li class="new-tag">';
										echo form_open( 'cdn/manager/new_tag/?' . $_SERVER['QUERY_STRING'] );
										echo form_input( 'label', '', 'placeholder="New Tag"' );
										echo form_submit( 'submit', 'Add', 'class="awesome small green"' );
										echo form_close();
										echo '</li>';
									
									?>
								</ul>
							</li>
							<li class="toolbar">
								<?php
								
									echo form_open_multipart( 'cdn/manager/upload/?' . $_SERVER['QUERY_STRING'] );
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
								Browsing bucket: <strong><?=$bucket_label?></strong>
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
										
										//	Filter out any existing filter-view=
										$_uri		 = preg_replace( '/&{1,}filter\-view=([a-z]*)/', '', $_uri_raw );
										
										$_selected = $_filter_view == 'thumb' ? 'selected ' . $_selected . '' : '';
										echo anchor( $_uri . 'filter-view=thumb', 'Thumbnails', 'class="thumbnail ' . $_selected . '"' );
										
										$_selected = $_filter_view == 'list' ? 'selected' : '';
										echo anchor( $_uri . 'filter-view=list', 'List', 'class="list ' . $_selected . '"' );
										
										$_selected = $_filter_view == 'detail' ? 'selected' : '';
										echo anchor( $_uri . 'filter-view=detail', 'Details', 'class="detail ' . $_selected . '"' );
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
								<ul>
								<?php
								
									if ( $bucket->objects ) :
									
										foreach ( $bucket->objects AS $object ) :

											switch ( $_filter_view ) :
											
												case 'detail' :
												
													$this->session->set_userdata( 'cdn-manager-view', 'detail' );
													$this->load->view( 'cdn/manager/file-detail', array( 'object' => &$object ) );
												
												break;
												
												// --------------------------------------------------------------------------
												
												case 'list' :
												
													$this->session->set_userdata( 'cdn-manager-view', 'list' );
													$this->load->view( 'cdn/manager/file-list', array( 'object' => &$object ) );
												
												break;
												
												// --------------------------------------------------------------------------
												
												case 'thumb' :
												default :
												
													$this->session->set_userdata( 'cdn-manager-view', 'thumb' );
													$this->load->view( 'cdn/manager/file-thumb', array( 'object' => &$object ) );
												
												break;
											
											endswitch;
										
										endforeach;
										
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
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		
		<script type="text/javascript">
		<!--//
		
			var _nails,_api,_manager;
			
			$(function(){
			
				//	Initialise NAILS_JS
				_nails = new NAILS_JS();
				_nails.init();
				
				// --------------------------------------------------------------------------
				
				//	Initialise NAILS_API
				_api = new NAILS_API();
				_api.init( '', '' );
				
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
				
					$is_fancybox = $this->input->get( 'is_fancybox' ) ? 'true' : 'false';
					
					if ( isset( $_GET['CKEditorFuncNum'] ) ) :
					
						echo '_manager.init( \'ckeditor\', ' . $_GET['CKEditorFuncNum'] . ', _urlscheme, ' . $is_fancybox . ' );';
						
						if ( $this->input->get( 'deleted' ) ) :
						
							echo '_manager._insert_ckeditor( \'\', \'\' );';
						
						endif;
						
					else :
					
						echo '_manager.init( \'native\', \'' . $this->input->get( 'callback' ) . '\', {}, ' . $is_fancybox . ' );';
						
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
