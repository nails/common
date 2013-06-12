<div class="group-testimonials edit">
	<?=form_open()?>
	<fieldset id="edit-testimonials-meta">
		<legend><?=lang( 'testimonials_edit_legend' )?></legend>
		<?php


			$_field				= array();
			$_field['key']		= 'quote';
			$_field['label']	= lang( 'testimonials_edit_field_quote' );
			$_field['type']		= 'textarea';
			$_field['required']	= TRUE;
			$_field['default']	= $testimonial->quote;

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			$_field				= array();
			$_field['key']		= 'quote_by';
			$_field['label']	= lang( 'testimonials_edit_field_quote_by' );
			$_field['required']	= TRUE;
			$_field['default']	= $testimonial->quote_by;

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			$_field				= array();
			$_field['key']		= 'order';
			$_field['label']	= lang( 'testimonials_edit_field_order' );
			$_field['default']	= $testimonial->order;

			echo form_field( $_field );

		?>
	</fieldset>
	<p>
		<?=form_submit( 'submit', lang( 'action_save_changes' ) );?>
	</p>
	<?=form_close();?>
</div>