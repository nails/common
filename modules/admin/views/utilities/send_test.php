<h1>Send Test Email</h1>	
<p>
	Use this form to send a test email, useful for testing that emails being sent are received by the end user.
</p>

<hr />

<?=form_open()?>

	<div class="fieldset">
	
		<div class="legend">Recipient</div>
		
		<?php
		
			//	Recipient
			$_field					= array();
			$_field['key']			= 'recipient';
			$_field['label']		= 'Recipient';
			$_field['default']		= set_value( $_field['key'] );
			$_field['required']		= TRUE;
			$_field['placeholder']	= 'Type recipient\'s email address';
			
			echo form_field( $_field );
		
		?>
		
		<!--	CLEARFIX	-->
		<div class="clear"></div>
		
	</div>
	
<?php

	echo '<p>' . form_submit( 'submit', 'Send Test Email' ) . '</p>';
	echo form_close();
	
?>