<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
	<meta charset="UTF-8" />
	<title>
	<?php
		
		echo 'Admin - ';
		echo isset( $page->module->name ) ? $page->module->name . ' - ' : NULL;
		echo isset( $page->title ) ? $page->title . ' - ' : NULL;
		echo APP_NAME;
				
	?></title>	
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
	
	<!--	ASSETS	-->
	<script type="text/javascript">
		window.NAILS_URL = '<?=NAILS_URL?>';
		window.SITE_URL = '<?=site_url()?>';
	</script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
	<script>!window.jQuery && document.write('<script src="<?=NAILS_URL?>js/jquery.min.js"><\/script>')</script>
	<?php
	
		echo $this->asset->output( 'css' );
		echo $this->asset->output( 'js' );
		echo $this->asset->output( 'css-inline' );
	
	?>
	<link rel="stylesheet" type="text/css" media="print" href="<?=NAILS_URL . 'css/nails.admin.print.css'?>" />
	<script type="text/javascript" charset="utf-8">
		<?=$this->asset->output( 'js-inline' )?>
	</script>	
	
</head>
<body class="<?=!$loaded_modules ? 'no-modules' : ''?>">
		
	<div class="header">
		
		<ul class="left">
			
			<li style="display:block;margin-bottom:4px;">
				<a href="<?=site_url( 'admin' )?>" style="font-size:18px;font-weight:bold;color:#fff;">
					<span class="app-name"><?=APP_NAME?></span>
					Administration
				</a>
			</li>
			<li><?=anchor( 'admin', 'Home' )?></li>
			<?=( isset( $page->module->name ) ) ? '<li>&rsaquo;</li><li>' . $page->module->name . '</li>' : NULL?></li>
			<?=( isset( $page->title ) ) ? '<li>&rsaquo;</li><li>' . $page->title . '</li>' : NULL?></li>
		
		</ul>
		
		<ul class="right shaded">
		
			<li><?=anchor( '/', 'Switch to Front End')?></li>
			<li style="color:#999;">Logged in as <?=anchor( 'admin/accounts/edit/' . active_user( 'id' ), active_user( 'first_name' ) )?></li>
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
		<div class="padder">
		<?php
			
			$_acl			= active_user( 'acl' );
			$_mobile_menu	= array();
			
			foreach ( $loaded_modules AS $module => $config ) :
			
				//	Add this to the mobile version of the menu
				$_mobile_menu[$module]			= new stdClass();
				$_mobile_menu[$module]->module	= $config->name;
				$_mobile_menu[$module]->url		= NULL;
				$_mobile_menu[$module]->subs	= array();
				
				// --------------------------------------------------------------------------
				
				//	Get any notifications for this module if applicable
				$_notifications = method_exists( $module, 'notifications') ? $module::notifications() : array();
				
				?>
				<div class="box" id="box_<?=url_title( $config->name )?>">
					<h2>
						<?=$config->name?>
						<a href="#" class="toggle">close</a>
					</h2>
					<div class="box-container">
						<ul>
						<?php
						
							//	Loop all the module methods
							foreach( $config->funcs AS $method => $label ) :
							
								//	Is the method enabled?
								if ( get_userobject()->is_superuser() || isset( $_acl['admin'][$module][$method]) ) :
								
									echo '<li> &rsaquo; ';
									echo anchor( 'admin/' . $module . '/' . $method, $label );
									
									if ( isset( $_notifications[$method] ) && $_notifications[$method] ) :
									
										$_type	= isset( $_notifications[$method]['type'] ) ? $_notifications[$method]['type'] : 'info';
										$_title	= isset( $_notifications[$method]['title'] ) ? $_notifications[$method]['title'] : '';
										
										switch ( $_type ) :
										
											case 'split' :
											
												foreach ( $_notifications[$method]['options'] AS $notification ) :
												
													$_split_type 	= isset( $notification['type'] ) ? $notification['type'] : 'info';
													$_split_title	= isset( $notification['title'] ) ? $notification['title'] : '';
													
													if ( $notification['value'] ) :
													
														echo '<span class="indicator split ' . $_split_type .  '" title="' . $_split_title . '" rel="tipsy-right">' . number_format( $notification['value'] ) . '</span>';
														
													endif;
													
												endforeach;
											
											break;
											
											default :
											
												if ( $_notifications[$method]['value'] ) :
												
													echo '<span class="indicator ' . $_type . '" title="' . $_title . '" rel="tipsy-right">' . number_format( $_notifications[$method]['value'] ) . '</span>';
													
												endif;
												
											break;
										
										endswitch;
									
									endif;
									
									// --------------------------------------------------------------------------
									
									//	Add to the mobile menu
									$_mobile_menu[$module]->subs[$method]			= new stdClass();
									$_mobile_menu[$module]->subs[$method]->label	= $label;
									$_mobile_menu[$module]->subs[$method]->url		= 'admin/' . $module . '/' . $method;
									
									echo '<div class="clear"></div></li>';
								
								endif;
								
							endforeach;
							
						?>
						</ul>
					</div>
				</div>
				<?php
				
			endforeach;
			
			// --------------------------------------------------------------------------
			
			//	Build the Dropdown menu
			echo '<div id="mobile-menu-main">';
			echo '<select>';
			echo '<option data-url="">Menu</option>';
			
			$_module	= $this->uri->rsegment( 1 );
			$_method	= $this->uri->rsegment( 2 );
			
			foreach ( $_mobile_menu AS $module => $item ) :
				
				echo '<optgroup label="' . str_replace( '"', '\"', $item->module ) . '">';
				foreach ( $item->subs AS $method => $sub ) :
				
					$_selected = $_module == $module && $_method == $method ? 'selected="selected"' : '';
					echo '<option data-url="' . $sub->url . '" ' . $_selected . '>' . $sub->label . '</option>';
				
				endforeach;
				echo '</optgroup>';
			
			endforeach;
			echo '</select>';
			echo '</div>';
				
		?>
		</div>
	</div>
	
	
	
	<div class="content">
		<div class="padder">
		<div class="content_inner">
		
			<?php
			
				if ( isset( $page->module->name ) && isset( $page->title ) ) :
				
					echo '<h1>';
					echo $page->module->name . ' &rsaquo; ' . $page->title;
					echo '</h1>';
					
				elseif ( ! isset( $page->module->name ) && isset( $page->title ) ) :
				
					echo '<h1>';
					echo $page->title;
					echo '</h1>';
				
				endif;
				
			?>
			
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
			
			<div class="js_error" style="display:none;">
				<p>
					<span class="js_error_head">Hey!</span>
					<span class="js_error_text"></span>
				</p>
			</div>