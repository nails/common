<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="UTF-8" />
	<title>Admin - <?=( isset( $page->title ) ) ? $page->title . ' - ' : NULL?><?=APP_NAME?></title>	
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	
	<!--	ASSETS	-->
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	
	<!--	JS GLOBALS	-->
	<script type="text/javascript">
		window.NAILS_URL = '<?=NAILS_URL?>';
	</script>
	
	<!--	REQUIRED JS	-->
	<script type="text/javascript" src="<?=NAILS_URL . 'js/jquery.tipsy.min.js'?>"></script>
	<script type="text/javascript" src="<?=NAILS_URL . 'js/jquery.fancybox.pack.js'?>"></script>
	<script type="text/javascript" src="<?=NAILS_URL . 'js/jquery.mousewheel.pack.js'?>"></script>
	<script type="text/javascript" src="<?=NAILS_URL . 'js/jquery.easing.pack.js'?>"></script>
	<script type="text/javascript" src="<?=NAILS_URL . 'js/nails.admin.min.js'?>"></script>
	
	<!--	REQUIRED CSS	-->
	<link rel="stylesheet" type="text/css" media="screen" href="<?=NAILS_URL . 'css/nails.admin.css'?>" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?=NAILS_URL . 'css/jquery.tipsy.css'?>" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?=NAILS_URL . 'css/jquery.fancybox.css'?>" />
	
	<!--	DYNAMIC	-->
	<?php
	
		echo $this->asset->output( 'css' );
		echo $this->asset->output( 'js' );
		echo $this->asset->output( 'css-inline' );
	
	?>
	
	<script type="text/javascript" charset="utf-8">
		<?=$this->asset->output( 'js-inline' )?>
	</script>	
	
</head>
<body>
		
	<div class="header">
	
		<?=img( array(
			'src'	=> NAILS_URL . 'img/admin/logo.png',
			'class'	=> 'left'
		))?>
		
		<ul class="left" style="min-width:400px;">
			
			<li style="display:block;margin-bottom:4px;"><a href="<?=site_url( 'admin' )?>" style="font-size:18px;font-weight:bold;color:#fff;"><?=APP_NAME?> Administration</a></li>
			<li><?=anchor( 'admin', 'Home' )?></li>
			<?=( isset( $page->title ) ) ? '<li>&rsaquo;</li><li>' . $page->title . '</li>' : NULL?></li>
		
		</ul>
		
		<ul class="right shaded">
		
			<li><?=anchor( '/', 'Switch to Front End')?></li>
			<li style="color:#999;">Logged in as <a href="#"><?=active_user( 'first_name' )?></a></li>
			<li class="logout"><?=anchor( 'auth/logout', 'Logout' )?><?=anchor( 'auth/logout', '' )?></li>
		
		</ul>
		
		<!--	CLEARFIX	-->
		<div class="clear"></div>
	
	</div>
	
	<div class="header_min">
	
		<a href="#" class="toggle-header">
		
			<?=img( array(
				'src'	=> NAILS_URL . 'img/admin/head_slide_up.png',
				'class'	=> 'up'
			))?>
	
		</a>
	
	</div>
	
	
	
	
	<div class="sidebar left">
	
	
		<?php if ( isset( $loaded_modules ) ) : foreach ( $loaded_modules AS $module => $config ) : ?>
		
			<div class="box" id="box_<?=url_title( $config->name )?>">
			<h2 title=""><?=$config->name?> <a href="#" class="toggle">close</a></h2>
			<ul class="container">
			
				<?php foreach( $config->funcs AS $method => $label ) : ?>
				
					<?php if ($method == 'su' ) : ?>
					
						<?php if ( ! $user->is_superuser() ) continue ?>
					
						<?php foreach ( $label AS $su_method => $su_label ) : ?>
						
							<li>
								&rsaquo;
								<?=anchor( 'admin/' . $module . '/' . $su_method, $su_label )?>
							</li>
						
						<?php endforeach; ?>
						
					<?php continue; ?>
					
					<?php endif; ?>
					
					
					<li>
						&rsaquo;
						<?=anchor( 'admin/' . $module . '/' . $method, $label )?>
					</li>
					
				<?php endforeach; ?>
				
			</ul>
			</div>
		
		
		<?php endforeach; else: ?>
		
		
			<div class="box">
				<h2 title="">Oops!</h2>
				<ul>
					<li>
						No modules enabled for this site.
					</li>
				</ul>
			</div>
		
		
		<?php endif; ?>
	
	</div>
	
	
	
	<div class="content">
	
		<div class="content_inner">
		
		
			<?php if ( isset( $notice ) && ! empty( $notice ) ) : ?>
			<div class="notice">
			
				<p>testestest<?=$notice?></p>
			
			</div>
			<?php endif; ?>
			
			<?php if ( isset( $message ) && ! empty( $message ) ) : ?>
			<div class="message">
			
				<p><?=$message?></p>
			
			</div>
			<?php endif; ?>
			
			<?php if ( isset( $error ) && ! empty( $error ) ) : ?>
			<div class="error">
			
				<p><?=$error?></p>
			
			</div>
			<?php endif; ?>
			
			<?php if ( isset( $success ) && ! empty( $success ) ) : ?>
			<div class="success">
			
				<p><?=$success?></p>
			
			</div>
			<?php endif; ?>
			
			<div class="js_error" style="display:none;">
				<p>
					<span class="js_error_head">Hey!</span>
					<span class="js_error_text"></span>
				</p>
			</div>