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
	
	if ( $return_to || $return_to_fail ) :
	
		$_returns = '?';
		$_returns .= $return_to ? 'return_to=' . url_encode( $return_to ) : '';
		$_returns .= $return_to_fail ? 'return_to_fail=' . url_encode( $return_to_fail ) : '';
	
	else :
	
		$_returns = '';
	
	endif;
	
	echo form_open( 'auth/tw/connect/verify' . $_returns, $attr );
	
	// --------------------------------------------------------------------------
	
	//	Write the HTML for the register form
?>
	<p>
		In order to complete setting up your account we need a little more information from you.
	</p>
	
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
	
		if ( ! $first_name || ! $last_name ) :
		
			$_field	= 'first_name';
			$_name	= 'First Name';
			$_error = form_error( $_field ) ? 'error' : NULL
		
			?>
			<div class="row <?=$_error?>">
				<?=form_label( $_name, $_field, array( 'class' => 'two columns first' ) ); ?>
				<div class="four columns">
					<?=form_input( $_field, set_value( $_field, $first_name ), 'placeholder="' . $_name . '"' )?>
					<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
				</div>
			</div>
			<?php
			
			// --------------------------------------------------------------------------
			
			$_field	= 'last_name';
			$_name	= 'Surname';
			$_error = form_error( $_field ) ? 'error' : NULL
		
			?>
			<div class="row <?=$_error?>">
				<?=form_label( $_name, $_field, array( 'class' => 'two columns first' ) ); ?>
				<div class="four columns">
					<?=form_input( $_field, set_value( $_field, $last_name ), 'placeholder="' . $_name . '"' )?>
					<?=form_error( $_field, '<div class="system-alert error no-close">', '</div>' )?>
				</div>
			</div>
			<?php
	
		endif;
		
	?>
	
	<div class="row button-row">
		<label class="two columns first">&nbsp;</label>
		<div class="four columns last">
			<?=form_submit( 'submit', 'Continue', 'class="awesome"' )?>
		</div>
	</div>

<?php
	
	// --------------------------------------------------------------------------
	
	//	Close the form
	echo form_close();