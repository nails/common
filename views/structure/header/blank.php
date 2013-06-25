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
		<link rel="stylesheet" type="text/css" media="screen" href="<?=NAILS_URL . 'css/nails.admin.css'?>" />
		<?=$this->asset->output( 'css' )?>
		
		<!--	JS GLOBALS	-->
		<script type="text/javascript">
			var ENVIRONMENT		= '<?=ENVIRONMENT?>';
			window.SITE_URL		= '<?=site_url()?>';
			window.NAILS_URL	= '<?=NAILS_URL?>';
			window.NAILS_LANG	= {};
		</script>
		
		<!-- JAVASCRIPT[S] -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
		<script>!window.jQuery && document.write('<script src="<?=NAILS_URL?>js/jquery.min.js"><\/script>')</script>
		
		<script type="text/javascript" src="<?=NAILS_URL . 'js/jquery.tipsy.min.js'?>"></script>
		<script type="text/javascript" src="<?=NAILS_URL . 'js/jquery.chosen.min.js'?>"></script>
		<script type="text/javascript" src="<?=NAILS_URL . 'js/jquery.fancybox.min.js'?>"></script>
		<script type="text/javascript" src="<?=NAILS_URL . 'js/nails.default.min.js'?>"></script>
		<script type="text/javascript" src="<?=NAILS_URL . 'js/nails.admin.min.js'?>"></script>
		
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