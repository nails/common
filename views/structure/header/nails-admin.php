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

	<!--	NAILS JS GLOBALS	-->
	<script style="text/javascript">
		window.ENVIRONMENT		= '<?=ENVIRONMENT?>';
		window.SITE_URL			= '<?=site_url( '', page_is_secure() )?>';
		window.NAILS			= {};
		window.NAILS.URL		= '<?=NAILS_ASSETS_URL?>';
		window.NAILS.LANG		= {};
		window.NAILS.USER		= {};
		window.NAILS.USER.ID	= <?=active_user( 'id' )?>;
		window.NAILS.USER.FNAME	= '<?=active_user( 'first_name' )?>';
		window.NAILS.USER.LNAME	= '<?=active_user( 'last_name' )?>';
		window.NAILS.USER.EMAIL	= '<?=active_user( 'email' )?>';
	</script>

	<noscript>
		<style type="text/css">

			.js-only
			{
				display:none;
			}

		</style>
	</noscript>

	<!--	JS LOCALISATION	-->
	<script style="text/javascript">
		window.NAILS.LANG.non_html5	= '<?=str_replace( "'", "\'", lang( 'js_error_non_html5' ) )?>';
		window.NAILS.LANG.no_save	= '<?=str_replace( "'", "\'", lang( 'js_error_saving' ) )?>';
	</script>

	<!--	ASSETS	-->
	<?php

		echo $this->asset->output( 'CSS' );
		echo $this->asset->output( 'CSS-INLINE' );
		echo $this->asset->output( 'JS' );

	?>
	<link rel="stylesheet" type="text/css" media="print" href="<?=NAILS_ASSETS_URL . 'css/nails.admin.print.css'?>" />

</head>

<body class="<?=empty( $loaded_modules ) ? 'no-modules' : ''?>">

	<div class="header">

		<ul class="left">

			<li style="display:block;margin-bottom:4px;">
				<a href="<?=site_url( 'admin' )?>" style="font-size:18px;font-weight:bold;color:#fff;">
					<span class="app-name"><?=APP_NAME?></span>
					<?=lang( 'admin_word_long' )?>
				</a>
			</li>
			<li><?=anchor( 'admin', lang( 'admin_home' ) )?></li>
			<?=! empty( $page->module->name ) ? '<li>&rsaquo;</li><li>' . $page->module->name : NULL?></li>
			<?=! empty( $page->title ) ? '<li>&rsaquo;</li><li>' . $page->title : NULL?></li>
		</ul>

		<ul class="right shaded">
			<li>
				<?=anchor( '', lang( 'admin_switch_frontend' ) )?>
			</li>
			<li style="color:#999;">
				<?php

				//	Logged in as
				$_link = anchor( 'admin/accounts/edit/' . active_user( 'id' ), active_user( 'first_name' ), 'class="fancybox" data-fancybox-type="iframe"' );
				echo lang( 'admin_loggedin_as', $_link );

				if ( active_user( 'profile_img' ) ) :

					echo img( array( 'src' => cdn_thumb( active_user( 'profile_img' ), 16, 16 ), 'class' => 'avatar' ) );

				endif;

				// --------------------------------------------------------------------------

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

	<div class="sidebar">
		<div class="padder">

			<div class="nav-search">
				<input type="search" placeholder="Type to search menu" />
			</div>

		<ul class="modules">
		<?php

			$_acl			= active_user( 'acl' );
			$_mobile_menu	= array();
			$_counter		= 0;
			$loaded_modules	= ! empty( $loaded_modules ) ? $loaded_modules : array();

			foreach ( $loaded_modules AS $module => $config ) :

				//	Get any notifications for this module if applicable
				$_notifications = $module::notifications();

				$_class = '';

				if ( $_counter == 0 ) :

					$_class = 'first';

				endif;

				if ( $_counter == ( count( $loaded_modules ) - 1 ) ) :

					$_class = 'last';

				endif;

				$_counter++;

				// --------------------------------------------------------------------------

				//	Loop all the module methods and prepare an array, we do this so that we
				//	can make sure there'll be some output before we render the box header (i.e
				//	if a user only has access to an unlisted method they won't have an options
				//	here - e.g edit member - themselves - but not view members).


				$_options = array();

				foreach( $config->funcs AS $method => $label ) :

					$_temp						= new stdClass();
					$_temp->is_active			= FALSE;
					$_temp->label				= $label;
					$_temp->method				= $method;
					$_temp->url					= 'admin/' . $module . '/' . $method;
					$_temp->notification		= new stdClass();
					$_temp->notification->type	= '';
					$_temp->notification->title	= '';
					$_temp->notification->value	= '';

					//	Is the method enabled?
					if ( get_userobject()->is_superuser() || isset( $_acl['admin'][$module][$method] ) ) :

						//	Method enabled?
						$_temp->is_active = $this->uri->rsegment( 1 ) == $module && $this->uri->rsegment( 2 ) == $method ? 'current' : '';

						//	Notifications for this method?
						if ( ! empty( $_notifications[$method] ) ) :

							$_temp->notification->type		= isset( $_notifications[$method]['type'] ) ? $_notifications[$method]['type'] : 'neutral';
							$_temp->notification->title		= isset( $_notifications[$method]['title'] ) ? $_notifications[$method]['title'] : '';
							$_temp->notification->value		= isset( $_notifications[$method]['value'] ) ? $_notifications[$method]['value'] : '';
							$_temp->notification->options	= isset( $_notifications[$method]['options'] ) ? $_notifications[$method]['options'] : '';

						endif;

						// --------------------------------------------------------------------------

						//	Add to main $_options array
						$_options[] = $_temp;

					endif;

				endforeach;

				// --------------------------------------------------------------------------

				//	Render the options (if there are any)
				if ( $_options ) :

					//	Add this to the mobile version of the menu
					$_mobile_menu[$module]			= new stdClass();
					$_mobile_menu[$module]->module	= $config->name;
					$_mobile_menu[$module]->url		= NULL;
					$_mobile_menu[$module]->subs	= array();

					// --------------------------------------------------------------------------

					//	Dashboard is not sortable
					$_sortable = $module == 'dashboard' ? 'no-sort' : '';

					// --------------------------------------------------------------------------

					//	Initial open/close state?
					$_user_nav_pref = @unserialize( active_user( 'admin_nav' ) );

					if ( $_user_nav_pref ) :

						if ( empty( $_user_nav_pref->{$module}->open ) ) :

							$_state = 'closed';

						else :

							$_state = 'open';

						endif;

					else :

						//	Closed by default
						$_state = 'closed';

					endif;


					?>
					<li class="module <?=$_sortable?>" data-module="<?=$module?>" data-initial-state="<?=$_state?>">
						<div class="box <?=$_class?>" id="box_<?=url_title( $config->name )?>">
							<h2 class="<?=$module?>">
								<?=$_sortable !== 'no-sort' ? '<span class="handle ion-drag"></span>' : '';?>
								<?=$config->name?>
								<a href="#" class="toggle">
									<span class="close"><?=lang( 'action_close' )?></span>
									<span class="open"><?=lang( 'action_open' )?></span>
								</a>
							</h2>
							<div class="box-container">
								<ul>
								<?php

									foreach( $_options AS $option ) :

										//	Add to the mobile menu
										$_mobile_menu[$module]->subs[$option->method]			= new stdClass();
										$_mobile_menu[$module]->subs[$option->method]->label	= $option->label;
										$_mobile_menu[$module]->subs[$option->method]->url		= $option->url;


										//	Render
										echo '<li class="' . $option->is_active . '">';

											//	Link
											echo anchor( $option->url, $option->label );

											//	Notification
											switch ( $option->notification->type ) :

												case 'split' :

													$_mobile_notification	= array();

													foreach ( $option->notification->options AS $notification ) :

														$_split_type 	= isset( $notification['type'] ) ? $notification['type'] : 'neutral';
														$_split_title	= isset( $notification['title'] ) ? $notification['title'] : '';

														if ( $notification['value'] ) :

															echo '<span class="indicator split ' . $_split_type .  '" title="' . $_split_title . '" rel="tipsy-right">' . number_format( $notification['value'] ) . '</span>';

															//	Update mobile menu
															if ( $_split_title ) :

																$_mobile_notification[] = $_split_title . ': ' . number_format( $notification['value'] );

															else :

																$_mobile_notification[] = number_format( $notification['value'] );

															endif;

														endif;

													endforeach;

													$_mobile_menu[$module]->subs[$option->method]->label .= ' (' . implode( ', ', $_mobile_notification ) . ')';

												break;

												default :

													if ( $option->notification->value ) :

														echo '<span class="indicator ' . $option->notification->type . '" title="' . $option->notification->title . '" rel="tipsy-right">' . number_format( $option->notification->value ) . '</span>';

														if ( $option->notification->title ) :

															$_mobile_menu[$module]->subs[$option->method]->label .= ' (' . $option->notification->title . ': ' . number_format( $option->notification->value ) . ')';

														else :

															$_mobile_menu[$module]->subs[$option->method]->label .= ' (' . number_format( $option->notification->value ) . ')';

														endif;

													endif;

												break;

											endswitch;

											echo '<div class="clear"></div>';

										echo '</li>';

									endforeach;

								?>
								</ul>
							</div>
						</div>
					</li>
					<?php

				endif;

			endforeach;

		?>
		</ul>
		<?php

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

			<?php if ( ! empty( $error ) ) : ?>
			<div class="system-alert error">
				<p><?=$error?></p>
			</div>
			<?php endif; ?>

			<?php if ( ! empty( $success ) ) : ?>
			<div class="system-alert success">
				<p><?=$success?></p>
			</div>
			<?php endif; ?>

			<?php if ( ! empty( $message ) ) : ?>
			<div class="system-alert message">
				<p><?=$message?></p>
			</div>
			<?php endif; ?>

			<?php if ( ! empty( $notice ) ) : ?>
			<div class="system-alert notice">
				<p><?=$notice?></p>
			</div>
			<?php endif; ?>

			<div class="js_error" style="display:none;">
				<p>
					<span class="title"><?=lang( 'js_error_header' )?></span>
					<span class="message"></span>
				</p>
			</div>