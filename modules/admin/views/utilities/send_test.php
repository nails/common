<div class="group-utilities send-test">

	<p><?=lang( 'utilities_test_email_intro' )?></p>
	
	<hr />
	
	<?=form_open()?>
	
		<fieldset>

			<legend><?=lang( 'utilities_test_email_field_legend' )?></legend>
			
			<?php
			
				//	Recipient
				$_field					= array();
				$_field['key']			= 'recipient';
				$_field['label']		= lang( 'utilities_test_email_field_name' );
				$_field['default']		= set_value( $_field['key'] );
				$_field['required']		= TRUE;
				$_field['placeholder']	= lang( 'utilities_test_email_field_placeholder' );
				
				echo form_field( $_field );
			
			?>
			
			<!--	CLEARFIX	-->
			<div class="clear"></div>
			
		</fieldset>
		
	<?php
	
		echo '<p>' . form_submit( 'submit', lang( 'utilities_test_email_submit' ) ) . '</p>';
		echo form_close();
		
	?>
</div>