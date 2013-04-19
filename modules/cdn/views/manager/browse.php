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
		<div class="group-cdn manager">
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
										echo form_hidden( 'upload', TRUE );
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
												
												switch ( $type ) :
												
													case 'image' :
													
														echo img( cdn_scale( active_user( 'id' ) . '-' . $type, $file, 150, 175 ) );
														echo '<div class="actions">';
														echo '<a href="#" class="awesome green small insert">Insert</a>';
														echo anchor( 'cdn/manager/delete/image/' . $file, 'Delete', 'class="awesome red small delete"' );
														echo '<a href="' . cdn_serve( active_user( 'id' ) . '-' . $type, $file ) . '" target="_blank" class="awesome small">View</a>';
														echo '</div>';
														
													break;
												
												endswitch;
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
							<p>Your account doesn't have permission to view the media manager at the moment.</p>
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
					
					alert( 'TODO: Insert JS' );
					return false;
					
				});
				
				
				// --------------------------------------------------------------------------
				
				$( 'a.delete' ).on( 'click', function() {
					
					return confirm( 'Are you sure?\n\nThis action is not undoable.' );
					
				});
			
			});
		
		//-->
		</script>
	</body>
</html>
