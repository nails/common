<!DOCTYPE html>
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
							
								<?php if ( $this->input->get( 'filter-tag' ) ) : ?>
								<div class="info remove-object-tag" data-id="<?=$this->input->get( 'filter-tag' )?>">
									<p>Drag files here to remove from tag</p>
								</div>
								<?php elseif( $bucket->tags ): ?>
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
									
										$_selected	 = ! $this->input->get( 'filter-tag' ) ? 'selected' : '';
										$_uri		 = $_SERVER['REQUEST_URI'];
										$_uri		.= strpos( $_SERVER['REQUEST_URI'], '?' ) !== FALSE ? '&' : '?';
										
										//	Filter out any existing filter-tag=
										$_uri		 = preg_replace( '/&{1,}filter\-tag=([0-9]*)/', '', $_uri );
										
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
							</li>
							<li class="progress">
								uploading &rsaquo;
								<span class="track">
									<span class="bar" style="width:75%;"></span>
								</span>
							</li>
							<li class="thumbs">
								<ul>
								<?php
								
									if ( $bucket->objects ) :
									
										foreach ( $bucket->objects AS $object ) :
										
											echo '<li class="thumb" data-id="' . $object->id . '">';
											
											echo '<div class="image">';
											switch ( $object->mime ) :
											
												case 'image/jpg' :
												case 'image/jpeg' :
												case 'image/png' :
												case 'image/gif' :
													
													//	Thumbnail
													echo img( cdn_scale( $bucket->slug, $object->filename, 150, 175 ) );
													
													$_action_download = 'View';
																											
												break;
												
												// --------------------------------------------------------------------------
												
												default :
												
													//	Generic file
													echo img( array( 'src' => NAILS_URL . 'img/icons/document-icon-128px.png', 'style' => 'border:none;margin-top:20px;' ) );
													
													$_action_download = 'Download';
												
												break;
											
											endswitch;
											
												//	Actions
												echo '<div class="actions">';
												
												echo '<a href="#" data-bucket="' . $bucket->slug .'" data-file="' . $object->filename .'" class="awesome green small insert">Insert</a>';
												echo anchor( 'cdn/manager/delete/' . $object->id . '?' . $_SERVER['QUERY_STRING'], 'Delete', 'class="awesome red small delete"' );
												echo '<a href="' . cdn_serve( $bucket->slug, $object->filename ) . '" class="fancybox awesome small">' . $_action_download . '</a>';
												
												echo '</div>';
											
											echo '</div>';
																							
											//	Filename
											echo '<p class="filename">' . $object->filename_display . '</p>';
											
											echo '</li>';
										
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
				
				_urlscheme.serve		= '<?=CDN::cdn_serve_url_scheme()?>';
				_urlscheme.thumb		= '<?=CDN::cdn_thumb_url_scheme()?>';
				_urlscheme.scale		= '<?=CDN::cdn_scale_url_scheme()?>';
				_urlscheme.placeholder	= '<?=CDN::cdn_placeholder_url_scheme()?>';
				_urlscheme.blank_avatar	= '<?=CDN::cdn_blank_avatar_url_scheme()?>';
				
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
