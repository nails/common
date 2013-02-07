<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

	<head>
		
		<title>Intern Avenue</title>
		
		<!--	META TAGS	-->
		<meta charset="utf-8" />
		
		<!--	STYLES	-->
		<link rel="stylesheet" type="text/css" media="screen" href="<?=NAILS_URL . 'css/nails.admin.css'?>" />
		<?=$this->asset->output( 'css' )?>
		
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