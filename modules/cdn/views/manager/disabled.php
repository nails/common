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
			<div class="browser-outer">
				<div class="browser-inner">
					<div class="disabled">
						<h1>Sorry, the media manager is not available.</h1>
						<p>You don't have permission to view the media manager at the moment.</p>
						<?php

							if ( ! $user->is_logged_in() ) :

								echo '<p>' . anchor( 'auth/login?return_to=' . urlencode( $_SERVER['REQUEST_URI']), lang( 'action_login' ), 'class="awesome"' ) . '</p>';

							endif;

							// --------------------------------------------------------------------------

							if ( isset( $bad_bucket ) ) :

								echo '<p class="system-alert error">';
								echo $bad_bucket;
								echo '</p>';

							endif;

						?>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
