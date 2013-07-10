<!DOCTYPE html>
<html>
	<head>
		<title>Media Manager: Attachments</title>
		<meta charset="utf-8">
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
		<style type="text/css">
		
			body
			{
				height:auto;
				background:transparent;
			}

			body *
			{
				padding:0;
				margin:0;
			}
		
		</style>
	</head>
	<body>
		<div class="group-cdn manager attachments">
		<?php

			if ( $error ) :

				echo '<p class="system-alert error no-close">';
				echo $error;
				echo '</p>';

			else :

				echo '<h1>';
				echo $object->filename_display;
				
				if ( $object->is_img ) :

					echo img( cdn_thumb( $object->id, 32, 32 ) );

				endif;
				echo '</h1>';

				// --------------------------------------------------------------------------

				if ( $attachments ) :

					echo '<p>This item has the following attachments:</p>';
					echo '<ul>';
					foreach ( $attachments AS $attachment ) :

						echo '<li class="attachment">';
						echo $attachment;
						echo '</li>';

					endforeach;
					echo '</ul>';


				else :

					echo '<p class="system-alert success no-close">';
					echo '<strong>No Attachments!</strong> This item is not marked as being attached to any other piece of content.';
					echo '</p>';

				endif;

			endif;

		?>
		</div>
	</body>
</html>