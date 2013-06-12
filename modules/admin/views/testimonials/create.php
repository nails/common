<div class="group-testimonials create">
	<?=form_open()?>
	<fieldset id="create-testimonials-meta">
		<legend><?=lang( 'testimonials_create_legend' )?></legend>
		<?php


			$_field				= array();
			$_field['key']		= 'quote';
			$_field['label']	= lang( 'testimonials_create_field_quote' );
			$_field['type']		= 'textarea';
			$_field['required']	= TRUE;

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			$_field				= array();
			$_field['key']		= 'quote_by';
			$_field['label']	= lang( 'testimonials_create_field_quote_by' );
			$_field['required']	= TRUE;

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			$_field				= array();
			$_field['key']		= 'order';
			$_field['label']	= lang( 'testimonials_create_field_order' );

			echo form_field( $_field );

		?>
	</fieldset>
	<p>
		<?=form_submit( 'submit', lang( 'action_create' ) );?>
	</p>
	<?=form_close();?>
</div>