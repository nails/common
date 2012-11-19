<!--	PAGE TITLE	-->
<section>
	<h1>Send Test Email</h1>	
</section>

<p>
	Use this form to send a test email, useful for testing that emails being sent are received by the end user.
</p>

<hr />

<?=form_open()?>

	<div class="fieldset">
	
		<div class="legend">Recipient</div>
		
		<?php
		
			$_field					= array();
			$_field['key']			= 'recipient';
			$_field['label']		= 'Recipient';
			$_field['default']		= set_value( $_field['key'] );
			$_field['sub_label']	= '';
			$_field['required']		= TRUE;
			$_field['placeholder']	= 'Type recipient\'s email address';
		
		?>
		<div class="field <?=form_error( $_field['key'] ) ? 'error' : ''?>">
			<label>
			<?php
				
				//	Label
				echo '<span class="label">';
				echo $_field['label'];
				echo $_field['required'] ? '*' : '';
				echo $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';
				echo '</span>';
				
				//	Input
				echo form_input( $_field['key'], set_value( $_field['key'], $_field['default'] ), 'placeholder="' . $_field['placeholder'] . '"' );
				
				//	Error
				echo form_error( $_field['key'], '<span class="error">', '</span>' );
			?>
			</label>
			<div class="clear"></div>
		</div>
		
		<!--	CLEARFIX	-->
		<div class="clear"></div>
		
	</div>
	
	<hr />
	
<?php

	echo form_submit( 'submit', 'Send Test Email' );
	echo form_close();
	
?>