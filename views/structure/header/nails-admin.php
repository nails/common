<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
	<meta charset="UTF-8" />
	<title>
	<?php
		
		echo lang( 'admin_word_short' ) . ' - ';
		echo isset( $page->module->name ) ? $page->module->name . ' - ' : NULL;
		echo isset( $page->title ) ? $page->title . ' - ' : NULL;
		echo APP_NAME;
				
	?></title>	
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
	
	<!--	JS LOCALISATION	-->
	<script tyle="text/javascript">
		window.NAILS_LANG			= {};
		window.NAILS_LANG.non_html5	= '<?=str_replace( "'", "\'", lang( 'js_error_non_html5' ) )?>';
		window.NAILS_LANG.no_save	= '<?=str_replace( "'", "\'", lang( 'js_error_saving' ) )?>';
	</script>
	
	<!--	ASSETS	-->
	<script type="text/javascript">
		window.NAILS_URL	= '<?=NAILS_URL?>';
		window.SITE_URL		= '<?=site_url()?>';
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
					<?=lang( 'admin_word_long' )?>
				</a>
			</li>
			<li><?=anchor( 'admin', lang( 'admin_home' ) )?></li>
			<?=( isset( $page->module->name ) ) ? '<li>&rsaquo;</li><li>' . $page->module->name . '</li>' : NULL?></li>
			<?=( isset( $page->title ) ) ? '<li>&rsaquo;</li><li>' . $page->title . '</li>' : NULL?></li>
		</ul>
		
		<ul class="right shaded">
			<li>
				<?=anchor( '/', lang( 'admin_switch_frontend' ) )?>
			</li>
			<li style="color:#999;">
				<?=lang( 'admin_loggedin_as', array( site_url( 'admin/accounts/edit/' . active_user( 'id' ) ), active_user( 'first_name' ) ) )?>
				<?php
				
				$_admin_recovery = $this->session->userdata( 'admin_recovery' );
				
				if ( $this->session->userdata( 'admin_recovery' ) ) :
				
					echo lang( 'admin_admin_recover', array( site_url( 'auth/override/login_as/' . $_admin_recovery->id . '/' . $_admin_recovery->hash ), $_admin_recovery->name ) );
				
				endif;
				
				?>
			</li>
			<li class="logout">
				<?=anchor( 'auth/logout', lang( 'action_logout' ) )?>
			</li>
		</ul>
		
		<!--	CLEARFIX	-->
		<div class="clear"></div>
	
	</div>
	
	<div class="sidebar left">
		<div class="padder">
		
		<div class="nav-search">
		<input type="search" placeholder="Type to search menu" />
		</div>
		
		<?php
			
			$_acl			= active_user( 'acl' );
			$_mobile_menu	= array();
			$_counter		= 0;
			
			foreach ( $loaded_modules AS $module => $config ) :
			
				//	Add this to the mobile version of the menu
				$_mobile_menu[$module]			= new stdClass();
				$_mobile_menu[$module]->module	= $config->name;
				$_mobile_menu[$module]->url		= NULL;
				$_mobile_menu[$module]->subs	= array();
				
				// --------------------------------------------------------------------------
				
				//	Get any notifications for this module if applicable
				$_notifications = method_exists( $module, 'notifications') ? $module::notifications() : array();
				
				$_class = '';
				
				if ( $_counter == 0 ) :
				
					$_class = 'first';
				
				endif;
				
				if ( $_counter == ( count( $loaded_modules ) - 1 ) ) :
				
					$_class = 'last';
				
				endif;
				
				$_counter++;
				
				?>
				<div class="box <?=$_class?>" id="box_<?=url_title( $config->name )?>">
					<h2 class="<?=$module?>">
						<?=$config->name?>
						<a href="#" class="toggle">
							<span class="close"><?=lang( 'action_close' )?></span>
							<span class="open"><?=lang( 'action_open' )?></span>
						</a>
					</h2>
					<div class="box-container">
						<ul>
						<?php
						
							//	Loop all the module methods
							foreach( $config->funcs AS $method => $label ) :
							
								//	Is the method enabled?
								if ( get_userobject()->is_superuser() || isset( $_acl['admin'][$module][$method]) ) :
								
									//	Method enabled?
									$_current = ( $this->uri->rsegment( 1 ) == $module && $this->uri->rsegment( 2 ) == $method )  ? 'current' : '';
									
									echo '<li class="' . $_current . '">&rsaquo; ';
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
			echo '<option data-url="" disabled>' . lang( 'admin_nav_menu' ) . '</option>';
			
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
					
				else :
				
					echo '<h1>';
					echo $page->module->name;
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
					<span class="title"><?=lang( 'js_error_header' )?></span>
					<span class="message"></span>
				</p>
			</div>