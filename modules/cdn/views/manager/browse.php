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
			<div class="browser-outer">
				<div class="browser-inner">
				<?php
				
					if ( $enabled ) :
					
						?>
						<div class="enabled">
							<ul class="layout">
								<li class="directories">
									<h2>Directories</h2>
									<ul>
										<li class="directory">Directory</li>
									</ul>
								</li>
								<li class="toolbar">
									<?php
									
										echo form_open_multipart( 'cdn/manager/upload/' . $type );
										echo form_hidden( 'return', $_SERVER['REQUEST_URI'] );
										echo form_submit( 'submit', 'Upload ' . $type_single, 'class="awesome green"' );
										echo form_upload( 'userfile' );
										echo form_close();
										
										// --------------------------------------------------------------------------
										
										//	Alerts
										if ( $success ) :
										
											echo '<p class="system-alert success no-close">';
											echo $success;
											echo '</p>';
											
										endif;
										
										if ( $error ) :
										
											echo '<p class="system-alert error no-close">';
											echo $error;
											echo '</p>';
											
										endif;
										
										if ( $notice ) :
										
											echo '<p class="system-alert notice no-close">';
											echo $notice;
											echo '</p>';
											
										endif;
										
										if ( $message ) :
										
											echo '<p class="system-alert message no-close">';
											echo $message;
											echo '</p>';
											
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
									
										if ( $files ) :
										
											foreach ( $files AS $file ) :
											
												echo '<li class="thumb">';
												
												echo '<div class="image">';
												switch ( $type ) :
												
													case 'image' :
														
														//	Thumbnail
														echo img( cdn_scale( $bucket, $file, 150, 175 ) );
														
														//	Actions
														echo '<div class="actions">';
														
														echo '<a href="#" data-bucket="' . $bucket .'" data-file="' . $file .'" class="awesome green small insert">Insert</a>';
														echo anchor( 'cdn/manager/delete/' . $type . '/' . $file . '?' . $_SERVER['QUERY_STRING'] . '&return=' . urlencode( $_SERVER['REQUEST_URI'] ), 'Delete', 'class="awesome red small delete"' );
														echo '<a href="' . cdn_serve( $bucket, $file ) . '" class="fancybox awesome small">View</a>';
														
														echo '</div>';
																												
													break;
													
													// --------------------------------------------------------------------------
													
													case 'file' :
													
														switch ( substr( $file, strrpos( $file, '.' ) ) ) :
														
															case '.jpg' :
															case '.png' :
															case '.gif' :
															
																//	It's an image, so thumbnail it
																echo img( cdn_scale( $bucket, $file, 150, 175 ) );
																
																//	Actions
																echo '<div class="actions">';
																
																echo '<a href="#" data-bucket="' . $bucket .'" data-file="' . $file .'" class="awesome green small insert">Insert</a>';
																echo anchor( 'cdn/manager/delete/' . $type . '/' . $file, 'Delete', 'class="awesome red small delete"' );
																echo '<a href="' . cdn_serve( active_user( 'id' ) . '-' . $type, $file ) . '"class="fancybox awesome small">View</a>';
																
																echo '</div>';
															
															break;
															
															default :
															
																//	Generic file
																echo img( array( 'src' => NAILS_URL . 'img/icons/document-icon-128px.png', 'style' => 'border:none;margin-top:20px;' ) );
																
																//	Actions
																echo '<div class="actions">';
																
																echo '<a href="#" data-bucket="' . $bucket .'" data-file="' . $file .'" class="awesome green small insert">Insert</a>';
																echo anchor( 'cdn/manager/delete/' . $type . '/' . $file, 'Delete', 'class="awesome red small delete"' );
																echo '<a href="' . cdn_serve( active_user( 'id' ) . '-' . $type, $file ) . '" target="_blank" class="awesome small">Download</a>';
																
																echo '</div>';
																
															break;
														
														endswitch;
													
													break;
												
												endswitch;
												echo '</div>';
																								
												//	Filename
												echo '<p class="filename">' . $file . '</p>';
												
												echo '</li>';
											
											endforeach;
											
										else :
										
											echo '<div class="no-files">';
											echo '<h1>No ' . $type_plural . '</h1>';
											echo '<p>Upload your first ' . $type_single . ' using the form above.</p>';
											echo '</div>';
										
										endif;
									
									?>
									</ul>
								</li>
							</ul>
						</div>
						<?php
						
					else :
					
						?>
						<div class="disabled">
							<h1>Sorry, the media manager is not available.</h1>
							<p>You don't have permission to view the media manager at the moment.</p>
							<?php
							
								if ( isset( $bad_bucket ) ) :
								
									echo '<p class="system-alert error no-close">';
									echo $bad_bucket;
									echo '</p>';
								
								endif;
							
								if ( ! $user->is_logged_in() ) :
								
									echo '<p>' . anchor( 'auth/login?return_to=' . urlencode( 'cdn/manager/browse/' . $type ), lang( 'action_login' ), 'class="awesome"' ) . '</p>';
								
								endif;
							
							?>
						</div>
						<?php
						
					endif;
				?>
				</div>
			</div>
		</div>
		
		<script type="text/javascript">
		<!--//
		
			var _nails;
			
			$(function(){
			
				//	Initialise NAILS_JS
				_nails = new NAILS_JS();
				_nails.init();
				
				// --------------------------------------------------------------------------
				
				$( 'form' ).on( 'submit', function() {
					
					$( '#mask' ).show();
					
				});
				
				// --------------------------------------------------------------------------
				
				$( 'a.insert' ).on( 'click', function() {
					
					var _bucket	= $(this).attr( 'data-bucket' );
					var _file	= $(this).attr( 'data-file' );
					
					execute_callback( _bucket, _file );
					
					//	Close window
					<?php
						
						if ( $this->input->get( 'is_fancybox' ) ) :
						
							echo 'parent.$.fancybox.close();';
						
						else :
						
							echo 'window.close();';
						
						endif;
					
					?>
					
					// --------------------------------------------------------------------------
					
					return false;
					
				});
				
				
				function execute_callback( _bucket, _file )
				{
				<?php
				
					if ( isset( $_GET['CKEditorFuncNum'] ) ) :
					
						//	Called from a CKEditor Instance
						
						?>
							//	TODO Render a modal asking for customisations to the URL
							
							var _urlscheme			= {};
							
							_urlscheme.serve		= '<?=CDN::cdn_serve_url_scheme()?>';
							_urlscheme.thumb		= '<?=CDN::cdn_thumb_url_scheme()?>';
							_urlscheme.scale		= '<?=CDN::cdn_scale_url_scheme()?>';
							_urlscheme.placeholder	= '<?=CDN::cdn_placeholder_url_scheme()?>';
							_urlscheme.blank_avatar	= '<?=CDN::cdn_blank_avatar_url_scheme()?>';
							
							//	Choose the scheme to use (TODO, amke this dynamic)
							var _scheme = _urlscheme['serve'];
							
							//	Define the data object
							var _data = {
								bucket	: _bucket,
								file	: _file,
								width	: 0,		//	TODO
								height	: 0,		//	TODO
								sex		: '',		//	TODO
								border	: 0			//	TODO
							};
							
							//	Apply the scheme
							var _url = Mustache.render( _scheme, _data );
							
							//	Call back to the CKEditor instance
							window.opener.CKEDITOR.tools.callFunction(<?=$ckeditor_func_num?>, _url );
						
						<?php
					
					else :
					
						//	Callback to a user defined function on the calling page.
						
						if ( $this->input->get( 'is_fancybox' ) ) :
						
							echo 'parent.' . $this->input->get( 'callback' ) . '( _file );';
						
						else :
						
							echo 'window.opener.' . $this->input->get( 'callback' ) . '( _file );';
						
						endif;
					
					endif;
				
				?>
				}
				
				// --------------------------------------------------------------------------
				
				$( 'a.delete' ).on( 'click', function() {
					
					return confirm( 'Are you sure?\n\nThis action is not undoable.' );
					
				});
				
				<?php
				
					if ( $this->input->get( 'deleted' ) ) :
					
						echo 'execute_callback( \'\', \'\' );';
					
					endif;
				
				?>
			
			});
		
		//-->
		</script>
	</body>
</html>
