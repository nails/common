<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
	<head>
	
		<!--	META	-->
		<meta charset="utf-8">
		<title><?=isset( $page->title ) ? $page->title . ' - ' : NULL?><?=APP_NAME?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
		<meta name="description" content="">
		<meta name="author" content="">
		
		<!--	JS GLOBALS	-->
		<script tyle="text/javascript">
			window.NAILS_URL = '<?=NAILS_URL?>';
		</script>
		
		<!--	STYLES	-->
		<link href="<?=NAILS_URL?>css/nails.default.css" rel="stylesheet">
		
		<!--	JAVASCRIPT	-->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
		<script>!window.jQuery && document.write('<script src="<?=NAILS_URL?>jquery.min.js"><\/script>')</script>
		
		<script src="<?=NAILS_URL?>js/nails.default.min.js"></script>
		<script src="<?=NAILS_URL?>js/jquery.tipsy.min.js"></script>
		
		<!--	HTML5 shim, for IE6-8 support of HTML5 elements	-->
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		
	</head>
	<body>
	
	<div class="container">
	
		<!--	HEADER	-->
		<div class="row" id="nails-default-header">
			<div class="sixteen columns">
				<h1><a href="<?=site_url()?>" class="brand"><?=APP_NAME?></a></h1>
			</div>
		</div>
	
	
		<!--	CONTENT	-->
		<div class="row" id="nails-default-content">
			<div class="sixteen columns">
			
				<?=isset( $page->title ) ? '<h2>' . $page->title . '</h2>' : NULL?>
				
				<!--	SYSTEM ALERTS	-->
				<?php if ( isset( $error ) && $error ) : ?>
					<div class="system-alert error">
						<div class="padder">
							<p>
								<?=$error?>
							</p>
						</div>
					</div>
				<?php endif; ?>
				
				<?php if ( isset( $success ) && $success ) : ?>
					<div class="system-alert success">
						<div class="padder">
							<p>
								<?=$success?>
							</p>
						</div>
					</div>
				<?php endif; ?>
				
				<?php if ( isset( $message ) && $message ) : ?>
					<div class="system-alert message">
						<div class="padder">
							<p>
								<?=$message?>
							</p>
						</div>
					</div>
				<?php endif; ?>
				
				<?php if ( isset( $notice ) && $notice ) : ?>
					<div class="system-alert notice">
						<div class="padder">
							<p>
								<?=$notice?>
							</p>
						</div>
					</div>
				<?php endif; ?>