<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
	<head>
		<title><?php

			echo ! empty( $page->title ) ? $page->title . ' - ' : '';

			echo APP_NAME;

		?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta charset='utf-8'>
		<meta name="description" content="<?=! empty( $page->description ) ? $page->description : ''?>">
		<meta name="keywords" content="<?=! empty( $page->keywords ) ? $page->keywords : ''?>">
		<script type="text/javascript">
			var ENVIRONMENT					= '<?=ENVIRONMENT?>';
			window.SITE_URL					= '<?=site_url( '', page_is_secure() )?>';
			window.NAILS					= {};
			window.NAILS.URL				= '<?=NAILS_ASSETS_URL?>';
			window.NAILS.LANG				= {};
			window.NAILS.USER				= {};
			window.NAILS.USER.ID			= <?=active_user( 'id' ) ? active_user( 'id' ) : 'null'?>;
			window.NAILS.USER.FNAME			= '<?=active_user( 'first_name' )?>';
			window.NAILS.USER.LNAME			= '<?=active_user( 'last_name' )?>';
			window.NAILS.USER.EMAIL			= '<?=active_user( 'email' )?>';
		</script>
		<?=link_tag( NAILS_ASSETS_URL . 'bower_components/bootstrap/dist/css/bootstrap.min.css' )?>
		<?=link_tag( NAILS_ASSETS_URL . 'bower_components/ionicons/css/ionicons.min.css' )?>
		<?php

			$this->asset->output( 'css' );
			$this->asset->output( 'css-inline' );

		?>
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		  <script src="<?=NAILS_ASSETS_URL . 'bower_components/html5shiv/dist/html5shiv.js'?>"></script>
		  <script src="<?=NAILS_ASSETS_URL . 'bower_components/respond/dest/respond.min.js'?>"></script>
		<![endif]-->
	</head>
	<body>
		<div class="container">
			<div class="row text-center" style="margin-top:1em;">
				<?php

					if ( $user->was_admin() ) :

						echo '<div class="alert alert-info text-left">';
						echo 'Logged in as <strong>' . active_user( 'first_name,last_name' ) . ' (' . active_user( 'email' ) . ')</strong>.';
						echo anchor( $this->session->userdata( 'admin_recovery' )->back_to_admin_url, 'Back to Admin', 'class="pull-right btn btn-sm btn-default" style="margin-top:-0.5em;"' );
						echo '</div>';

					endif;

				?>
				<h1>
					<?=APP_NAME?>
				</h1>
				<p>
					A webapp powered by <?=anchor( NAILS_PACKAGE_URL, NAILS_PACKAGE_NAME )?>, ooh la la
				</p>
			</div><!-- /.row -->
			<hr />
			<?php

				if ( $success || $error || $message || $notice ) :

					echo '<div class="container row">';
						echo $success	? '<p class="alert alert-success">' . $success . '</p>' : '';
						echo $error		? '<p class="alert alert-danger">' . $error . '</p>' : '';
						echo $message	? '<p class="alert alert-warning">' . $message . '</p>' : '';
						echo $notice	? '<p class="alert alert-info">' . $notice . '</p>' : '';
					echo '</div>';

				endif;