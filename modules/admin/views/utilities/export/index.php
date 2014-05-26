<div class="group-utilities export">

	<p>
		<?=lang( 'utilities_export_intro' )?>
	</p>
	<p class="system-alert message no-close">
		<?=lang( 'utilities_export_warn' )?>
	</p>

	<?=form_open()?>
	<fieldset>
		<legend><?=lang( 'utilities_export_legend_source' )?></legend>
		<?php

			//	Display Name
			$_field					= array();
			$_field['key']			= 'source';
			$_field['label']		= lang( 'utilities_export_field_source' );
			$_field['required']		= TRUE;
			$_field['class']		= 'select2';

			$_options = array();
			foreach ( $sources AS $key => $source ) :

				$_options[$key] = $source[0] . ' - ' . $source[1];

			endforeach;

			echo form_field_dropdown( $_field, $_options );

		?>
	</fieldset>

	<fieldset>
		<legend><?=lang( 'utilities_export_legend_format' )?></legend>
		<?php

			//	Display Name
			$_field					= array();
			$_field['key']			= 'format';
			$_field['label']		= lang( 'utilities_export_field_format' );
			$_field['required']		= TRUE;
			$_field['class']		= 'select2';

			$_options = array();
			foreach ( $formats AS $key => $format ) :

				$_options[$key] = $format[0] . ' - ' . $format[1];

			endforeach;

			echo form_field_dropdown( $_field, $_options );

		?>
	</fieldset>

	<p>
		<?=form_submit( 'submit', 'Export' )?>
	</p>
	<?=form_close()?>
</div>