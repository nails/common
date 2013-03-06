<?php

	/**
	 *	THE REGISTRATION FORM
	 *	
	 *	This view contains only the basic form required for registering a user. The controller
	 *	will look for an app version of the file  first and load that up. It will fall back
	 *	to the empty Nails view if not available (which includes some basic styling so
	 *	as not to look totally rubbish).
	 *	
	 *	You can completely overload this view by creating a view at:
	 *	
	 *	application/views/auth/register/form
	 *	
	 **/
	
	// --------------------------------------------------------------------------
	
	/**
	 *	REGISTRATION ERRORS
	 *	
	 *	Only individual field errors are shown, generic erros should be handled by the
	 *	containing header files.
	 *	
	 **/
	 
	
	//	Form attributes
	$attr = array(
	
		'id'	=> 'register-form',
		'class'	=> 'container nails-default-form'
		
	);
	
	echo form_open( 'auth/register', $attr );
	
	// --------------------------------------------------------------------------
	
	//	Write the HTML for the register form
?>
	
	<div class="row">
	
		<!--	FORCE REGISTER CHECK	-->
		<?=form_hidden( 'registerme', TRUE )?>
		
			
		<!--	INPUT FIELDS	-->
		<div class="first seven columns">
		
			<p>
				<?=lang( 'auth_register_message', APP_NAME )?>
			</p>
		
			<?php
				
				$_field			= 'first_name';
				$_name			= lang( 'form_label_first_name' );
				$_placeholder	= lang( 'auth_register_first_name_placeholder' );
				$_error			= form_error( $_field ) ? 'error' : NULL
			
			?>
			<div class="row <?=$_error?>">
				<?=form_label( $_name, 'input-' . $_field, array( 'class' => 'two columns first' ) ); ?>
				<div class="four columns last">
					<?=form_input( $_field, set_value( $_field ), 'id="input-' . $_field . '" placeholder="' . $_placeholder . '"' )?>
					<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
				</div>
			</div>
			
			<?php
				
				$_field			= 'last_name';
				$_name			= lang( 'form_label_last_name' );
				$_placeholder	= lang( 'auth_register_last_name_placeholder' );
				$_error			= form_error( $_field ) ? 'error' : NULL
			
			?>
			<div class="row <?=$_error?>">
				<?=form_label( $_name, 'input-' . $_field, array( 'class' => 'two columns first' ) ); ?>
				<div class="four columns last">
					<?=form_input( $_field, set_value( $_field ), 'id="input-' . $_field . '" placeholder="' . $_placeholder . '"' )?>
					<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
				</div>
			</div>
			
			<?php
				
				$_field			= 'email';
				$_name			= lang( 'form_label_email' );
				$_placeholder	= lang( 'auth_register_email_placeholder' );
				$_error			= form_error( $_field ) ? 'error' : NULL
			
			?>
			<div class="row <?=$_error?>">
				<?=form_label( $_name, 'input-' . $_field, array( 'class' => 'two columns first' ) ); ?>
				<div class="four columns last">
					<?=form_input( $_field, set_value( $_field ), 'id="input-' . $_field . '" placeholder="' . $_placeholder . '"' )?>
					<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
				</div>
			</div>
			
			<?php
				
				$_field			= 'password';
				$_name			= lang( 'form_label_password' );
				$_placeholder	= lang( 'auth_register_password_placeholder' );
				$_error			= form_error( $_field ) ? 'error' : NULL
			
			?>
			<div class="row <?=$_error?>">
				<?=form_label( $_name, 'input-' . $_field, array( 'class' => 'two columns first' ) ); ?>
				<div class="four columns last">
					<?=form_password( $_field, NULL, 'id="input-' . $_field . '" placeholder="' . $_placeholder . '"' )?>
					<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
				</div>
			</div>
			
			<!--	REMEMBER ME CHECKBOX	-->
			<?php
				
				$_field	= 'terms';
				$_name	= lang ( 'auth_register_label_accept_tc', site_url( 'legal/terms' ) );
				$_error = form_error( $_field ) ? 'error' : NULL
			
			?>
			<div class="row <?=$_error?>">
				<label class="two columns first">&nbsp;</label>
				<div class="four columns last">
					<label class="checkbox">
						<?=form_checkbox( $_field, TRUE, FALSE )?>
						<?=$_name?>
						<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
					</label>
				</div>
			</div>
			
			<!--	SUBMIT BUTTON	-->
			<div class="row button-row">
				<label class="two columns first">&nbsp;</label>
				<div class="four columns last">
					<?=form_submit( 'submit', lang( 'action_register' ), 'class="awesome"' )?>
				</div>
			</div>
		
		</div>
		
		<!--	SOCIAL NETWORK BUTTONS	-->
		<?php
		
			if ( module_is_enabled( 'auth[facebook]' ) || module_is_enabled( 'auth[facebook]' ) || module_is_enabled( 'auth[facebook]' ) ) :
			
				echo '<div class="eight columns last offset-by-one">';
				echo '<p style="text-align:center;">' . lang( 'auth_register_social_message' ) . '</p>';
				
				// --------------------------------------------------------------------------
				
				//	FACEBOOK
				if ( module_is_enabled( 'auth[facebook]' ) ) :
				
					echo '<p style="text-align:center;">' . anchor( 'auth/fb/connect', lang( 'auth_register_social_signin', 'Facebook' ), 'class="social-signin fb"' ) . '</p>';
				
				endif;
				
				//	TWITTER
				if ( module_is_enabled( 'auth[twitter]' ) ) :
				
					echo '<p style="text-align:center;">' . anchor( 'auth/tw/connect', lang( 'auth_register_social_signin', 'Twitter' ), 'class="social-signin tw"' ) . '</p>';
				
				endif;
				
				//	LINKEDIN
				if ( module_is_enabled( 'auth[linkedin]' ) ) :
				
					echo '<p style="text-align:center;">' . anchor( 'auth/li/connect', lang( 'auth_register_social_signin', 'LinkedIn' ), 'class="social-signin li"' ) . '</p>';
				
				endif;
				
				echo '</div>';
			
			endif;
			
		?>
	
	</div>
	
<?php
	
	// --------------------------------------------------------------------------
	
	//	Close the form
	echo form_close();