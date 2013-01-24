<?php

	/**
	 *	THE LOGIN FORM
	 *	
	 *	This view contains only the basic form required for logging a user in. The controller
	 *	will look for an app version of the file  first and load that up. It will fall back
	 *	to the empty Nails view if not available (which includes some basic styling so
	 *	as not to look totally rubbish).
	 *	
	 *	You can completely overload this view by creating a view at:
	 *	
	 *	application/views/auth/login/form
	 *	
	 **/
	
	// --------------------------------------------------------------------------
	
	/**
	 *	LOGIN ERRORS
	 *	
	 *	Only individual field errors are shown, generic erros (such as too many attempted logins)
	 *	should be handled by the containing header files.
	 *	
	 **/
	 
	
	//	Form attributes
	$attr = array(
	
		'id'	=> 'login-form',
		'class'	=> 'nails-default-form'
		
	);
	
	//	If there's a 'return_to' variable set it as a GET variable in case there;'s a form
	//	validation error. Otherwise don't show it - cleaner.
	
	echo ( $return_to ) ? form_open( 'auth/login?return_to=' . $return_to, $attr ) : form_open( 'auth/login', $attr );
	
	// --------------------------------------------------------------------------
	
	//	Write the HTML for the login form
?>
	
	<!--	LOGIN FIELDS	-->
	<?php
		
		$_field	= 'email';
		$_name	= 'Email';
		$_error = form_error( $_field ) ? 'error' : NULL
	
	?>
	<div class="row <?=$_error?>">
		<?=form_label( $_name, $_field, array( 'class' => 'two columns first' ) ); ?>
		<div class="four columns">
			<?=form_input( $_field, set_value( $_field ), 'placeholder="' . $_name . '"' )?>
			<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
		</div>
	</div>
	
	<?php
		
		$_field	= 'password';
		$_name	= 'Password';
		$_error = form_error( $_field ) ? 'error' : NULL
	
	?>
	<div class="row <?=$_error?>">
		<?=form_label( $_name, $_field, array( 'class' => 'two columns first' ) ); ?>
		<div class="four columns">
			<?=form_password( $_field, NULL, 'placeholder="' . $_name . '"' )?>
			<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
		</div>
	</div>
	
	<!--	REMEMBER ME CHECKBOX	-->
	<?php
		
		$_field	= 'remember';
		$_name	= 'Remember Me';
		$_error = form_error( $_field ) ? 'error' : NULL
	
	?>
	<div class="row">
		<label class="two columns first">&nbsp;</label>
		<div class="four columns last">
			<label class="checkbox">
				<?=form_checkbox( $_field, TRUE, TRUE )?>
				<?=$_name?>
			</label>
		</div>
	</div>
	
	
	<div class="row button-row">
		<label class="two columns first">&nbsp;</label>
		<div class="four columns last">
			<?=form_submit( 'submit', 'Log In', 'class="awesome"' )?>
			<small style="margin-left:15px;">
				<?=anchor( 'auth/forgotten_password', 'Forgotten your Password?' )?>
			</small>
		</div>
	</div>
	
<?php
	
	// --------------------------------------------------------------------------
	
	//	Close the form
	echo form_close();