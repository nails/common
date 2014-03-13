<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
	<head>

		<!--	META	-->
		<meta charset="utf-8">
		<title><?php

			echo isset( $page->title ) ? $page->title : '';
			echo isset( $page->title ) && APP_NAME ? ' - ' : '';
			echo APP_NAME;


		?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
		<meta name="description" content="">
		<meta name="author" content="">

		<!--	STYLES	-->
		<link rel="stylesheet" type="text/css" media="screen" href="<?=NAILS_ASSETS_URL . 'css/nails.admin.css'?>" />
		<?=$this->asset->output( 'css' )?>

		<!--	JS GLOBALS	-->
		<script type="text/javascript">
			var ENVIRONMENT					= '<?=ENVIRONMENT?>';
			window.SITE_URL					= '<?=site_url()?>';
			window.NAILS					= {};
			window.NAILS.URL				= '<?=NAILS_ASSETS_URL?>';
			window.NAILS.LANG				= {};
			window.NAILS.USER				= {};
			window.NAILS.USER.ID			= <?=active_user( 'id' ) ? active_user( 'id' ) : 'null'?>;
			window.NAILS.USER.FNAME			= '<?=active_user( 'first_name' )?>';
			window.NAILS.USER.LNAME			= '<?=active_user( 'last_name' )?>';
			window.NAILS.USER.EMAIL			= '<?=active_user( 'email' )?>';
		</script>

		<!-- JAVASCRIPT[S] -->
		<?=$this->asset->output( 'js' )?>

	</head>
	<body class="blank">

		<?php if ( isset( $error ) && ! empty( $error ) ) : ?>
		<div class="system-alert error">
			<div class="padder">
				<p><?=$error?></p>
			</div>
		</div>
		<?php endif; ?>

		<?php if ( isset( $success ) && ! empty( $success ) ) : ?>
		<div class="system-alert success">
			<div class="padder">
				<p><?=$success?></p>
			</div>
		</div>
		<?php endif; ?>

		<?php if ( isset( $message ) && ! empty( $message ) ) : ?>
		<div class="system-alert message">
			<div class="padder">
				<p><?=$message?></p>
			</div>
		</div>
		<?php endif; ?>

		<?php if ( isset( $notice ) && ! empty( $notice ) ) : ?>
		<div class="system-alert notice">
			<div class="padder">
				<p><?=$notice?></p>
			</div>
		</div>
		<?php endif; ?>