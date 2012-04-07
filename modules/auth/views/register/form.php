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
		'class'	=>	'well form-horizontal'
		
	);
	
	echo form_open( 'auth/register', $attr );
	
	// --------------------------------------------------------------------------
	
	//	Write the HTML for the register form
?>
	
	
	<!--	FORCE REGISTER CHECK	-->
	<?=form_hidden( 'registerme', TRUE )?>
	
		
	<!--	INPUT FIELDS	-->
	<?php
		
		$_field	= 'first_name';
		$_name	= 'First Name';
		$_error = form_error( $_field ) ? 'error' : NULL
	
	?>
	<div class="control-group <?=$_error?>">
		<?=form_label( $_name, $_field, array( 'class' => 'control-label' ) ); ?>
		<div class="controls">
			<?=form_input( $_field, set_value( $_field ), 'placeholder="' . $_name . '"' )?>
			<?=form_error( $_field, '<span class="help-inline">', '</span>' )?>
		</div>
	</div>
	
	<?php
		
		$_field	= 'last_name';
		$_name	= 'Last Name';
		$_error = form_error( $_field ) ? 'error' : NULL
	
	?>
	<div class="control-group <?=$_error?>">
		<?=form_label( $_name, $_field, array( 'class' => 'control-label' ) ); ?>
		<div class="controls">
			<?=form_input( $_field, set_value( $_field ), 'placeholder="' . $_name . '"' )?>
			<?=form_error( $_field, '<span class="help-inline">', '</span>' )?>
		</div>
	</div>
	
	<?php
		
		$_field	= 'email';
		$_name	= 'Email';
		$_error = form_error( $_field ) ? 'error' : NULL
	
	?>
	<div class="control-group <?=$_error?>">
		<?=form_label( $_name, $_field, array( 'class' => 'control-label' ) ); ?>
		<div class="controls">
			<?=form_input( $_field, set_value( $_field ), 'placeholder="' . $_name . '"' )?>
			<?=form_error( $_field, '<span class="help-inline">', '</span>' )?>
		</div>
	</div>
	
	<?php
		
		$_field	= 'password';
		$_name	= 'Password';
		$_error = form_error( $_field ) ? 'error' : NULL
	
	?>
	<div class="control-group <?=$_error?>">
		<?=form_label( $_name, $_field, array( 'class' => 'control-label' ) ); ?>
		<div class="controls">
			<?=form_password( $_field, NULL, 'placeholder="' . $_name . '"' )?>
			<?=form_error( $_field, '<span class="help-inline">', '</span>' )?>
		</div>
	</div>
	
	<!--	REMEMBER ME CHECKBOX	-->
	<?php
		
		$_field	= 'terms';
		$_name	= 'I accept the T&C\'s';
		$_error = form_error( $_field ) ? 'error' : NULL
	
	?>
	<div class="control-group <?=$_error?>">
		<label class="control-label">&nbsp;</label>
		<div class="controls">
			<label class="checkbox">
				<?=form_checkbox( $_field, TRUE, FALSE )?>
				<?=$_name?>
				<?=form_error( $_field, '<span class="help-inline">', '</span>' )?>
			</label>
		</div>
	</div>
	
	<!--	SUBMIT BUTTON	-->
	<hr />
	
	<div class="control-group">
		<label class="control-label">&nbsp;</label>
		<div class="controls">
			<?=form_submit( 'submit', 'Register', 'class="btn btn-primary"' )?>
		</div>
	</div>
	
	
<?php
	
	// --------------------------------------------------------------------------
	
	//	Close the form
	echo form_close();